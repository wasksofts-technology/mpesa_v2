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
```php
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

```php
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

```
## Transaction Status
Checks the status of any transaction.
Identifier Types: 1 (MSISDN), 2 (Till Number), 4 (Shortcode)
$mpesa->transaction_status($TransactionID, $Remarks, $indentifier = 4, $result_url = "transaction_status", $timeout_url = "transaction_status", $Occassion = NULL)

```
$mpesa->transaction_status(
    'LKXXXX1234',
    'Status check'
);

```

## QR Code Generation
Generates a dynamic M-Pesa QR code.
Formats: 1 (Image), 2 (QR String), 3 (Binary), 4 (PDF)
$mpesa->generate_qrcode($amount, $reference, $MerchantName = 'SERVICE', $qrformat = 1, $trxcode = 'PG')

```
$mpesa->generate_qrcode(
    100,
    'INV-001',
    'MyShop',
    2
);
```
## Tax Remittance
Remits tax to Kenya Revenue Authority (KRA).
$mpesa->tax_remittance($amount, $account_prn, $Remarks = "OK", $result_url = 'tax', $timeout_url = 'tax', $kra_paybill = "572572")

```
$mpesa->tax_remittance(
    2500,
    'PRN-123456',
    'Tax payment'
);

```
## Standing Orders
Creates a recurring standing order on a customer profile.
$mpesa->standing_order($name, $start_date, $end_date, $amount, $from, $AccountReference, $TransactionDesc, $Frequency)
Frequency: 1 One-off, 2 Daily, 3 Weekly, 4 Monthly, 5 Bi-Monthly, 6 Quarterly, 7 Half Year, 8 Yearly

```
$mpesa->standing_order(
    'Monthly Subscription',
    '20240101',
    '20241231',
    500,
    '254708374149',
    'SUB-001',
    'Monthly subscription fee',
    4
);
```

## Bill Manager
optin_biller($email, $reminders = 1)

Opts a business into Bill Manager.
php
```
$mpesa->optin_biller('billing@example.com');

```
optin_update($email, $reminders = 1)

Updates Bill Manager onboarding details.
php

```
$mpesa->optin_update('newemail@example.com');
```

single_invoice($reference, $billedfullname, $billedphoneNumber, $billedperiod, $invoiceName, $dueDate, $accountRef, $amount)

Creates and sends a single e-invoice.

```php

$mpesa->single_invoice(
    'EXT-001',
    'John Doe',
    '254708374149',
    '2024-01',
    'January Bill',
    '2024-01-31',
    'ACC-001',
    1500
);
```

bulk_invoicing($invoiceArray)

Creates and sends multiple invoices at once.
```
php

$mpesa->bulk_invoicing([
    ['externalReference' => 'EXT-001', /* ... */],
    ['externalReference' => 'EXT-002', /* ... */],
]);
````

reconciliation($payment_date, $paidAmmount, $actReference, $transactionId, $phoneNumber, $fullName, $invoiceName, $reference)

Reconciles a payment against an invoice.
```php

$mpesa->reconciliation(
    '2024-01-15',
    1500,
    'ACC-001',
    'TXN12345',
    '254708374149',
    'John Doe',
    'January Bill',
    'EXT-001'
);
```
update_invoice_data($payment_date, $paidAmmount, $actReference, $transactionId, $phoneNumber, $fullName, $invoiceName, $reference)

Updates existing invoice data.
```php
$mpesa->update_invoice_data(/* same signature as reconciliation */);
```
$mpesa->cancel_single_invoice($reference)

Cancels a single invoice using its external reference.
```php

$mpesa->cancel_single_invoice('EXT-001');
```
