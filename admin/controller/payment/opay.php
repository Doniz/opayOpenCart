<?php
require_once DIR_SYSTEM.'library/opay/opay_8.1.gateway.inc.php';

class ControllerPaymentOpay extends Controller {
	private $error = array();

	public function index() {
		$this->language->load('payment/opay');
		$this->load->model('setting/setting');

		$this->document->setTitle($this->language->get('heading_title'));

		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			
		    if($this->validate()) {
		        $this->load->model('setting/setting');
                $this->model_setting_setting->editSetting('opay', $this->request->post);
                $this->session->data['success'] = $this->language->get('text_success');
                $this->redirect(HTTPS_SERVER . 'index.php?route=extension/payment&token=' . $this->session->data['token']);
		    }
		}

		$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['text_yes'] = $this->language->get('text_yes');
		$this->data['text_no'] = $this->language->get('text_no');
		$this->data['text_all_zones'] = $this->language->get('text_all_zones');
		$this->data['text_enabled'] = $this->language->get('text_enabled');
		$this->data['text_disabled'] = $this->language->get('text_disabled');
		
		$this->data['entry_website'] = $this->language->get('entry_website');
		$this->data['entry_status'] = $this->language->get('entry_status');
		$this->data['entry_finished_order_status'] = $this->language->get('entry_finished_order_status');
		$this->data['entry_new_order_status'] = $this->language->get('entry_new_order_status');
		$this->data['entry_canceled_order_status'] = $this->language->get('entry_canceled_order_status');
		$this->data['entry_user_id'] = $this->language->get('entry_user_id');
		$this->data['entry_test_mode'] = $this->language->get('entry_test_mode');
		$this->data['entry_geo_zone'] = $this->language->get('entry_geo_zone');
		$this->data['entry_password_signature'] = $this->language->get('entry_password_signature');
		$this->data['entry_rsa_signature'] = $this->language->get('entry_rsa_signature');
		$this->data['entry_certificate'] = $this->language->get('entry_certificate');
		$this->data['entry_show_channels'] = $this->language->get('entry_show_channels');
		$this->data['entry_sort_order'] = $this->language->get('entry_sort_order');

		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_cancel'] = $this->language->get('button_cancel');

		$this->data['error_warning'] = ( isset($this->error['warning']) ) ? $this->error['warning'] : '';
		$this->data['error_opay_website_id'] = ( isset($this->error['opay_website_id']) ) ? $this->error['opay_website_id'] : '';
		$this->data['error_opay_password_sign'] = ( isset($this->error['opay_password_sign']) ) ? $this->error['opay_password_sign'] : '';
		$this->data['error_opay_certificate'] = ( isset($this->error['opay_certificate']) ) ? $this->error['opay_certificate'] : '';
		$this->data['error_opay_rsa_signature'] = ( isset($this->error['opay_rsa_signature']) ) ? $this->error['opay_rsa_signature'] : '';
		$this->data['error_opay_user_id'] = ( isset($this->error['opay_user_id']) ) ? $this->error['opay_user_id'] : '';

  		$this->data['breadcrumbs'] = array();

  		$this->data['breadcrumbs'][] = array(
  			'text'      => $this->language->get('text_home'),
  			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
  			'separator' => false
  		);

  		$this->data['breadcrumbs'][] = array(
  			'text'      => $this->language->get('text_payment'),
  			'href'      => $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'),      		
  			'separator' => ' :: '
  		);

  		$this->data['breadcrumbs'][] = array(
  			'text'      => $this->language->get('heading_title'),
  			'href'      => $this->url->link('payment/opay', 'token=' . $this->session->data['token'], 'SSL'),
  			'separator' => ' :: '
  		);

       $this->data['action'] = $this->url->link('payment/opay', 'token=' . $this->session->data['token'], 'SSL');
       $this->data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');

		$this->load->model('localisation/order_status');
		$this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		$this->load->model('localisation/geo_zone');
		$this->data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

