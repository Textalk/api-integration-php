<?php

class PaysonApi_ResponseEnvelope {
    protected $ack;
    protected $timestamp;
    protected $errors;

    public function __construct($responseData) {
        $this->ack = $responseData["responseEnvelope.ack"];
        $this->timestamp = $responseData["responseEnvelope.timestamp"];
        $this->errors = $this->parseErrors($responseData);
    }

    public function wasSuccessful() {
        return $this->ack === "SUCCESS";
    }

    public function getErrors() {
        return $this->errors;
    }

    public function __toString() {
        return "ack: " . $this->ack . "\n" .
               "timestamp: " . $this->timestamp . "\n";
    }

    private function parseErrors($output) {
        $errors = array();

        $i = 0;
        while(isset($output[sprintf("errorList.error(%d).message", $i)])){
            $errors[$i] = new PaysonApi_Error(
                $output[sprintf("errorList.error(%d).errorId", $i)],
                $output[sprintf("errorList.error(%d).message", $i)],
                    isset($output[sprintf("errorList.error(%d).parameter", $i)]) ?
                            $output[sprintf("errorList.error(%d).parameter", $i)] : null
            );
            $i++;
        }

        return $errors;
    }
}

?>