<?php
require_once("vendor/autoload.php");

$mpesa  = new Wasksofts\Mpesa_v2\Mpesa();
$mpesa->config('consumer_key', "");
$mpesa->config('consumer_secret', "");
$mpesa->config('pass_key', '');
$mpesa->config('transaction_type', 'PAYBILL');
$mpesa->config('shortcode', '');
$mpesa->config('store_number', '');
$mpesa->config('b2c_shortcode', '');
$mpesa->config('callback_url', 'https://example.com/callback_url/');
$mpesa->config('confirmation_url', 'https://example.com/confirmation_url/');
$mpesa->config('validation_url', 'https://example.com/validation_url/');
$mpesa->config('initiator_name', '');
$mpesa->config('initiator_pass', '');
$mpesa->config('security_credential', '');
$mpesa->config('result_url', 'https://example.com/result_url/');
$mpesa->config('timeout_url', 'https://example.com/timeout_url/');
$mpesa->config('env', 'production');

//echo $mpesa->oauth_token();
//print_r($mpesa->STKPush('5', '254716224372', 'test001', 'test'));
//$mpesa->STKPushQuery('ws_CO_19092024145257708716224372');
//$mpesa->register_url(); 
//$mpesa->b2c('200', 'BusinessPayment', '254716224372', 'payment');
$mpesa->transaction_status('SIJ159QZWJ', 'payment');
$mpesa->accountbalance('4', 'remarks');
$mpesa->reversal('2', '254708374149', '1', 'NCR7S1UXBT');

var_dump($mpesa->getResponseData());