		$newOrderIdDefault = ($this->config->get('opay_new_order_id')) ? $this->config->get('opay_new_order_id') : 1;
		$canceledOrderIdDefault = ($this->config->get('opay_canceled_order_id')) ? $this->config->get('opay_canceled_order_id') : 7;
		$finishedOrderIdDefault = ($this->config->get('opay_finished_order_id')) ? $this->config->get('opay_finished_order_id') : 15;

		$this->data['opay_status'] = (isset($this->request->post['opay_status'])) ? $this->request->post['opay_status'] : $this->config->get('opay_status');
		$this->data['opay_website_id'] = (isset($this->request->post['opay_website_id'])) ? $this->request->post['opay_website_id'] : $this->config->get('opay_website_id');
		$this->data['opay_user_id'] = (isset($this->request->post['opay_user_id'])) ? $this->request->post['opay_user_id'] : $this->config->get('opay_user_id');
		$this->data['opay_test_mode'] = (isset($this->request->post['opay_test_mode'])) ? $this->request->post['opay_test_mode'] : $this->config->get('opay_test_mode');
		$this->data['opay_password_sign'] = (isset($this->request->post['opay_password_sign'])) ? $this->request->post['opay_password_sign'] : $this->config->get('opay_password_sign');
		$this->data['opay_rsa_signature'] = (isset($this->request->post['opay_rsa_signature'])) ? $this->request->post['opay_rsa_signature'] : $this->config->get('opay_rsa_signature');
		$this->data['opay_certificate'] = (isset($this->request->post['opay_certificate'])) ? $this->request->post['opay_certificate'] : $this->config->get('opay_certificate');
		$this->data['opay_new_order_id'] = (isset($this->request->post['opay_new_order_id'])) ? $this->request->post['opay_new_order_id'] : $newOrderIdDefault;
		$this->data['opay_finished_order_id'] = (isset($this->request->post['opay_finished_order_id'])) ? $this->request->post['opay_finished_order_id'] : $finishedOrderIdDefault;
		$this->data['opay_canceled_order_id'] = (isset($this->request->post['opay_canceled_order_id'])) ? $this->request->post['opay_canceled_order_id'] : $canceledOrderIdDefault;
		$this->data['opay_show_channels'] = (isset($this->request->post['opay_show_channels'])) ? $this->request->post['opay_show_channels'] : $this->config->get('opay_show_channels');
		$this->data['opay_geo_zone_id'] = (isset($this->request->post['opay_geo_zone_id'])) ? $this->request->post['opay_geo_zone_id'] : $this->config->get('opay_geo_zone_id');
		$this->data['opay_sort_order'] = (isset($this->request->post['opay_sort_order'])) ? $this->request->post['opay_sort_order'] : $this->config->get('opay_sort_order');

		$this->template = 'payment/opay.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);

		$this->response->setOutput($this->render(TRUE), $this->config->get('config_compression'));
	}

	public function install()
	{

	}

	private function validate() {
		if (!$this->user->hasPermission('modify', 'payment/opay')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!@$this->request->post['opay_website_id']) {
			$this->error['opay_website_id'] = $this->language->get('error_website');
		}

		if (!@$this->request->post['opay_password_sign']) {
			if (!@$this->request->post['opay_certificate'] && !@$this->request->post['opay_rsa_signature']) {
				$this->error['opay_password_sign'] = $this->language->get('error_empty_signing');
			} elseif (!@$this->request->post['opay_rsa_signature']) {
				$this->error['opay_rsa_signature'] = $this->language->get('error_empty_rsa_signature');
			} elseif (!@$this->request->post['opay_certificate']) {
				$this->error['opay_certificate'] = $this->language->get('error_empty_certificate');
			}
		}

		if ($this->request->post['opay_test_mode'] and !@$this->request->post['opay_user_id']) {
			$this->error['opay_user_id'] = $this->language->get('error_empty_user_id');
		}

		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}
}