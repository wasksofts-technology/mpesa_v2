# Introduction
Mpesa library which you can use with various framework like laravel ,codeigniter ,cakephp and many more
This package seeks to help php developers implement the various Mpesa APIs without much hustle. It is based on the REST API whose documentation is available on https://developer.safaricom.co.ke.

#  Installation using composer
``` bash
 composer require wasksofts-technology/mpesa_v2
```

## Quick start
```
 <?php
require 'vendor/autoload.php';

use Wasksofts\Mpesa_v2\Mpesa;

$mpesa = new Mpesa();

// Configure credentials
$mpesa->config('consumer_key', 'YOUR_CONSUMER_KEY');
$mpesa->config('consumer_secret', 'YOUR_CONSUMER_SECRET');
$mpesa->config('shortcode', '174379');
$mpesa->config('pass_key', 'YOUR_PASSKEY');
$mpesa->config('env', 'sandbox'); // or 'production'
```

## Configuration
Set configuration values using config($key, $value).

Key	Description
consumer_key	Daraja app consumer key
consumer_secret	Daraja app consumer secret
transaction_type	paybill or buygoods
shortcode	Paybill / Business shortcode
store_number	Till number (for Buy Goods)
b2c_shortcode	B2C shortcode
b2b_shortcode	B2B shortcode
initiator_name	API initiator username
initiator_password	API initiator password
pass_key	Lipa na M-Pesa passkey
security_credential	Pre-generated security credential (optional)
callback_url	STK Push callback URL base
so_callback_url	Standing Order callback URL
confirmation_url	C2B confirmation URL
validation_url	C2B validation URL
result_url	Base URL for result callbacks
timeout_url	Base URL for timeout callbacks
official_contact	Bill Manager official contact
logo_link	Bill Manager logo URL
env	sandbox or production


## register URL
Registers validation and confirmation URLs for a shortcode.  Cancelled/Completed
```
$mpesa->register_url('Completed',$version = "v1");

```

## STK Push (Lipa na M-Pesa Online)
STKPush($Amount, $phoneNumberSendingFund, $AccountReference, $TransactionDesc)
Initiates an STK push to a customer's phone.
```
$response = $mpesa->STKPush(
    100,                    // Amount
    '254708374149',         // Phone
    'INV-001',              // Account Reference
    'Payment for goods'     // Description
);

```


