<?php
require_once DIR_SYSTEM.'library/opay/opay_8.1.gateway.inc.php';

class ControllerPaymentOpay extends Controller {

    protected function index() {

        $this->opay = new OpayGateway;

        $this->language->load('payment/opay');

        $this->data['action']         = HTTPS_SERVER . 'index.php?route=payment/opay/confirm';
        $this->data['button_confirm'] = $this->language->get('button_confirm');

        $this->data['show_channels'] = $this->config->get('opay_show_channels');

        $this->data['channels'] = $this->_getChannelsList();

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/opay.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/payment/opay.tpl';
        } else {
            $this->template = 'default/template/payment/opay.tpl';
        }

        $this->render();
    }

    public function confirm() {

        $this->opay = new OpayGateway;

        $this->load->model('checkout/order');
        $order = $this->model_checkout_order->getOrder($this->session->data['order_id']);

        try {

            // Set Opay signing type
            $this->_setOpaySignatureType();

            // Get Opay parameters
            $paramsArray = $this->_getParamsArray();
            $paramsArray = $this->opay->signArrayOfParameters($paramsArray);

            // Generate autosubmit form
            $this->data['html'] = $this->opay->generatetAutoSubmitForm('https://gateway.opay.lt/pay/', $paramsArray);

            // Add order status
            $opayNewOrderId = $this->config->get('opay_new_order_id');
            $this->model_checkout_order->confirm($order['order_id'], $opayNewOrderId);

            // Add chosen channel as a comment for order history
            if (isset($_POST['channel'])) {
                $this->language->load('payment/opay');
                $comment = $this->language->get('text_clicked_channel') . $_POST['channel'];
                $this->model_checkout_order->update($order['order_id'], $opayNewOrderId, $comment, FALSE);
            }

            $this->template = 'default/template/payment/opay_redirect.tpl';
            $this->response->setOutput($this->render(TRUE));

        } catch (OpayGatewayException $e) {
            $this->log->write('Opay error: '. $e->getMessage() );
            exit( get_class($e).': '.$e->getMessage() );
        }
    }

    public function accept() {
        // Possible returned status = 1 or 2

        $statusReturned = $this->callback(TRUE);

        if ($statusReturned == 1) {
            if (isset($this->session->data['token'])) {
                $this->redirect(HTTPS_SERVER . 'index.php?route=checkout/success&token=' . $this->session->data['token']);
            } else {
                $this->redirect(HTTPS_SERVER . 'index.php?route=checkout/success');
            }
        } elseif ($statusReturned == 2) {
            if (isset($this->session->data['token'])) {
                $this->redirect(HTTPS_SERVER . 'index.php?route=payment/opay/processing&token=' . $this->session->data['token']);
            } else {
                $this->redirect(HTTPS_SERVER . 'index.php?route=payment/opay/processing');
            }
        }
        
    }

    public function processing()
    {
        $this->language->load('payment/opay');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->data['heading_processing'] = $this->language->get('heading_processing');
        $this->data['text_payment_processing'] = $this->language->get('text_payment_processing');

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/opay_processing.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/payment/opay_processing.tpl';
        } else {
            $this->template = 'default/template/payment/opay_processing.tpl';

        }

        $this->children = array(
            'common/column_left',
            'common/column_right',
            'common/content_top',
            'common/content_bottom',
            'common/footer',
            'common/header'
        );

        $this->response->setOutput($this->render(true));
    }

    public function callback($returnStatusOnly = false) {
        
        $this->opay = new OpayGateway;

        $this->load->model('checkout/order');
        $this->language->load('payment/opay');

            
        $opayPendingOrderId = $this->config->get('opay_new_order_id');
        $opayFinishedOrderId = $this->config->get('opay_finished_order_id');
        $opayCanceledOrderId = $this->config->get('opay_canceled_order_id');

        try {

            // Set Opay signing type
            $this->_setOpaySignatureType();

            if (isset($_POST['encoded']))
            {
                $response = $this->opay->convertEncodedStringToArrayOfParameters($_POST['encoded']);

                if ($this->opay->verifySignature($response))
                {
                    $message = '';
                    if($response['status'] == 1) {

                        $orderId = isset($response['order_nr']) ? $response['order_nr'] : null;
                        $order = $this->model_checkout_order->getOrder($orderId);

                        if (empty($order)) {
                            throw new OpayGatewayException('Order with ID "'.$orderId.'" was not found.');
                        }

                        if ($order['currency_code'] != $response['p_currency']) {
                            throw new OpayGatewayException('Wrong currency. Expected '.$order['currency_code'].', but got '.$response['p_currency'].'. Order ID: '.$orderId);
                        }

                        // Get expected total amount in default currency
                        $orderTotal = intval(number_format($order['total'] * $order['currency_value'], 2, '', ''));

                        if ($response['p_amount'] < $orderTotal) {
                            throw new OpayGatewayException('Bad amount: ' . $response['p_amount'] . ', expected: ' . $orderTotal . '. Order ID: '.$orderId);
                        }

                        // Change status if the payment was not registered
                        if ($order['order_status_id'] == $opayPendingOrderId OR $order['order_status_id'] == $opayCanceledOrderId) {
                            $message = $this->language->get('text_paid_by') . ' '. ucfirst($response['p_bank']);
                            $this->model_checkout_order->update($response['order_nr'], $opayFinishedOrderId, $message, FALSE);
                        }


                        if ($returnStatusOnly) {
                            return 1;
                        }
                        exit('OK');
                    } 
                    elseif($response['status'] == 2)
                    {
                        if ($returnStatusOnly) {
                            return 2;
                        }
                        exit('OK');
                    }
                    elseif($response['status'] == 0) 
                    {
                        $message = '';
                        $this->model_checkout_order->update($response['order_nr'], $opayCanceledOrderId, $message, FALSE);
                        exit('OK');
                    }

                }
                else
                {
                    throw new OpayGatewayException('Wrong signature.');
                }    
            }
            else
            {
                throw new OpayGatewayException('No direct access.');
            }
        } catch (OpayGatewayException $e) {
            $this->log->write('Opay error: '. $e->getMessage() );
            exit('OK');
        }
    }

    private function _setOpaySignatureType()
    {
        $opayPassowrd = $this->config->get('opay_password_sign');
        $merchantPrivateKey = $this->config->get('opay_rsa_signature');
        $opayCertificate = $this->config->get('opay_certificate');

        if (!empty($opayCertificate) and !empty($merchantPrivateKey)) {
            $this->opay->setMerchantRsaPrivateKey($merchantPrivateKey);
            $this->opay->setOpayCertificate($opayCertificate);
        } else {
            $this->opay->setSignaturePassword($opayPassowrd);
        }
    }

    private function _getParamsArray()
    {
        $this->load->model('checkout/order');
        $order = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        $defaultCurrency = $this->config->get('config_currency');

        $orderTotal = intval(number_format($order['total'] * $this->currency->getvalue($defaultCurrency), 2, '', ''));

        $paramsArray = array(
            'website_id'                => $this->config->get('opay_website_id'),
            'order_nr'                  => $order['order_id'],
            'redirect_url'              => HTTPS_SERVER.'index.php?route=payment/opay/accept',
            'redirect_on_success'       => 0,
            'web_service_url'           => HTTPS_SERVER.'index.php?route=payment/opay/callback',
            'back_url'                  => '',
            'standard'                  => 'opay_8.1',
            'language'                  => $this->language->get('code'),
            'amount'                    => $orderTotal,
            'currency'                  => $defaultCurrency,
            'country'                   => 'LT',
            'payment_description'       => 'Apmokėjimas pagal {merchant} užsakymo nr. {order_nr}',
            'test'                      => ($this->config->get('opay_test_mode')) ? $this->config->get('opay_user_id') : '',
            'c_email'                   => $order['email'],
            'c_mobile_nr'               => $order['telephone'],
            'pass_through_channel_name' => (isset($_REQUEST['channel'])) ? $_REQUEST['channel'] : '',
        );

        return $paramsArray;
    }

    private function _getChannelsList() {

        $paramsArray = $this->_getParamsArray();

        // Set Opay signing type
        $this->_setOpaySignatureType();

        $paramsArray = $this->opay->signArrayOfParameters($paramsArray);
        $channelsArray = $this->opay->webServiceRequest('https://gateway.opay.lt/api/listchannels/', $paramsArray);

        if (isset($channelsArray['response']['result'])) {
            return $channelsArray['response']['result'];
        }
        return false;
    }

}
