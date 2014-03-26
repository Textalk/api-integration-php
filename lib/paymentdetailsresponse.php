<?php

require_once "responseenvelope.php";
require_once "orderitem.php";
require_once "receiver.php";
require_once "paymentdetails.php";

class PaysonApi_PaymentDetailsResponse {

    protected $responseEnvelope;
    protected $paymentDetails;

    public function __construct($responseData) {
        $this->responseEnvelope = new PaysonApi_ResponseEnvelope($responseData);
        $this->paymentDetails = new PaysonApi_PaymentDetails($responseData);
    }

    /**
     * Returns the response envelope
     *
     * @see ResponseEnvelope
     *
     * @return ResponseEnvelope
     */
    public function getResponseEnvelope() {
        return $this->responseEnvelope;
    }

    /**
     * Returns the payment details from the response
     *
     * @return PaymentDetails
     */
    public function getPaymentDetails() {
        return $this->paymentDetails;
    }

    /**
     * Returns a string representation of the payment details
     *
     * @return string
     */
    public function __toString() {
        return $this->paymentDetails->__toString();
    }

}

?>
