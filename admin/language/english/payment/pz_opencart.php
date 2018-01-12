<?php
/*
*************************************************************************************
Please Do not edit or add any code in this file without permission.

opencart version 1.5.5.1			Pz Version 1.0.0


Module Version. Pz-1.0.0  			Module release: August 16/2017
**************************************************************************************
 */
 

$_['heading_title']							= 'Pz Opencart Online Payment';

$_['text_payment']							= 'Payment';
$_['entry_title']							= 'Title :';
$_['entry_live_url']					    = 'Live Url to send the request:';
$_['entry_test_url']					    = 'Test Url to send the request:';
$_['entry_description']					    = 'Description :';
$_['text_success']							= 'Success: You have modified Pz Opencart Pay account details!';
$_['text_pz_opencart']					    	= '<a onclick="window.open(\'#\');"><img src="view/image/payment/pz.png" alt="Pz Opencart Online Payment" title="Pz Opencart Online Payment" style="border: 1px solid #EEEEEE; max-width: 65px;" /></a>';
$_['entry_status']							= 'Status:';
$_['text_live']						    	= 'Live:';
$_['text_test']							    = 'Test:';

$_['text_file']							    = 'Send this file:';

$_['entry_partner_name']	              	= 'Name of Payment Service Provider:<br /><span class="help">Provided by Pz Opencart. Do not change the value. It will be fixed for all transactions Accept only [0-9][A-Z][a-z] .</span>';
$_['entry_partner_id']	              	    = 'Partner Id:<br /><span class="help">Enter partner id provided to you by Pz Opencart</span>';
$_['entry_ipaddr']	              	    = 'Ip Address:<br /><span class="help">Your Ip address</span>';
$_['entry_mode']	                     	= 'Transaction Mode:<span class="help">Transaction mode used for processing orders.</span>';

$_['entry_merchant_id']	                   	= 'Merchant ID:';
$_['entry_working_key']	                   	= 'Working Key:<span class="help">Put in the 32 bit alphanumeric key. To get this key, Login to your Pz Opencart Merchant Account and click Settings -> Generate Key</span>';
$_['entry_license_key']	                   	= 'License Key:';
$_['entry_payment_confirmation_mail']       = 'Payment Confirmation E-Mail';

$_['entry_geo_zone']						= 'Geo Zone:';
$_['entry_sort_order']						= 'Sort Order:';
$_['entry_completed_status']				= 'Order Status Completed:<br /><span class="help">This is the status set when the payment has been completed successfully.</span>';
$_['entry_pending_status']			        = 'Order Status Pending:<br /><span class="help">The payment is pending; see the pending_reason variable for more information. Please note, you will receive another Instant Payment Notification when the status of the payment changes to Completed, Failed, or Denied.</span>';
$_['entry_failed_status']				    = 'Order Status Failed:<br /><span class="help">The payment has failed. This will only happen if the payment was attempted from your customers bank account.</span>';


$_['error_permission']						= 'Warning: You do not have permission to modify payment Pz Opencart Pay!';
$_['error_merchant_id']						= 'Merchant ID required!';
$_['error_partner_name']					= 'Partner Name/Totype required!';
$_['error_partner_id']					    = 'Partner id required!';
$_['error_ipaddr']					        = 'Ip address required';
$_['error_working_key']						= 'Working Key required!';
$_['error_license_key']						= 'License Key required!';
?>