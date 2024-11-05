<?php

namespace Wasksofts\Mpesa_v2;

date_default_timezone_set("Africa/Nairobi");

/**
 * Mpesa API library
 *
 * @package     Mpesa Class
 * @version     1.0.6
 * @license     MIT License Copyright (c) 2017 Wasksofts Technology
 */
class Mpesa
{
    private $msg = '';
    private $security_credential;
    private $consumer_key;
    private $consumer_secret;
    private $transaction_type;
    private $shortcode;
    private $store_number;
    private $pass_key;
    private $initiator_name;
    private $initiator_password;
    private $callback_url;
    private $so_callback_url;
    private $confirmation_url;
    private $validation_url;
    private $b2c_shortcode;
    private $b2b_shortcode;
    private $result_url;
    private $timeout_url;
    private $official_contact;
    private $logo_link;
    private $live_endpoint;
    private $sandbox_endpoint;
    private $env;

    public function __construct()
    {
        $this->live_endpoint = 'https://api.safaricom.co.ke/';
        $this->sandbox_endpoint = 'https://sandbox.safaricom.co.ke/';
    }

    /**
     * Mpesa configuration function
     * 
     * @param string $key
     * @param mixed $value
     * 
     * @return void
     */
    public function config($key, $value)
    {
        switch ($key) {
            case 'consumer_key':
                $this->consumer_key = trim($value);
                break;
            case 'consumer_secret':
                $this->consumer_secret = trim($value);
                break;
            case 'transaction_type':
                $this->transaction_type = $value;
                break;
            case 'store_number':
                $this->store_number = $value;
                break;
            case 'shortcode':
                $this->shortcode = $value;
                break;
            case 'b2c_shortcode':
                $this->b2c_shortcode = $value;
                break;
            case 'b2b_shortcode':
                $this->b2b_shortcode = $value;
                break;
            case 'initiator_name':
                $this->initiator_name = trim($value);
                break;
            case 'initiator_password':
                $this->initiator_password = trim($value);
                break;
            case 'pass_key':
                $this->pass_key = trim($value);
                break;
            case 'security_credential':
                $this->security_credential = $value;
                break;
            case 'callback_url':
                $this->callback_url = $value;
                break;
            case 'so_callback_url':
                $this->so_callback_url = $value;
                break;
            case 'confirmation_url':
                $this->confirmation_url = $value;
                break;
            case 'validation_url':
                $this->validation_url = $value;
                break;
            case 'result_url':
                $this->result_url = $value;
                break;
            case 'timeout_url':
                $this->timeout_url = $value;
                break;
            case 'official_contact':
                $this->official_contact = $value;
                break;
            case 'logo_link':
                $this->logo_link = $value;
                break;
            case 'callback_url':
                $this->callback_url = $value;
                break;
            case 'env':
                $this->env = $value;
                break;
            default:
                echo 'Invalid config key: ' . $key;
                die;
        }
    }

