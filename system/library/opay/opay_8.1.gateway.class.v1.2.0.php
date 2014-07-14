<?php

require_once dirname(__FILE__).'/opay_8.1.gateway.core.interface.php';
require_once dirname(__FILE__).'/opay_8.1.gateway.webservice.interface.php';

class OpayGatewayException extends Exception implements OpayGatewayCoreException, OpayGatewayWebServicException{}

class OpayGateway implements OpayGatewayCoreInterface, OpayGatewayWebServiceInterface 
{

    protected $signaturePassword;
    protected $merchantRsaPrivateKey;
    protected $opayCertificate;
    
    public function setMerchantRsaPrivateKey($merchantRsaPrivateKey)
    {
        $this->merchantRsaPrivateKey = $merchantRsaPrivateKey;    
    }
    
    public function setOpayCertificate($opayCertificate)
    {
        $this->opayCertificate = $opayCertificate;    
    } 
    
    public function setSignaturePassword($password)
    {
        $this->signaturePassword = $password;    
    }
    
    public function getTypeOfSignatureIsUsed()
    {
        if (!empty($this->merchantRsaPrivateKey) && !empty($this->opayCertificate))
        {
            if (function_exists('openssl_pkey_get_public'))
            {
                return self::SIGNATURE_TYPE_RSA;
            }
            else
            {
                if (!empty($this->signaturePassword))
                {
                    return self::SIGNATURE_TYPE_PASSWORD;
                }
                else
                {
                    throw new OpayGatewayException('OpenSSL is not available in your server. To use RSA signature type (which is set when using setMerchantRsaPrivateKey() and setOpayCertificate()) install the OpenSSL PHP module. Otherwise use password signature (which is set when using setSignaturePassword()).', OpayGatewayException::SIGNATURE_OPEN_SSL_NOT_FOUND);
                }    
            }
        }
        else if (!empty($this->signaturePassword))
        {
            return self::SIGNATURE_TYPE_PASSWORD;
        }
        else
        {
            throw new OpayGatewayException('Signature parameters are not set. Use functions setMerchantRsaPrivateKey() and setOpayCertificate() to set parameters for RSA signature type, or setSignaturePassword() for password signature type.', OpayGatewayException::SIGNATURE_PARAMETERS_ARE_NOT_SET);
        }
    }
    
    public function signArrayOfParameters($parametersArray)
    {
        // cleaning signature parameters if someone tries to sign already signed array
        if (isset($parametersArray['rsa_signature']))
        {
            unset($parametersArray['rsa_signature']);   
        }
        
        if (isset($parametersArray['password_signature']))
        {
            unset($parametersArray['password_signature']);   
        }
        
        $signatureType = $this->getTypeOfSignatureIsUsed();
        
        $stringToBeSigned = '';
        foreach ($parametersArray as $key => $val)
        {
            $stringToBeSigned .= $key.$val;
        }
    
        if ($signatureType == self::SIGNATURE_TYPE_RSA)
        {
            $parametersArray['rsa_signature'] = $this->signStringUsingPrivateKey($stringToBeSigned, $this->merchantRsaPrivateKey);
        }
        else
        {
            $parametersArray['password_signature'] = $this->signStringUsingPassword($stringToBeSigned, $this->signaturePassword);    
        }
    
        return $parametersArray;
    }
    
