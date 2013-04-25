<?php

require_once "paysonapiexception.php";
require_once "paysonapierror.php";
require_once "paysoncredentials.php";
require_once "paymentdetailsresponse.php";
require_once "payresponse.php";
require_once "paymentupdateresponse.php";
require_once "validateresponse.php";
require_once 'nvpcodec.php';
require_once 'sender.php';
require_once "paydata.php";
require_once "paymentdetailsdata.php";
require_once "paymentupdatedata.php";

class PaysonApi_PaymentUpdateMethod {
    const CancelOrder = 0;
    const ShipOrder = 1;
    const CreditOrder = 2;
    const Refund = 3;

    public static function ConstantToString($value){
        switch ($value) {
            case self::CancelOrder:
                return "CANCELORDER";
            case self::ShipOrder:
                return "SHIPORDER";
            case self::CreditOrder:
                return "CREDITORDER";
            case self::Refund:
                return "REFUND";
            default:
                throw new PaysonApi_Exception("Invalid constant");
        }
    }
}

class PaysonApi_LocaleCode {
    public static function ConstantToString($value){
        switch (strtoupper($value)) {
            case "SV":
                return "SV";
            case "FI":
                return "FI";
            case "EN":
                return "EN";
            default:
                throw new PaysonApi_Exception("Invalid constant");
        }
    }
}

class PaysonApi_CurrencyCode {
    public static function ConstantToString($value){
        switch (strtoupper($value)) {
            case "SEK":
                return "SEK";
            case "EUR":
                return "EUR";
            default:
                throw new PaysonApi_Exception("Invalid constant");
        }
    }
}

class PaysonApi_FeesPayer {
    public static function ConstantToString($value) {
        switch (strtoupper($value)) {
            case "SENDER":
                return "SENDER";
            case "PRIMARYRECEIVER":
                return "PRIMARYRECEIVER";
            case "EACHRECEIVER":
                return "EACHRECEIVER";
            case "SECONDARYONLY":
                return "SECONDARYONLY";
            default:
                throw new PaysonApi_Exception("Invalid constant");
        }
    }
}

class PaysonApi_FundingConstraint {
    const NONE = 0;
    const CREDITCARD = 1;
    const BANK = 2;
    const INVOICE = 3;

    public static function addConstraintsToOutput($fundingConstraints, &$output){
        $formatString = "fundingList.fundingConstraint(%d).constraint";

        $i = 0;
        foreach($fundingConstraints as $constraint){
            if ($constraint != self::NONE){
                $output[sprintf($formatString, $i)] = self::ConstantToString($constraint);
                $i++;
            }
        }
    }

    public static function ConstantToString($value) {
        switch ($value) {
            case self::BANK:
                return "BANK";
            case self::CREDITCARD:
                return "CREDITCARD";
            case self::INVOICE:
                return "INVOICE";
        }
    }
}

class PaysonApi_GuaranteeOffered {
    public static function ConstantToString($value) {
        switch (strtoupper($value)) {
            case "OPTIONAL":
                return "OPTIONAL";
            case "REQUIRED":
                return "REQUIRED";
            case "NO":
                return "NO";
        }
    }
}

class PaysonApi {

    protected $credentials;

    const PAYSON_WWW_HOST = "https://www.payson.se";
    const PAYSON_WWW_PAY_FORWARD_URL = "/paysecure/?token=%s";

    const PAYSON_API_ENDPOINT = "https://api.payson.se";
    const PAYSON_API_VERSION = "1.0";
    const PAYSON_API_PAY_ACTION = "Pay";
    const PAYSON_API_PAYMENT_DETAILS_ACTION = "PaymentDetails";
    const PAYSON_API_PAYMENT_UPDATE_ACTION = "PaymentUpdate";
    const PAYSON_API_VALIDATE_ACTION = "Validate";

    /**
     * Sets up the PaysonAPI with credentials
     *
     * @param PaysonApi_Credentials $credentials
     */
    public function __construct($credentials){
        if(get_class($credentials) != "PaysonApi_Credentials") {
            throw new PaysonApi_Exception("Parameter must be of type PaysonApi_Credentials");
        }
        $this->credentials = $credentials;
    }