    /** To authenticate your app and get an Oauth access token
     * An access token expires in 3600 seconds or 1 hour
     *
     * @access   private
     * @return   array object
     */
    public function oauth_token()
    {
        $url = $this->env('oauth/v1/generate?grant_type=client_credentials');
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, ['Authorization: Basic ' . base64_encode($this->consumer_key . ':' . $this->consumer_secret)]);
        curl_setopt($curl, CURLOPT_HEADER, FALSE);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, TRUE);

        $curl_response = curl_exec($curl);

        if (curl_errno($curl)) {
            return curl_error($curl);
        }
        curl_close($curl);

        if ($curl_response == true) {
            return isset(json_decode($curl_response)->access_token) ? json_decode($curl_response)->access_token : $curl_response;
        }
    }

    /** C2B enable Paybill and buy goods merchants to integrate to mpesa and receive real time payment notification
     *  C2B register URL API register the 3rd party's confirmation and validation url to mpesa
     *  which then maps these URLs to the 3rd party shortcode whenever mpesa receives atransaction on the shortcode
     *  Mpesa triggers avalidation request against the validation URL and the 3rd party system responds to mpesa 
     *  with a validation response (either success or an error code)
     *
     *  @param  $status Completed/Cancelled
     *  @return json 
     */
    public function register_url($status = 'Cancelled', $version = "v1")
    {
        $curl_post_data = array(
            'ShortCode' => $this->shortcode,
            'ResponseType' => $status,
            'ConfirmationURL' => $this->confirmation_url,
            'ValidationURL' => $this->validation_url
        );

        $url =  $this->env('mpesa/c2b/' . $version . '/registerurl');
        $this->http_post($url, ['Content-Type:application/json', 'charset=utf8', 'Authorization:Bearer ' . $this->oauth_token()], $curl_post_data);
    }


    /** STK Push Simulation lipa na M-pesa Online payment API is used to initiate a M-pesa transaction on behalf of a customer using STK push
     * This is the same technique mySafaricom app uses whenever the app is used to make payments
     *  
     * @param  int  $amount
     * @param  int  $PartyA | The MSISDN sending the funds.
     * @param  int  $AccountReference  | (order id) Used with M-Pesa PayBills
     * @param  string  $TransactionDesc | A description of the transaction.
     * @param  string  $transactionType 'CustomerPayBillOnline' or CustomerBuyGoodsOnline
     * @return  null
     */
    public function STKPush($Amount, $phoneNumberSendingFund, $AccountReference, $TransactionDesc)
    {
        //Fill in the request parameters with valid values     
        $curl_post_data = array(
            'BusinessShortCode' => strtolower($this->transaction_type) === "paybill" ? $this->shortcode : $this->store_number,
            'Password' => $this->password(),
            'Timestamp' => $this->timestamp(),
            'TransactionType' => $this->transactionType(),
            'Amount' => $Amount,
            'PhoneNumber' => $phoneNumberSendingFund,
            'PartyA' => $phoneNumberSendingFund,
            'PartyB' => $this->shortcode,
            'CallBackURL' => $this->callback_url . $AccountReference,
            'AccountReference' => $AccountReference,
            'TransactionDesc' => $TransactionDesc
        );

        $url =  $this->env('mpesa/stkpush/v1/processrequest');
        $header = ['Content-Type:application/json', 'charset=utf8', 'Authorization:Bearer ' . $this->oauth_token()];
        return $this->http_post($url, $header, $curl_post_data);
    }

    /**  transactionType
     * 
     */
    public function transactionType()
    {
        switch (strtolower($this->transaction_type)) {
            case 'paybill':
                return 'CustomerPayBillOnline';
                break;

            case 'buygoods':
                return 'CustomerBuyGoodsOnline';
                break;
        }
    }

    /** STK Push Status Query
     * This is used to check the status of a Lipa Na M-Pesa Online Payment.
     *
     * @param   string  $checkoutRequestID | Checkout RequestID
     * @return  void
     */
    public function STKPushQuery($checkoutRequestID)
    {
        //Fill in the request parameters with valid values        
        $curl_post_data = array(
            'BusinessShortCode' => strtolower($this->transaction_type) == 'paybill' ? $this->shortcode : $this->store_number,
            'Password'  => $this->password(),
            'Timestamp' => $this->timestamp(),
            'CheckoutRequestID' => $checkoutRequestID
        );

        $url =  $this->env('mpesa/stkpushquery/v1/query');
        $header = ['Content-Type:application/json', 'charset=utf8', 'Authorization:Bearer ' . $this->oauth_token()];
        $this->http_post($url, $header, $curl_post_data);
    }

    /** reverses a B2B ,B2C or C2B Mpesa,transaction
     *
     * @access  public
     * @param   int      $amount
     * @param   int      $ReceiverParty
     * @param   int      $TransactionID
     * @param   int      $RecieverIdentifierType
     * @param   string   $Remarks
     * @param   string   $Ocassion
     * @return  null
     */
    public function reversal($Amount, $TransactionID, $Remarks, $result_url = 'reversal', $timeout_url = 'reversal', $Occasion = NULL)
    {
        $curl_post_data = array(
            'Initiator' => $this->initiator_name,
            'SecurityCredential' => $this->security_credential(),
            'CommandID' => 'TransactionReversal',
            'TransactionID' => $TransactionID,
            'Amount' => $Amount,
            'ReceiverParty' => strtolower($this->transaction_type) === 'paybill' ? $this->shortcode : $this->store_number,
            'RecieverIdentifierType' => 11,
            'ResultURL' => $this->result_url . $result_url,
            'QueueTimeOutURL' => $this->timeout_url . $timeout_url,
            'Remarks' => $Remarks,
            'Occasion' => $Occasion
        );

        $url =  $this->env('mpesa/reversal/v1/request');
        $this->http_post($url, ['Content-Type:application/json', 'charset=utf8', 'Authorization:Bearer ' . $this->oauth_token()], $curl_post_data);
    }


    /**  
     * B2C Payment Request transactions betwwen a company and customers 
     * who are the enduser of its products ir services
     * command id SalaryPayment,BussinessPayment ,PromotionPayment
     *
     * @param   int       $amount
     * @param   string    $commandId | Unique command for each transaction type e.g. SalaryPayment, BusinessPayment, PromotionPayment
     * @param   string    $receiver  | Phone number receiving the transaction
     * @param   string    $remark    | Comments that are sent along with the transaction.
     * @param   string    $ocassion  | optional
     * @return  void
     */
    public function b2c($amount, $commandId, $receiver, $remark,  $result_url = 'b2c', $timeout_url = 'b2c', $occassion = null)
    {
        $curl_post_data = array(
            'InitiatorName' => $this->initiator_name,
            'SecurityCredential' => $this->security_credential(),
            'CommandID' => $commandId,
            'Amount' => $amount,
            'PartyA' => $this->b2c_shortcode,
            'PartyB' => $receiver,
            'Remarks' => $remark,
            'QueueTimeOutURL' => $this->timeout_url . $timeout_url,
            'ResultURL' => $this->result_url . $result_url,
            'Occasion' => $occassion
        );

        $url = $this->env('mpesa/b2c/v1/paymentrequest');
        $this->http_post($url, ['Content-Type:application/json', 'charset=utf8', 'Authorization:Bearer ' . $this->oauth_token()], $curl_post_data);
    }

    /** B2B Payment Request transactions between a business and another business
     * Api requires a valid and verifiedB2B Mpesa shortcode for the business initiating the transaction 
     * andthe bothbusiness involved in the transaction
     * Command ID : BussinessPayBill ,MerchantToMerchantTransfer,MerchantTransferFromMerchantToWorking,MerchantServucesMMFAccountTransfer,AgencyFloatAdvance
     *
     * @param  int      $Amount
     * @param  string   $commandId
     * @param  int      $PartyB | Organization’s short code receiving the funds being transacted.
     * @param  int      $SenderIdentifierType | Type of organization sending the transaction. 1,2,4
     * @param  int      $RecieverIdentifierType | Type of organization receiving the funds being transacted. 1,2,4
     * @param  string   $AccountReference | Account Reference mandatory for “BusinessPaybill” CommandID.
     * @param  string   $remarks
     * @return  null 
     */
    public function b2b($Amount, $PartyB, $commandId, $AccountReference, $Remarks, $result_url = 'b2b', $timeout_url = 'b2b')
    {

        $curl_post_data = array(
            'Initiator' => $this->initiator_name,
            'SecurityCredential' => $this->security_credential(),
            'CommandID' => $commandId,
            'SenderIdentifierType' => 4,
            'RecieverIdentifierType' => 4,
            'Amount' => $Amount,
            'PartyA' => strtolower($this->transaction_type) === 'paybill' ? $this->shortcode : $this->b2b_shortcode,
            'PartyB' => $PartyB,
            'AccountReference' => $AccountReference,
            'Remarks' => $Remarks,
            'QueueTimeOutURL' => $this->timeout_url . $timeout_url,
            'ResultURL' => $this->result_url . $result_url
        );

        $url =  $this->env('/mpesa/b2b/v1/paymentrequest');
        $this->http_post($url, ['Content-Type:application/json', 'charset=utf8', 'Authorization:Bearer ' . $this->oauth_token()],  $curl_post_data);
    }

    /** Account Balance API request for account balance of a shortcode
     * 
     * @access  public
     * @param   int     $PartyA | Type of organization receiving the transaction
     * @param   int     $IdentifierType |Type of organization receiving the transaction
     * @param   string  $Remarks | Comments that are sent along with the transaction.
     * @return  null
     */
    public function accountbalance($IdentifierType, $Remarks, $result_url = 'balance', $timeout_url = 'balance')
    {

        //Fill in the request parameters with valid values
        $curl_post_data = array(
            'Initiator' => $this->initiator_name,
            'SecurityCredential' => $this->security_credential(),
            'CommandID' => 'AccountBalance',
            'PartyA' => strtolower($this->transaction_type) === 'paybill' ? $this->shortcode : $this->store_number,
            'IdentifierType' => $IdentifierType,
            'Remarks' => $Remarks,
            'QueueTimeOutURL' => $this->timeout_url . $timeout_url,
            'ResultURL' => $this->result_url . $result_url
        );

        $url =  $this->env('mpesa/accountbalance/v1/query');
        $this->http_post($url, ['Content-Type:application/json', 'charset=utf8', 'Authorization:Bearer ' . $this->oauth_token()], $curl_post_data);
    }

    /** Transaction Status Request API checks the status of B2B ,B2C and C2B APIs transactions
     *
     * @access  public
     * @param   string  $TransactionID | Organization Receiving the funds.
     * @param   int     $PartyA | Organization/MSISDN sending the transaction
     * @param   int     $IdentifierType | Type of organization receiving the transaction 1 – MSISDN 2 – Till Number 4 – Organization short code
     * @param   string  $Remarks
     * @param   string  $Ocassion
     * @return  null
     */
    public function transaction_status($TransactionID,  $Remarks, $indentifier = 4, $result_url = "transaction_status", $timeout_url = "transaction_status", $Occassion = NULL)
    {
        $curl_post_data = array(
            'Initiator' => $this->initiator_name,
            'SecurityCredential' => $this->security_credential(),
            'CommandID' => 'TransactionStatusQuery',
            'TransactionID' => $TransactionID,
            'PartyA' => strtolower($this->transaction_type) === 'paybill' ? $this->shortcode : $this->store_number,
            'IdentifierType' => $indentifier,
            'ResultURL' => $this->result_url . $result_url,
            'QueueTimeOutURL' => $this->timeout_url . $timeout_url,
            'Remarks' => $Remarks,
            'Occasion' => $Occassion
        );

        $url =  $this->env('mpesa/transactionstatus/v1/query');
        $this->http_post($url, ['Content-Type:application/json', 'charset=utf8', 'Authorization:Bearer ' . $this->oauth_token()], $curl_post_data);
    }

    /**
     * QR Code Generate
     * Format of QR output:"1": Image Format."2": QR String Format "3": Binary Data Format."4": PDF Format.
     * Transaction Type. The supported types are: BG: Pay Merchant (Buy Goods).WA: Withdraw Cash at Agent Till.PB: Paybill or Business number.SM: Send Money(Mobile number).SB: Sent to Business. Business number CPI in MSISDN format.
     * 	Credit Party Identifier. Can be a Mobile Number, Business Number, Agent Till, Paybill or Business number, Merchant Buy Goods.
     * 
     * @param QRFormat Format of QR output "1": Image Format. "2": QR String Format "3": Binary Data Format. "4": PDF Format.
     * @param TrxCodeBG BG Pay Merchant (Buy Goods).WA: Withdraw Cash at Agent Till. PB: Paybill or Business number.SM: Send Money(Mobile number).SB: Sent to Business. Business number CPI in MSISDN format.
     * @param Amount  
     * @return Qrformart
     */
    public function generate_qrcode($amount, $reference, $MerchantName = 'SERVICE', $qrformat = 1, $trxcode = 'PG')
    {
        $curl_post_data = array(
            "QRVersion" => "01",
            "QRFormat" => $qrformat,
            "QRType" => "D",
            "MerchantName" => $MerchantName,
            "RefNo" => $reference,
            "Amount" => $amount,
            "TrxCode" => strtolower($this->transaction_type) === 'paybill' ? 'PB' : 'BG',
            "CPI" => strtolower($this->transaction_type) === 'paybill' ? $this->shortcode : $this->store_number
        );

        $url = $this->env('mpesa/qrcode/v1/generate');
        $this->http_post($url, ['Content-Type:application/json', 'charset=utf8', 'Authorization:Bearer ' . $this->oauth_token()], $curl_post_data);
    }

    /**
     * Tax remittance
     * This API enables businesses to remit tax to Kenya Revenue Authority (KRA). To use this API,
     * prior integration is required with KRA for tax declaration,
     * payment registration number (PRN) generation, and exchange of other tax-related information.
     * 
     * @return  null
     */
    public function tax_remittance($amount, $account_prn, $Remarks = "OK", $result_url = 'tax', $timeout_url = 'tax',  $kra_paybill = "572572")
    {
        $curl_post_data = array(
            "Initiator" => $this->initiator_name,
            "SecurityCredential" => $this->security_credential(),
            "CommandID" => "PayTaxToKRA",
            "Amount" => $amount,
            "AccountReference" => $account_prn,
            "SenderIdentifierType" => strtolower($this->transaction_type) === 'paybill' ? "4" : "2",
            "RecieverIdentifierType" => "4",
            "PartyA" => strtolower($this->transaction_type) === 'paybill' ? $this->shortcode : $this->store_number,
            "PartyB" => $kra_paybill,
            "Remarks" => $Remarks,
            'ResultURL' => $this->result_url . $result_url,
            'QueueTimeOutURL' => $this->timeout_url . $timeout_url,
        );

        $url = $this->env('mpesa/b2b/v1/remittax');
        $this->http_post($url, ['Content-Type:application/json', 'charset=utf8', 'Authorization:Bearer ' . $this->oauth_token()], $curl_post_data);
    }

    /**
     * The Standing Order APIs enable teams to integrate with the standing order solution by initiating a request to create a standing order on the customer profile.
     * $startdate YYYYMMDD
     * 
     * @param frequency  1 - One Off 2 - Daily 3 - Weekly 4 - Monthly 5 - Bi-Monthly 6 - Quarterly 7 - Half Year8 - Yearly
     *
     * @return  void
     */
    public function standing_order($name, $start_date, $end_date, $amount, $from, $AccountReference, $TransactionDesc, $Frequency)
    {
        $curl_post_data = array(
            "StandingOrderName" => $name,
            "StartDate" => $start_date,
            "EndDate" => $end_date,
            "BusinessShortCode" => strtolower($this->transaction_type) === 'paybill' ? $this->shortcode : $this->store_number,
            "TransactionType" => "Standing Order Customer Pay Bill",
            "ReceiverPartyIdentifierType" => strtolower($this->transaction_type) === 'paybill' ? "4" : "2",
            "Amount" =>  $amount,
            "PartyA" => $from,
            "CallBackURL" => $this->so_callback_url,
            "AccountReference" => $AccountReference,
            "TransactionDesc" => $TransactionDesc,
            "Frequency" => $Frequency
        );

        $url = $this->env('standingorder/v1/createStandingOrderExternal');
        $this->http_post($url, ['Content-Type:application/json', 'charset=utf8', 'Authorization:Bearer ' . $this->oauth_token()], $curl_post_data);
    }

    //-------------------------------------------------------------------------------------------------------------------------------------
    //Bill manager

    /**
     * Business to optin to biller manager
     * 
     * This is the first API used to opt you as a biller to our bill manager features. 
     * Once you integrate to this API and send a request with a success response, 
     * your shortcode is whitelisted and you are able to integrate with all the other remaining bill manager APIs.
     * 
     * @param $email ,$reminder
     * @return array
     */
    public function optin_biller($email, $reminders = 1)
    {
        $url =  $this->env('v1/billmanager-invoice/change-optin-details');

        //Fill in the request parameters 
        $curl_post_data = array(
            "shortcode" => $this->shortcode,
            "logo" => $this->logo_link,
            "email" => $email,
            "officialContact" => $this->official_contact,
            "sendReminders" => $reminders,
            "callbackUrl" =>  $this->callback_url
        );

        $this->http_post($url, ['Content-Type:application/json', 'charset=utf8', 'Authorization:Bearer ' . $this->oauth_token()], $curl_post_data);
    }

    /** 
     * Modify Onboarding Details
     * This API allows you to update the Onboarding fields. 
     * These are the fields you can update
     * 
     */
    public function optin_update($email, $reminders = 1)
    {
        $url =  $this->env('v1/billmanager-invoice/optin');

        //Fill in the request parameters 
        $curl_post_data = array(
            "shortcode" => $this->shortcode,
            "logo" => $this->logo_link,
            "email" => $email,
            "officialContact" => $this->official_contact,
            "sendReminders" => $reminders,
            "callbackUrl" =>  $this->callback_url
        );

        $this->http_post($url, ['Content-Type:application/json', 'charset=utf8', 'Authorization:Bearer ' . $this->oauth_token()], $curl_post_data);
    }

    /**
     * Bill Manager invoicing service enables you to create and send e-invoices to your customers.
     * Single invoicing functionality will allow you to send out customized individual e-invoices 
     * Your customers will receive this notification(s) via an SMS to the Safaricom phone number specified while creating the invoice.
     */
    public function single_invoice($reference, $billedfullname, $billedphoneNumber, $billedperiod, $invoiceName, $dueDate, $accountRef, $amount)
    {
        $url =  $this->env('v1/billmanager-invoice/single-invoicing');

        //Fill in the request parameters 
        $curl_post_data = array(
            "externalReference" => $reference,
            "billedFullName" => $billedfullname,
            "billedPhoneNumber" => $billedphoneNumber,
            "billedPeriod" => $billedperiod,
            "invoiceName" => $invoiceName,
            "dueDate" => $dueDate,
            "accountReference" => $accountRef,
            "amount" => $amount
        );

        $this->http_post($url, ['Content-Type:application/json', 'charset=utf8', 'Authorization:Bearer ' . $this->oauth_token()], $curl_post_data);
    }

    /**   
     * Bulk invoicing
     *  while bulk invoicing allows you to send multiple invoices.
     * 
     * @param array
     */
    public function bulk_invoicing($invoiceArray)
    {
        $url =  $this->env('v1/billmanager-invoice/bulk-invoicing');
        $this->http_post($url, ['Content-Type:application/json', 'charset=utf8', 'Authorization:Bearer ' . $this->oauth_token()], $invoiceArray);
    }

    /**
     * Reconciliation
     * 
     * @param  string
     * @return array
     */
    public function reconciliation($payment_date, $paidAmmount, $actReference, $transactionId, $phoneNumber, $fullName, $invoiceName, $reference)
    {
        $url =  $this->env('v1/billmanager-invoice/reconciliation');

        //Fill in the request parameters 
        $curl_post_data = array(
            "paymentDate" => $payment_date,
            "paidAmount" => $paidAmmount,
            "accountReference" => $actReference,
            "transactionId" => $transactionId,
            "phoneNumber" => $phoneNumber,
            "fullName" => $fullName,
            "invoiceName" => $invoiceName,
            "externalReference" => $reference
        );

        $this->http_post($url, ['Content-Type:application/json', 'charset=utf8', 'Authorization:Bearer ' . $this->oauth_token()], $curl_post_data);
    }

    /**
     * 
     *  Update invoice API allows you to alter invoice items by using the external reference previously used to create the invoice you want to update.
     *  Any other update on the invoice can be done by using the Cancel Invoice API which will recall the invoice,
     *  then a new invoice can be created. The following changes can be done using the Update Invoice API
     * 
     */

    public function update_invoice_data($payment_date, $paidAmmount, $actReference, $transactionId, $phoneNumber, $fullName, $invoiceName, $reference)
    {
        $url =  $this->env('v1/billmanager-invoice/change-invoice');

        //Fill in the request parameters 
        $curl_post_data = array(
            "paymentDate" => $payment_date,
            "paidAmount" => $paidAmmount,
            "accountReference" => $actReference,
            "transactionId" => $transactionId,
            "phoneNumber" => $phoneNumber,
            "fullName" => $fullName,
            "invoiceName" => $invoiceName,
            "externalReference" => $reference
        );

        $this->http_post($url, ['Content-Type:application/json', 'charset=utf8', 'Authorization:Bearer ' . $this->oauth_token()], $curl_post_data);
    }


    public function cancel_single_invoice($reference)
    {
        $url =  $this->env('v1/billmanager-invoice/cancel-single-invoice');

        //Fill in the request parameters 
        $curl_post_data = array(
            "externalReference" => $reference
        );

        $this->http_post($url, ['Content-Type:application/json', 'charset=utf8', 'Authorization:Bearer ' . $this->oauth_token()], $curl_post_data);
    }

    /** query function
     * 
     * @param  $url
     * @param  $header
     * @param  $$body
     * @return  null
     */
    public function http_post($url, array $header, array $body)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($body));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);

        $curl_response = curl_exec($curl);
        if ($curl_response == true) {
            $this->msg = $curl_response;
        } else {
            $this->msg = curl_error($curl);
        }
        curl_close($curl);
    }

    /** get environment url
     *
     * @access public
     * @param  string $request_url
     * @return string
     */
    public function env($request_url = null)
    {
        if (!is_null($request_url)) {
            if ($this->env === "sandbox") {
                return $this->sandbox_endpoint . $request_url;
            } elseif ($this->env === "production") {
                return $this->live_endpoint . $request_url;
            }
        }
    }

    /** Password for encrypting the request.
     *  This is generated by base64 encoding Bussiness shorgcode passkey and timestamp
     *
     * @access  private
     * @return  string
     */
    public function password()
    {
        $Merchant_id = trim(strtolower($this->transaction_type) === "paybill" ? $this->shortcode : $this->store_number);
        $passkey =  trim($this->pass_key);
        $password =  base64_encode($Merchant_id . $passkey . $this->timestamp());

        return $password;
    }

    /**
     * timestamp for the time of transaction
     */
    public function timestamp()
    {
        return date('YmdHis');
    }

    /**
     * Mpesa authenticate a transaction by decrypting the security credential 
     * Security credentials are generated by encrypting the Base64 encoded string of the M-Pesa short code 
     * and password, which is encrypted using M-Pesa public key and validates the transaction on M-Pesa Core system.
     * 
     * @access  private
     * @return  String
     */
    public function security_credential()
    {
        $publicKey =  $this->env === "production" ? file_get_contents(__DIR__ . '/ProductionCertificate.cer') : file_get_contents(__DIR__ . '/SandboxCertificate.cer');
        $publicKeyResource = openssl_pkey_get_public($publicKey);
        openssl_public_encrypt($this->initiator_password, $encrypted, $publicKeyResource, OPENSSL_PKCS1_PADDING);
        return is_null($this->security_credential) ? base64_encode($encrypted) : $this->security_credential;
    }


    /**
     *  response on api call
     * 
     *  @return data array or json
     */
    public function getResponseData($array = NULL)
    {
        if ($array == TRUE) {
            return $this->msg;
        }
        return json_decode($this->msg);
    }
}
