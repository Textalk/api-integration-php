<?php

require_once "responseenvelope.php";

class PaysonApi_PayResponse {

    protected $responseEnvelope;
    protected $token;

    public function __construct($responseData) {
        $this->responseEnvelope = new PaysonApi_ResponseEnvelope($responseData);

        if (isset($responseData["TOKEN"])) {
            $this->token = $responseData["TOKEN"];
        } else {
            $this->token = "";
        }
    }

    /**
     *
     * @return type ResponseEnvelope
     */
    public function getResponseEnvelope() {
        return $this->responseEnvelope;
    }

    public function getToken() {
        return $this->token;
    }

    public function __toString() {

        return $this->responseEnvelope->__toString() .
                "token: " . $this->token;
    }

}

?>