    protected function signStringUsingPrivateKey($stringToBeSigned, $privateKey, $toBase64Encode = true)
    {
        // -- creating private key resource                          
        $pkeyid = openssl_get_privatekey($privateKey); 
        if ($pkeyid !== false)
        {  
            // -- signing the $stringToBeSigned
            if (openssl_sign($stringToBeSigned, $signature, $pkeyid) === true)
            { 
                if ($toBase64Encode == true)
                {
                    // -- encoding to base64
                    if (($signature = base64_encode($signature)) !== false)
                    { 
                        // stripping new lines
                        $signature = preg_replace("/[\r\n\t]*/", "", $signature); 
                        // -- freeing the memory
                        openssl_free_key($pkeyid);
                        return $signature;
                    }
                    else
                    {
                        throw new OpayGatewayException('Could not encode to base64 after signing using a private key.', OpayGatewayException::SIGNING_USING_PRIVATE_KEY_BASE_64_ERROR);
                    }
                }
                else
                {
                    return $signature;    
                }
            }
            else
            {
                throw new OpayGatewayException('Error occurred when signing using private key', OpayGatewayException::SIGNING_USING_PRIVATE_KEY_ERROR);
            }
        }
        else
        {
            throw new OpayGatewayException('Error reading private key', OpayGatewayException::SIGNING_USING_PRIVATE_KEY_READING_ERROR);
        }
    }
    
    protected function signStringUsingPassword($stringToBeSigned, $password)
    {
        return md5($stringToBeSigned . $password);
    }
    
    public function generatetAutoSubmitForm($url, $parametersArray, $sendEncoded = true)
    {
        $str  = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
        $str .= '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="lt" lang="lt">';
        $str .= '<head>';
        $str .= '    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';
        $str .= '    <title>...</title>';
        $str .= '
                    <script type="text/javascript">
                    //<![CDATA[
                        onload = function()
                        {
                            document.redirectForm.submit();
                        }
                    //]]>
                    </script>
        ';
        $str .= '</head>';
        $str .= '<body>';
        $str .= '<form action="'.htmlspecialchars($url, ENT_QUOTES, 'UTF-8').'" method="post" accept-charset="UTF-8" name="redirectForm">';
        if ($sendEncoded == true)
        {
            $encoded = $this->convertArrayOfParametersToEncodedString($parametersArray);
            $str .= '<input type="hidden" name="encoded" value="'.$encoded.'" />';
        }
        else
        {
            foreach ($parametersArray as $key => $value)
            {
                $str .= '<input type="hidden"  name="'.htmlspecialchars($key, ENT_QUOTES).'" value="'.htmlspecialchars($value, ENT_QUOTES, 'UTF-8').'" />';
            }
        }

        $str .=  '</form>';

        $str .= '</body>';
        $str .= '</html>';
    
        return $str;    
    }
    
    public function convertArrayOfParametersToEncodedString($parametersArray)
    {
        return strtr(base64_encode(http_build_query($parametersArray)), array('+' => '-', '/' => '_', '=' => ','));    
    }
    
    public function convertEncodedStringToArrayOfParameters($encodedString)
    {
        $data = strtr($encodedString, array('-' => '+', '_' => '/', ',' => '='));
        
        if (($data = base64_decode($data)) !== false)
        {
            $params = array();
            parse_str($data, $params);
            return $params;
        }
        else
        {
            throw new OpayGatewayException('Base64 decoding error when converting encoded request from gateway.', OpayGatewayException::GATEWAY_REQUEST_BASE64_DECODE_ERROR);
        }    
    }
    
    public function verifySignature($parametersArray)
    {
        $rsaSignature      = '';
        $passwordSignature = '';
        
        if (isset($parametersArray['rsa_signature']))
        {
            $rsaSignature = $parametersArray['rsa_signature']; 
            unset($parametersArray['rsa_signature']);   
        }
        
        if (isset($parametersArray['password_signature']))
        {
            $passwordSignature = $parametersArray['password_signature']; 
            unset($parametersArray['password_signature']);   
        }
        
        $stringToBeVerified = '';
        foreach ($parametersArray as $key => $val)
        {
            $stringToBeVerified .= $key.$val;
        }
    
        if (!empty($rsaSignature) && !empty($this->opayCertificate))
        {
            return $this->verifySignatureUsingCertificate($stringToBeVerified, $rsaSignature, $this->opayCertificate);    
        }
        else if (!empty($passwordSignature) && !empty($this->signaturePassword))
        {
            return $this->verifySignatureUsingPassword($stringToBeVerified, $passwordSignature, $this->signaturePassword);    
        }
        else
        {
            throw new OpayGatewayException('Could not verify a signature. Signature parameters are not set properly. Use functions setMerchantRsaPrivateKey() and setOpayCertificate() to set parameters for RSA signature type, or setSignaturePassword() for password signature type. ', OpayGatewayException::SIGNATURE_PARAMETERS_ARE_NOT_SET);        
        }
    }
    
