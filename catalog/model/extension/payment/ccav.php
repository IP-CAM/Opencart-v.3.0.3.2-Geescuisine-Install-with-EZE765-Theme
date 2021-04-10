<?php
class ModelExtensionPaymentCcav extends Model {
	public function getMethod($address, $total) {
		$this->load->language('extension/payment/ccav');

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('cod_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");

		if ($this->config->get('cod_total') > 0 && $this->config->get('cod_total') > $total) {
			$status = false;
		} elseif (!$this->cart->hasShipping()) {
			$status = false;
		} elseif (!$this->config->get('cod_geo_zone_id')) {
			$status = true;
		} elseif ($query->num_rows) {
			$status = true;
		} else {
			$status = false;
		}

		$method_data = array();

		if ($status) {
			$method_data = array(
				'code'       => 'ccav',
				'title'      => $this->language->get('text_title'),
				'terms'      => '',
				'sort_order' => $this->config->get('ccav_sort_order')
			);
		}

		return $method_data;
	}
	public function getUrl($id)
	{
		$this->load->model('checkout/order');            
        $order_info = $this->model_checkout_order->getOrder($id);
		$merchant_data_array['merchant_id'] 		= $this->config->get('ccavenuepay_merchant_id');
		$merchant_data_array['order_id']			= $id;
		$merchant_data_array['currency']			= $order_info->currency_code;
		$merchant_data_array['amount'] 				= $Amount;
		$merchant_data_array['redirect_url']		= $redirect_url;
		$merchant_data_array['cancel_url'] 			= $cancel_url;
		$merchant_data_array['language']			= "EN";
		$merchant_data_array['billing_name']		= $billing_name;
		$merchant_data_array['billing_address']		= $billing_address;
		$merchant_data_array['billing_city']		= $billing_city;
		$merchant_data_array['billing_state']		= $billing_state;
		$merchant_data_array['billing_zip']			= $billing_zip;
		$merchant_data_array['billing_country']		= $billing_country;
		$merchant_data_array['billing_tel']			= $billing_tel;
		$merchant_data_array['billing_email']		= $billing_email;
		$merchant_data_array['delivery_name']		= $delivery_name;
		$merchant_data_array['delivery_address']	= $delivery_address;
		$merchant_data_array['delivery_city']		= $delivery_city;
		$merchant_data_array['delivery_state']		= $delivery_state;
		$merchant_data_array['delivery_zip']		= $delivery_zip;
		$merchant_data_array['delivery_country']	= $delivery_country;
		$merchant_data_array['delivery_tel']		= $delivery_tel;
		$merchant_data_array['merchant_param1']		= $merchant_param1;
		 var_dump($order_info);
		var_dump($merchant_data);
		die(); 
	}
	public function proceed($order_info,$app=false)
	{
		$Amount						= $this->currency->format($order_info['total'], $order_info['currency_code'], false, false);
			$Merchant_Id 				= $this->config->get('payment_ccav_merchant_id');
			$access_code 				= $this->config->get('payment_ccav_access_code');
			$Order_Id 					= $this->session->data['order_id'];
			$Url 						= $this->url->link('payment/ccavenuepay/callback');
			$workingKey 				= $this->config->get('payment_ccav_working_key');			
			$Url 						= $this->url->link('payment/ccavenuepay/callback');
			$this->load->model('localisation/zone');
			$billing_name 		= '';
			$billing_address 	= '';
			$billing_city		= '';
			$billing_state		= '';
			$billing_tel		= '';
			$billing_zip		= '';
			$billing_country 	= '';
			$billing_email		= '';		
			
			$delivery_name		= '';
			$delivery_address 	= '';
			$delivery_city		= '';
			$delivery_state		= '';
			$delivery_tel		= '';
			$delivery_zip		= '';
			$delivery_country 	= '';
			if($app)
			$merchant_param1 	= '1';
			else
			$merchant_param1 	= '';
			
			if($order_info['payment_firstname'])
			{
				$customer_firstname 				= html_entity_decode($order_info['payment_firstname'], ENT_QUOTES, 'UTF-8');	
				$customer_lastname 				    = html_entity_decode($order_info['payment_lastname'], ENT_QUOTES, 'UTF-8');					
				$billing_name						= $customer_firstname." ".$customer_lastname;
				$address1  							= html_entity_decode($order_info['payment_address_1'], ENT_QUOTES, 'UTF-8');	
				$address2  							= html_entity_decode($order_info['payment_address_2'], ENT_QUOTES, 'UTF-8');	
				$billing_address 					= $address1." ".$address2;
				$billing_city 						= html_entity_decode($order_info['payment_city'], ENT_QUOTES, 'UTF-8');
				$billing_state						= $order_info['payment_zone'];
				$billing_tel						= html_entity_decode($order_info['telephone'], ENT_QUOTES, 'UTF-8');		
				$billing_zip						= html_entity_decode($order_info['payment_postcode'], ENT_QUOTES, 'UTF-8');
				//$billing_country_iso_code_3 		= $order_info['payment_iso_code_3'];
				//$billing_country_query 				= $this->db->query("SELECT name FROM " . DB_PREFIX . "country where iso_code_3='".$billing_country_iso_code_3."'");
				$billing_country_name 				= html_entity_decode($order_info['payment_country'], ENT_QUOTES, 'UTF-8');
				$billing_country					= html_entity_decode($order_info['payment_country'], ENT_QUOTES, 'UTF-8');
				$billing_country					= $billing_country_name;
			}
			
			
			if($order_info['shipping_firstname'])
			{
				$customer_firstname 				= html_entity_decode($order_info['shipping_firstname'], ENT_QUOTES, 'UTF-8');	
				$customer_lastname  				= html_entity_decode($order_info['shipping_lastname'], ENT_QUOTES, 'UTF-8');	
				$delivery_name						= $customer_firstname." ".$customer_lastname;
				$address1  							= html_entity_decode($order_info['shipping_address_1'], ENT_QUOTES, 'UTF-8');	
				$address2  							= html_entity_decode($order_info['shipping_address_2'], ENT_QUOTES, 'UTF-8');	
				$delivery_address					= $address1." ".$address2;									
				$delivery_city						= html_entity_decode($order_info['shipping_city'], ENT_QUOTES, 'UTF-8');
				$delivery_state						= $order_info['shipping_zone'];
				$delivery_tel						= html_entity_decode($order_info['telephone'], ENT_QUOTES, 'UTF-8');				
				$delivery_zip						= html_entity_decode($order_info['shipping_postcode'], ENT_QUOTES, 'UTF-8');
				//$delivery_country_iso_code_3 		= $order_info['shipping_iso_'];
				//$delivery_country_query 			= $this->db->query("SELECT name FROM " . DB_PREFIX . "country where iso_code_3='".$billing_country_iso_code_3."'");
				$delivery_country_name 				= html_entity_decode($order_info['shipping_country'], ENT_QUOTES, 'UTF-8');
				$delivery_country					= html_entity_decode($order_info['shipping_country'], ENT_QUOTES, 'UTF-8');
				$delivery_country					= $delivery_country_name;
			}
		
			$billing_email								= $order_info['email'];
			$merchant_param1							= $this->session->data['comment'];
			$redirect_url								= $this->config->get('config_ssl')."payment/response";
			$cancel_url									= $this->config->get('config_ssl')."payment/response";
			$merchant_data_array 						= array();			
			$merchant_data_array['merchant_id'] 		= $Merchant_Id;
			$merchant_data_array['order_id']			= $Order_Id;
			$merchant_data_array['currency']			= $order_info['currency_code'];
			$merchant_data_array['amount'] 				= $Amount;
			$merchant_data_array['redirect_url']		= $redirect_url;
			$merchant_data_array['cancel_url'] 			= $cancel_url;
			$merchant_data_array['language']			= "EN";
			$merchant_data_array['billing_name']		= $billing_name;
			$merchant_data_array['billing_address']		= $billing_address;
			$merchant_data_array['billing_city']		= $billing_city;
			$merchant_data_array['billing_state']		= $billing_state;
			$merchant_data_array['billing_zip']			= $billing_zip;
			$merchant_data_array['billing_country']		= $billing_country;
			$merchant_data_array['billing_tel']			= $billing_tel;
			$merchant_data_array['billing_email']		= $billing_email;
			$merchant_data_array['delivery_name']		= $delivery_name;
			$merchant_data_array['delivery_address']	= $delivery_address;
			$merchant_data_array['delivery_city']		= $delivery_city;
			$merchant_data_array['delivery_state']		= $delivery_state;
			$merchant_data_array['delivery_zip']		= $delivery_zip;
			$merchant_data_array['delivery_country']	= $delivery_country;
			$merchant_data_array['delivery_tel']		= $delivery_tel;
			if($app)
			$merchant_param1 	= '1';
			else
			$merchant_param1 	= '';
			$merchant_data_array['merchant_param1']		= $merchant_param1;
			
			$merchant_data 								= "";	
			foreach ($merchant_data_array as $key => $value){
				$merchant_data.=$key.'='.$value.'&';
			}
			//echo $merchant_data;
			$data['access_code']	 					= $access_code ; 
			$encrypted_data = $this->encrypt($merchant_data,$workingKey); // Method for encrypting the data.

			$production_url='https://secure.ccavenue.com/transaction/transaction.do?command=initiateTransaction&encRequest='.$encrypted_data.'&access_code='.$access_code;
			
			return $production_url;
			
	}


	public function response($rec)
	{
		$workingKey 				= $this->config->get('payment_ccav_working_key');	
		$rcvdString=$this->decrypt($rec,$workingKey);		//Crypto Decryption used as per the specified working key.
		$order_status="";
		$decryptValues=explode('&', $rcvdString);
		$dataSize=sizeof($decryptValues);
		
		foreach($decryptValues as $value)
		{
			$information=explode('=',$value);
			if($information[0]=="order_status")
				$order_status=$information[1];
			elseif($information[0]=="merchant_param1")
				$app  = $information[1];
		}
		/* var_dump($decryptValues);
		echo '<br>'.$app;
die(); */

		if($app)
		{
			if($order_status=="Success")
			{
				$this->load->model('checkout/order');
				$this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('ccav_order_status_id'));
				echo "<br>Thank you for shopping with us. Your credit card has been charged and your transaction is successful. We will be shipping your order to you soon.<script type='text/javascript'>Android.showSuccess('Payment Successfully Done');	</script>";
				//$this->response->redirect($this->url->link('checkout/success'));
			}
			elseif($order_status=="Aborted")
			{
				echo "<br>Thank you for shopping with us.We will keep you posted regarding the status of your order through e-mail. <script type='text/javascript'>Android.showError('Payment Processing Failed');	</script>";
			}
			elseif($order_status=="Failure")
			{
				echo "<br>Thank you for shopping with us.However,the transaction has been declined. <a href=\"/\">Home</a> <script type='text/javascript'>Android.showError('Payment Processing Failed');	</script>";
			}
			else
			{
				
				echo "<br>Security Error. Illegal access detected <a href=\"/\">Home</a> <script type='text/javascript'>Android.showError('Payment Processing Failed');	</script> ";
			
			}
			die();
		}
		return $order_status;
		
		
	}