    /**
     * Initializes a payment
     *
     * @param  PayData $payData PayData-object set up with all necessary parameters
     * @return PayResponse
     */
    public function pay($payData)
    {
        $input = $payData->getOutput();
        $postData = PaysonApi_NVPCodec::Encode($input);

        $action = sprintf("/%s/%s/", self::PAYSON_API_VERSION, self::PAYSON_API_PAY_ACTION);

        $returnData = $this->doRequest($action,
                                       $this->credentials,
                                       $postData);

        $decoded = PaysonApi_NVPCodec::Decode($returnData);

        return new PaysonApi_PayResponse($decoded);
    }

    /**
     * Validate an IPN request
     *
     * @param  string $data The complete unaltered POST data from the IPN request by Payson.
     * @return ValidateResponse object
     */
    public function validate($data)
    {
        $action = sprintf("/%s/%s/", self::PAYSON_API_VERSION, self::PAYSON_API_VALIDATE_ACTION);

        $returnData = $this->doRequest($action,
                                       $this->credentials,
                                       $data);

        $decoded = PaysonApi_NVPCodec::Decode($data);

        return new PaysonApi_ValidateResponse($decoded, $returnData);
    }

    /**
     * Gets details about a payment
     *
     * @param  PaymentDetailsData $paymentDetailsData PaymentDetailsData-object set up with all necessary parameters
     * @return PaymentDetailsResponse object
     */
    public function paymentDetails($paymentDetailsData)
    {
        $input = $paymentDetailsData->getOutput();
        $postData = PaysonApi_NVPCodec::Encode($input);

        $action = sprintf("/%s/%s/", self::PAYSON_API_VERSION, self::PAYSON_API_PAYMENT_DETAILS_ACTION);

        $returnData = $this->doRequest($action,
                                       $this->credentials,
                                       $postData);

        $decoded = PaysonApi_NVPCodec::Decode($returnData);

        return new PaysonApi_PaymentDetailsResponse($decoded);
    }

    /**
     * Take an action on a payment such as ship an order etc.
     *
     * @param  PaymentUpdateData $paymentUpdateData PaymentUpdateData-object set up with all necessary parameters
     * @return PaymentUpdateResponse object
     */
    public function paymentUpdate($paymentUpdateData)
    {
        $input = $paymentUpdateData->getOutput();
        $postData = PaysonApi_NVPCodec::Encode($input);

        $action = sprintf("/%s/%s/", self::PAYSON_API_VERSION, self::PAYSON_API_PAYMENT_UPDATE_ACTION);

        $returnData = $this->doRequest($action,
                                       $this->credentials,
                                       $postData);

        $decoded = PaysonApi_NVPCodec::Decode($returnData);

        return new PaysonApi_PaymentUpdateResponse($decoded);
    }

    public function sendIpn($token){
        $input["token"] = $token;
        $postData = PaysonApi_NVPCodec::Encode($input);
        $action = "/1.0/SendIPN/";

        $this->doRequest($action,
                         $this->credentials,
                         $postData);
    }

    /**
     * Get the url to forward the customer to for completion of payment
     *
     * @param  PayResponse $payResponse
     * @return string The URL to forward to
     */
    public function getForwardPayUrl($payResponse){
        return self::PAYSON_WWW_HOST . sprintf(self::PAYSON_WWW_PAY_FORWARD_URL, $payResponse->getToken());
    }

    private function doRequest($url, $credentials, $postData) {

        if (function_exists('curl_exec')) {
			$output = $this->doCurlRequest($url, $credentials, $postData);
            return  $output;
		}

        throw new PaysonApi_Exception("Curl not installed.");
    }

    private function doCurlRequest($url, $credentials, $postData) {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_HTTPHEADER, $credentials->toHeader());
        curl_setopt($ch, CURLOPT_URL, self::PAYSON_API_ENDPOINT .  $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        $result = curl_exec($ch);

        if($result === false){
            die ('Curl error: ' . curl_error($ch));
        }

        $response_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        if($response_code == 200){
            return $result;
        }
        else {
            throw new PaysonApi_Exception("Remote host responded with HTTP response code: " . $response_code);
        }
    }
}

?>