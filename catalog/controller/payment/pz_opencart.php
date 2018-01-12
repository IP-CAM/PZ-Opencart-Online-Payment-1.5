<?php
/*
*************************************************************************************
Please Do not edit or add any code in this file without permission.
opencart version 1.5.5.1			Pz Version 1.0.0


Module Version. Pz-1.3  			Module release: August 16/2017
**************************************************************************************
 */
error_reporting(E_ALL);
class ControllerPaymentPzOpencart extends Controller {
	protected function index() {
		$this->language->load('payment/pz_opencart');

		$this->data['button_confirm'] = $this->language->get('button_confirm');
        $this->data['pz_opencart_mode'] 	= $this->config->get('pz_opencart_mode');
        $mode = $this->data['pz_opencart_mode'];
        $this->data['url'] 		        = $this->config->get('pz_opencart_url');

        if($mode == '1'){
           
            $this->data['action'] = $this->config->get('pz_opencart_live_url');//live mode url
          
        }
        else{
           
			  $this->data['action'] = $this->config->get('pz_opencart_test_url');//live mode url
            
        }

        $this->load->model('checkout/order');

		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
		if ($order_info) {
			$currencies = array(
				'AUD','CAD','EUR','GBP','JPY','USD','NZD','CHF','HKD','SGD',
				'SEK','DKK','PLN','NOK','HUF','CZK','ILS','MXN','MYR','BRL',
				'PHP','TWD','THB','TRY','INR', 'RUB'
			);

			if (in_array($order_info['currency_code'], $currencies)) {
				$currency = $order_info['currency_code'];
			} else {
				$currency = 'USD';
			}

			$shipping_total = 0;

		$gg = '';
		$products = $this->cart->getProducts();

		foreach ($products as $product) {

			 $quantity = $product['quantity'];
             $products = $product['name'];
             $gg .= $quantity . " - " . $products." ";
		}
			$this->data['orderdescription']= $gg;

			$Amount = $this->currency->format($order_info['total'],$currency, false, false);
            $this->data['title'] 		    = $this->config->get('pz_opencart_title');
            $this->data['description'] 		= $this->config->get('pz_opencart_description');
			$this->data['Merchant_Id'] 		= $this->config->get('pz_opencart_merchant_id');
		    $this->data['Order_Id'] 		= $this->session->data['order_id'];
			$this->data['Amount'] 			= $Amount;
			$this->data['currency_code'] 	= $currency;
			$this->data['totype']       	= $this->config->get('pz_opencart_partner_name');
			$this->data['partenerid']       	= $this->config->get('pz_opencart_partner_id');
			$this->data['ipaddr']       	= $this->config->get('pz_opencart_ipaddr');
			$this->data['pz_opencart_mode'] 	= $this->config->get('pz_opencart_mode');

				$Merchant_Id= $this->config->get('pz_opencart_merchant_id');
				$totype= $this->config->get('pz_opencart_partner_name');
				$partenerid= $this->config->get('pz_opencart_partner_id');
				$ipaddr= $this->config->get('pz_opencart_ipaddr');
				$Order_Id= $this->session->data['order_id'];
				$Amount= $this->data['Amount'];

            $Url= $this->url->link('payment/pz_opencart/callback');
				$WorkingKey= $this->config->get('pz_opencart_working_key');
						$pattern 		= '#http://www.#';
				preg_match($pattern, $Url, $matches);
				if(count($matches)==0)
				{
					$find_pattern   = '#http://#';
					$replace_string  = 'http://www.';
					$Url = preg_replace($find_pattern,$replace_string,$Url);
				}

			$Url= $this->url->link('payment/pz_opencart/callback');
		    $Checksum = $this->getchecksum($Merchant_Id,$totype,$Amount,$Order_Id,$Url,$WorkingKey);
			$this->data['Checksum'] = $Checksum;
			$this->data['WorkingKey'] = $WorkingKey;


            if($order_info['payment_firstname'])
			{
				$customer_firstname =  html_entity_decode($order_info['payment_firstname'], ENT_QUOTES, 'UTF-8');	
				$customer_lastname  =  html_entity_decode($order_info['payment_lastname'], ENT_QUOTES, 'UTF-8');	
				$this->data['billing_cust_name'] 	= $customer_firstname." ".$customer_lastname;
				$address1  							= html_entity_decode($order_info['payment_address_1'], ENT_QUOTES, 'UTF-8');	
				$address2  							= html_entity_decode($order_info['payment_address_2'], ENT_QUOTES, 'UTF-8');	
				$this->data['billing_cust_address'] = $address1." ".$address2;
				$this->data['billing_cust_city'] 	= html_entity_decode($order_info['payment_city'], ENT_QUOTES, 'UTF-8');
				$this->load->model('localisation/zone');
				$zone = $this->model_localisation_zone->getZone($order_info['payment_zone_id']);

				if (isset($zone['code'])) {
					$this->data['billing_cust_state'] 	= html_entity_decode($zone['code'], ENT_QUOTES, 'UTF-8');
				}
				$this->data['billing_cust_tel'] 		= html_entity_decode($order_info['telephone'], ENT_QUOTES, 'UTF-8');		
				$this->data['billing_zip_code'] 		= html_entity_decode($order_info['payment_postcode'], ENT_QUOTES, 'UTF-8');
				$this->data['billing_country_iso_code_2'] 		= $order_info['payment_iso_code_2'];
				$this->data['billing_country_iso_code_3'] 		= $order_info['payment_iso_code_3'];
				$billing_country_iso_code_3 			= $order_info['payment_iso_code_3'];
				$billing_country_query 					= $this->db->query("SELECT name FROM " . DB_PREFIX . "country where iso_code_3='".$billing_country_iso_code_3."'");
				$billing_country_name 					= $billing_country_query->row['name'];
				$this->data['billing_cust_country'] 	= $billing_country_name;
				
			}
			$this->data['delivery_cust_name']	='';
			$this->data['delivery_cust_address']='';
			$this->data['delivery_cust_city']	='';
			$this->data['delivery_cust_state']	='';
			$this->data['delivery_cust_tel']	='';
			$this->data['delivery_zip_code']	='';
			$this->data['delivery_cust_country']='';
			if($order_info['shipping_firstname'])
			{
				$customer_firstname 				= html_entity_decode($order_info['shipping_firstname'], ENT_QUOTES, 'UTF-8');	
				$customer_lastname  				= html_entity_decode($order_info['shipping_lastname'], ENT_QUOTES, 'UTF-8');	
				$this->data['delivery_cust_name'] 	= $customer_firstname." ".$customer_lastname;
				$address1  							= html_entity_decode($order_info['shipping_address_1'], ENT_QUOTES, 'UTF-8');	
				$address2  							= html_entity_decode($order_info['shipping_address_2'], ENT_QUOTES, 'UTF-8');	
				$this->data['delivery_cust_address']= $address1." ".$address2;
				$this->data['delivery_cust_city'] 	= html_entity_decode($order_info['shipping_city'], ENT_QUOTES, 'UTF-8');
				
				$this->load->model('localisation/zone');
				$zone = $this->model_localisation_zone->getZone($order_info['shipping_zone_id']);
				if (isset($zone['code'])) {
					$this->data['delivery_cust_state'] 	= html_entity_decode($zone['code'], ENT_QUOTES, 'UTF-8');
				}
				$this->data['delivery_cust_tel'] 		= html_entity_decode($order_info['telephone'], ENT_QUOTES, 'UTF-8');		
				$this->data['delivery_zip_code'] 		= html_entity_decode($order_info['shipping_postcode'], ENT_QUOTES, 'UTF-8');
				$delivery_country_iso_code_3 			= $order_info['shipping_iso_code_3'];
				$delivery_country_query 				= $this->db->query("SELECT name FROM " . DB_PREFIX . "country where iso_code_3='".$billing_country_iso_code_3."'");
				$delivery_country_name 					= $delivery_country_query->row['name'];
				$this->data['delivery_cust_country'] 	= $delivery_country_name;
			}
			$this->data['billing_cust_email']   = $order_info['email'];
			$this->data['billing_cust_notes']   = $this->session->data['comment'];
			$this->data['Redirect_Url']         = $Url;
			

			$this->load->library('encryption');
			$encryption 			= new Encryption($this->config->get('config_encryption'));
			$this->data['custom'] 	= $encryption->encrypt($this->session->data['order_id']);
			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/pz_opencart.tpl')) {
				$this->template 	= $this->config->get('config_template') . '/template/payment/pz_opencart.tpl';
			} else {
				$this->template 	= 'default/template/payment/pz_opencart.tpl';
			}
			$this->render();
		}
	}