    public function encrypt($plainText,$key)
	{
		$secretKey = $this->hextobin(md5($key));
		$initVector = pack("C*", 0x00, 0x01, 0x02, 0x03, 0x04, 0x05, 0x06, 0x07, 0x08, 0x09, 0x0a, 0x0b, 0x0c, 0x0d, 0x0e, 0x0f);
	  	$openMode = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '','cbc', '');
	  	$blockSize = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, 'cbc');
		$plainPad = $this->pkcs5_pad($plainText, $blockSize);
	  	if (mcrypt_generic_init($openMode, $secretKey, $initVector) != -1) 
		{
		      $encryptedText = mcrypt_generic($openMode, $plainPad);
	      	      mcrypt_generic_deinit($openMode);
		      			
		} 
		return bin2hex($encryptedText);
	}

	public function decrypt($encryptedText,$key)
	{
		$secretKey = $this->hextobin(md5($key));
		$initVector = pack("C*", 0x00, 0x01, 0x02, 0x03, 0x04, 0x05, 0x06, 0x07, 0x08, 0x09, 0x0a, 0x0b, 0x0c, 0x0d, 0x0e, 0x0f);
		$encryptedText=$this->hextobin($encryptedText);
	  	$openMode = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '','cbc', '');
		mcrypt_generic_init($openMode, $secretKey, $initVector);
		$decryptedText = mdecrypt_generic($openMode, $encryptedText);
		$decryptedText = rtrim($decryptedText, "\0");
	 	mcrypt_generic_deinit($openMode);
		return $decryptedText;
	}
	//*********** Padding Function *********************

	public function pkcs5_pad ($plainText, $blockSize)
	{
	    $pad = $blockSize - (strlen($plainText) % $blockSize);
	    return $plainText . str_repeat(chr($pad), $pad);
	}

	//********** Hexadecimal to Binary function for php 4.0 version ********

	public function hextobin($hexString) 
   	{ 
        	$length = strlen($hexString); 
        	$binString="";   
        	$count=0; 
        	while($count<$length) 
        	{       
        	    $subString =substr($hexString,$count,2);           
        	    $packedString = pack("H*",$subString); 
        	    if ($count==0)
		    {
				$binString=$packedString;
		    } 
        	    
		    else 
		    {
				$binString.=$packedString;
		    } 
        	    
		    $count+=2; 
        	} 
  	        return $binString; 
	} 
}
