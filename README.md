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

|Key |	Description |
| ------------- | ------------- |
| consumer_key |	Daraja app consumer key |
| consumer_secret |	Daraja app consumer secret|
| transaction_type |		paybill or buygoods |
| shortcode	 |	Paybill /Business shortcode |
| store_number	 |	Till number (for Buy Goods) |
| b2c_shortcode |		B2C shortcode |
| b2b_shortcode |		B2B shortcode |
| initiator_name |		API initiator username |
| initiator_password |		API initiator password |
| pass_key	 |	Lipa na M-Pesa passkey |
| security_credential |		Pre-generated security credential (optional) |
| callback_url	 |	STK Push callback URL base |
| so_callback_url	 |	Standing Order callback URL |
| confirmation_url |		C2B confirmation URL |
| validation_url	 |	C2B validation URL |
| result_url |		Base URL for result callbacks |
| timeout_url	 |	Base URL for timeout callbacks |
 
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
## STKPush Status
Checks the status of an STK Push transaction.

```
$mpesa->STKPushQuery('ws_CO_191220191020363925');
```
## B2C (Business to Customer)

$mpesa->b2c($amount, $commandId, $receiver, $remark, $result_url = 'b2c', $timeout_url = 'b2c', $occassion = null)

Sends money from business to customer.
```
$mpesa->b2c(
    500,
    'BusinessPayment',
    '254708374149',
    'Payment for services'
);
```
 
## B2B (Business to Business)
Transfers funds between businesses.
$mpesa->b2b($Amount, $PartyB, $commandId, $AccountReference, $Remarks, $result_url = 'b2b', $timeout_url = 'b2b')

```
$mpesa->b2b(
    1000,
    '600000',
    'BusinessPayBill',
    'ACCT-001',
    'Transfer to supplier'
);
```

## Transaction Reversal
Reverses a B2B, B2C, or C2B transaction.
$mpesa->reversal($Amount, $TransactionID, $Remarks, $result_url = 'reversal', $timeout_url = 'reversal', $Occasion = NULL)

```
$mpesa->reversal(
    500,
    'LKXXXX1234',
    'Reversal request'
);
```

## Account Balance
Queries the account balance of a shortcode.
Identifier Types: 1 (MSISDN), 2 (Till Number), 4 (Shortcode)
$mpesa->accountbalance(4, 'Balance query');

```
$mpesa->accountbalance($IdentifierType, $Remarks, $result_url = 'balance', $timeout_url = 'balance')

