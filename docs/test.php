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
$mpesa->config('callback_url', 'https://example.com/webhook.php&type=callback_url');
$mpesa->config('confirmation_url', 'https://example.com/webhook.php&type=confirmation_url');
$mpesa->config('validation_url', 'https://example.com/webhook.php&type=validation_url');
$mpesa->config('initiator_name', '');
$mpesa->config('initiator_password', '');
$mpesa->config('security_credential', '');
$mpesa->config('result_url', 'https://example.com/webhook.php&type=result_url');
$mpesa->config('timeout_url', 'https://example.com/webhook.php&type=timeout_url');
$mpesa->config('logo_link', 'https://example.com/logo.png');
$mpesa->config('official_contact', '25412345678');
$mpesa->config('env', 'production'); //sandbox

echo $mpesa->oauth_token();
$mpesa->STKPush('500', '254716224372', 'test001', 'test');
$mpesa->STKPushQuery('ws_CO_19092024145257708716224372');
$mpesa->register_url();
$mpesa->b2c('200', 'BusinessPayment', '254708374149', 'payment');
$mpesa->transaction_status('SIJ159QZWJ', 'payment');
$mpesa->accountbalance('4', 'remarks');
$mpesa->reversal('2', '254708374149', '1', 'NCR7S1UXBT');
$mpesa->generate_qrcode(10, 'TEST10');

var_dump($mpesa->getResponseData());