public function getchecksum($memberid,$totype,$amount,$description,$redirecturl,$key)
{
	$str = "$memberid|$totype|$amount|$description|$redirecturl|$key";
	$generatedChecksum = md5($str);
	return $generatedChecksum;
}

public function verifychecksum($trackingid,$description,$amount,$status,$checksum,$key)
{

    $str = "$trackingid|$description|$amount|$status|$key";
    $generatedCheckSum = md5($str);

    if($generatedCheckSum == $checksum)
		return "true" ;
    else
		return "false" ;
}

	public function adler32($adler , $str)
	{
		$BASE =  65521 ;
		$s1 = $adler & 0xffff ;
		$s2 = ($adler >> 16) & 0xffff;
		for($i = 0 ; $i < strlen($str) ; $i++)
		{
			$s1 = ($s1 + Ord($str[$i])) % $BASE ;
			$s2 = ($s2 + $s1) % $BASE ;
		}
		return $this->leftshift($s2 , 16) + $s1;
	}
	
	public function leftshift($str , $num)
	{
	
		$str = DecBin($str);
		for( $i = 0 ; $i < (64 - strlen($str)) ; $i++)
			$str = "0".$str ;
			for($i = 0 ; $i < $num ; $i++) 
			{
				$str = $str."0";
				$str = substr($str , 1 ) ;
			}
		return $this->cdec($str) ;
	}
	
	public function cdec($num)
	{
		$dec = '';
		for ($n = 0 ; $n < strlen($num) ; $n++)
		{
		   $temp = $num[$n] ;
		   $dec =  $dec + $temp*pow(2 , strlen($num) - $n - 1);
		}
		return $dec;
	}

	public function DBG_error($msg) {
      $handle = fopen(DIR_APPLICATION . 'log.txt', 'a');
      fwrite($handle, "\n" . $msg);
      fclose($handle);
      return true;
    }

	public function callback() {

		$WorkingKey 	= $this->config->get('pz_opencart_working_key');
        $trackingid = "null"; //$_REQUEST['trackingid'];
		
			if($_REQUEST['trackingid'] !=null && $_REQUEST['trackingid'] != "")
			{
			$trackingid=$_REQUEST['trackingid'];	
			}
	    $Amount 		= $_REQUEST['amount'];
		$Order_Id 		= $_REQUEST['desc'];
		$Checksum 	    = $_REQUEST['checksum'];
		$AuthDesc 		= $_REQUEST['status'];

      $this->DBG_error("\n GET " . date("Y:m:D h:i:s") . "\n" . print_r($_GET, true));
      $this->DBG_error("\n WorkingKey " . date("Y:m:D h:i:s") . "\n" . $WorkingKey);
	

	    $Checksum = $this->verifychecksum($trackingid,$Order_Id,$Amount,$AuthDesc,$Checksum,$WorkingKey);

		 $this->DBG_error("\n verify checksum " . date("Y:m:D h:i:s") . "\n" . $Checksum);
	
		$this->load->language('payment/pz_opencart');
		$this->load->library('encryption');
		$this->load->model('checkout/order');
		$order_info 	= $this->model_checkout_order->getOrder($Order_Id);
		
		if($order_info)
		{
			$this->language->load('payment/pz_opencart');
			$data = array(
				"WorkingKey"	=> $WorkingKey,
				
				"Amount"		=> $Amount,
				"Order_Id"		=> $Order_Id,
				
				"Checksum"		=> $Checksum,
				"AuthDesc"		=> $AuthDesc
				);
				
			$AuthDesc = $data['AuthDesc'];

			if (isset($order_info['order_id'])) {
				$Order_Id = $order_info['order_id'];
			} else {
				$Order_Id = $data['Order_Id'];
			
			}
		
			$payment_status_message = '';
			if($Checksum=="true" && $AuthDesc=="Y")
			{
				$payment_status_message = $this->language->get('success_comment');
				$payment_status  = true;
				$order_status_id = $this->config->get('pz_opencart_completed_status_id');
				if (!$order_info['order_status_id']) {
					$this->model_checkout_order->confirm($Order_Id, $order_status_id, $payment_status_message);
				} else {
					$this->model_checkout_order->update($Order_Id, $order_status_id, $payment_status_message, FALSE);
				}

				 $this->DBG_error("\n verify checksum in condition" . date("Y:m:D h:i:s") . "\n" . $Checksum);

				$payment_confirmation_mail =  $this->config->get('pz_opencart_payment_confirmation_mail');
				if($payment_confirmation_mail)
				{					
					$subject  = 'Direcpay Payment Status';
					$text     = "Dear ".$order_info['firstname']." ".$order_info['lastname']. "\n\n";
					$text    .= "We have received your order, Thanks for your Pz opencart payment.The transaction was successful.Your payment is authorized.". "\n";
					$text    .= "The details of the order are below:". "\n\n";
					$text    .= "Order ID:  ".$Order_Id. "\n";
					$text    .= "Date Ordered:  ".$order_info['date_added']. "\n";
					$text    .= "Payment Method:  ".$order_info['payment_method']. "\n";
					$text    .= "Shipping Method:  ".$order_info['shipping_method']. "\n";
					$text    .= "Order Total:  ".$order_info['total']. "\n\n";				
					$to 	  = array(1=>$order_info['email'],2=>$this->config->get('config_email'));
					$mail 	  = new Mail();							
					$mail->setTo($to);
					$mail->setFrom($this->config->get('config_email'));
					$mail->setSender($this->config->get('config_name'));
					$mail->setSubject($subject);
					$mail->setText(html_entity_decode($text, ENT_QUOTES, 'UTF-8'));
					$mail->send();
					mail($order_info['email'],'Pz opencart Payment Status','Your payment is authorized.');
				}
			
			}
			else if($Checksum=="true" && $AuthDesc=="P")
			{
				$payment_status_message = $this->language->get('pending_comment');
				$payment_status = true;
				$order_status_id = $this->config->get('pz_opencart_pending_status_id');
				if (!$order_info['order_status_id']) {
					$this->model_checkout_order->confirm($Order_Id, $order_status_id, $payment_status_message);
				} else {
					$this->model_checkout_order->update($Order_Id, $order_status_id, $payment_status_message, FALSE);
					
				}
				
			}
			else if($Checksum=="true" && $AuthDesc=="N")
			{

				$payment_status_message =$this->language->get('declined_comment');
				$payment_status = false;
				$order_status_id = $this->config->get('pz_opencart_failed_status_id');
				
				if (!$order_info['order_status_id']) {
					$this->model_checkout_order->confirm($Order_Id, $order_status_id, $payment_status_message);
					
				} else {
					$this->model_checkout_order->update($Order_Id, $order_status_id, $payment_status_message, FALSE);
				}	
			}
			
			else
			{

				$payment_status_message = $this->language->get('failed_comment');				
				$payment_status = false;				
				$order_status_id = $this->config->get('pz_opencart_failed_status_id');				
				if (!$order_info['order_status_id']) {
					$this->model_checkout_order->confirm($Order_Id, $order_status_id, $payment_status_message);
				} else {
					$this->model_checkout_order->update($Order_Id, $order_status_id, $payment_status_message, FALSE);
				}

			}

			$this->data['title'] 	= sprintf($this->language->get('heading_title'), $this->config->get('config_name'));
			if (!isset($this->request->server['HTTPS']) || ($this->request->server['HTTPS'] != 'on')) {
			$this->data['base'] = HTTP_SERVER;
			} else {
		    $this->data['base'] = HTTPS_SERVER;
			}
			$this->data['language'] 		= $this->language->get('code');
			$this->data['direction'] 		= $this->language->get('direction');
			$this->data['heading_title'] 	= sprintf($this->language->get('heading_title'), $this->config->get('config_name'));
			$this->data['text_response'] 	= $this->language->get('text_response');
			$this->data['payment_status_message'] = $payment_status_message;			
			
			$this->DBG_error("\n text_response" . date("Y:m:D h:i:s") . "\n" . $this->data['text_response']);
			$this->DBG_error("\n payment_status_message" . date("Y:m:D h:i:s") . "\n" . $this->data['payment_status_message']);
			$this->DBG_error("\n payment_status" . date("Y:m:D h:i:s") . "\n" . $payment_status);

            $this->data['pz_opencart_mode'] 	= $this->config->get('pz_opencart_mode');
            $mode= $this->data['pz_opencart_mode'];
            if($mode == '1'){
               

               
				  $this->data['action'] = $this->config->get('pz_opencart_live_url');//live mode url
 
            }
            else{
                
				  $this->data['action'] = $this->config->get('pz_opencart_test_url');//live mode url
            }


            if($payment_status)
			{
				$this->data['text_payment_wait'] = sprintf($this->language->get('text_payment_wait'), $this->url->link('checkout/success'));
				$this->data['continue'] = $this->url->link('checkout/success');
			}
			else
			{
				$this->data['text_payment_wait'] = sprintf($this->language->get('text_payment_wait'), $this->url->link('checkout/cart'));
				$this->data['continue'] = $this->url->link('checkout/cart');
				
			}
			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/pz_opencart_response.tpl')) {

					$this->template = $this->config->get('config_template') . '/template/payment/pz_opencart_response.tpl';

				} else {

					$this->template = 'default/template/payment/pz_opencart_response.tpl';

				}
			$this->children = array(

					'common/column_left',

					'common/column_right',

					'common/content_top',

					'common/content_bottom',

					'common/footer',

					'common/header'

				);
			$this->response->setOutput($this->render());
		}
	}
}
?>