    protected function verifySignatureUsingCertificate($string, $signature, $certificate)
    {  
        // -- extractig the public key 
        $pubkeyid = openssl_pkey_get_public($certificate); 
        if ($pubkeyid !== false)
        {
            // -- verifying the signature 
            $ok = openssl_verify($string, base64_decode($signature), $pubkeyid); 
            openssl_free_key($pubkeyid);
            if ($ok === 1)
            {   
                return true;
            }
            else if ($ok === 0)
            {    
                return false;
            }
            else
            {
                throw new OpayGatewayException('Error reading certificate or extracting a public key from it', OpayGatewayException::SIGNATURE_VERIFICATION_USING_CERTIFICATE_ERROR);
            } 
        }
        else
        {
            throw new OpayGatewayException('Error reading certificate or extracting a public key from it', OpayGatewayException::SIGNATURE_VERIFICATION_USING_CERTIFICATE_READING_ERROR);
        }
    }
    
    protected function verifySignatureUsingPassword($string, $signature, $password)
    {
        return ($this->signStringUsingPassword($string, $password) == $signature);     
    }
    
    
    public function webServiceRequest($url, $parametersArray, $sendEncoded = true)
    {
        if ($sendEncoded == true)
        {        
            $parametersArray['encoded'] = $this->convertArrayOfParametersToEncodedString($parametersArray);
        }
        $data = $this->sendRequest($url, 'POST', $parametersArray, false, "Content-Type: application/x-www-form-urlencoded\r\n");
        
        if ($data !== false)
        {
            $data = trim($data);
            $data = json_decode($data, true);
            $jsonLastError = json_last_error();
            if ($jsonLastError != JSON_ERROR_NONE) 
            {
                throw new OpayGatewayException('Could not decode JSON. json_decode() error code is '.$jsonLastError, OpayGatewayException::JSON_DECODING_ERROR);    
            }
            
            if (!is_array($data))
            {
                throw new OpayGatewayException('Wrong data format. An array must be returned.', OpayGatewayException::WRONG_JSON_FORMAT);    
            }
            
            return $data;
        }
        else
        { 
            throw new OpayGatewayException('Could not connect to server.', OpayGatewayException::COMMUNICATION_WITH_SERVER_ERROR);        
        }
 
    }
    
    protected function sendRequest($url, $httpMethod, $parametersArray, $keepAlive = false, $optionalHeaders = null, $timeout = 3)
    {
        $params = array(
          'http' => array(
            'method' => 'POST',
            'content' => http_build_query($parametersArray),
            'timeout' => $timeout 
          )
        );
        
        $params['http']['header']   = "User-Agent: OPAY Client\r\n"
                                     ."Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8\r\n"
                                     ."Accept-Language: en-us,en;q=0.5\r\n"
                                     ."Accept-Encoding: identity\r\n" // this client does not support a compression
                                     ."Accept-Charset: utf-8;q=0.7,*;q=0.7\r\n";
        
        if (!$keepAlive)
        {
            $params['http']['header'] .= "Connection: Close\r\n";
        }
        else
        {
            $params['http']['header'] .= "Connection: keep-alive\r\n";
        }
        
        if ($optionalHeaders !== null) {
            $params['http']['header'] .= $optionalHeaders;
        }
        

        return file_get_contents(
                $url, 
                false, 
                stream_context_create($params)
        );
        
        return $fp;     
    }
    
  
    
}
    
?>