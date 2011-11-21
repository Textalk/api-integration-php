<?php

require_once "responseenvelope.php";

class PaysonApi_PaymentUpdateResponse {
    protected $responseEnvelope;

    public function __construct($responseData) {
        $this->responseEnvelope = new PaysonApi_ResponseEnvelope($responseData);
    }

    public function getResponseEnvelope() {
        return $this->responseEnvelope;
    }

    public function __toString() {
        return $this->responseEnvelope->__toString();
    }
}


?>
