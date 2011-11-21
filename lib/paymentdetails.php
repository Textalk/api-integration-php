<?php

class PaysonApi_PaymentDetails {
    protected $orderItems;
    protected $receivers;
    protected $token;

    protected $status;
    protected $invoiceStatus;
    protected $guaranteeStatus;
    protected $guaranteeDeadlineTimestamp;

    protected $type;
    protected $currencyCode;
    protected $custom;
    protected $trackingId;
    protected $correlationId;
    protected $purchaseId;
    protected $senderEmail;

    public function __construct($responseData) {
        $this->orderItems = PaysonApi_OrderItem::parseOrderItems($responseData);
        $this->receivers = PaysonApi_Receiver::parseReceivers($responseData);

        $this->token = $responseData["token"];

        $this->status = $responseData["status"];

        if (isset($responseData["invoiceStatus"])){
            $this->invoiceStatus = $responseData["invoiceStatus"];
        }

        if (isset($responseData["guaranteeStatus"])) {
            $this->guaranteeStatus = $responseData["guaranteeStatus"];
        }

        if (isset($responseData["guaranteeDeadlineTimestamp"])){
            $this->guaranteeDeadlineTimestamp = $responseData["guaranteeDeadlineTimestamp"];
        }

        $this->type = $responseData["type"];

        $this->currencyCode = $responseData["currencyCode"];
        $this->custom = $responseData["custom"];
        $this->trackingId = $responseData["trackingId"];
        $this->correlationId = $responseData["correlationId"];
        $this->purchaseId = $responseData["purchaseId"];

        $this->senderEmail = $responseData["senderEmail"];
    }

    /**
     * Get array of OrderItem objects
     *
     * @return array
     */
    public function getOrderItems() {
        return $this->orderItems;
    }

    /**
     * Get array of Receiver objects
     *
     * @return array
     */
    public function getReceivers() {
        return $this->receivers;
    }

    /**
     *
     * @return
     */
    public function getToken() {
        return $this->token;
    }

    /**
     * Get status of the payment
     *
     * @return string
     */
    public function getStatus() {
        return $this->status;
    }

    /**
     * Get type of the payment
     *
     * @return string
     */
    public function getType() {
        return $this->type;
    }

    /**
     * Get currency code of the payment
     *
     * @return string
     */
    public function getCurrencyCode() {
        return $this->currencyCode;
    }

    /**
     * Get custom field of the payment
     *
     * @return string
     */
    public function getCustom() {
        return $this->custom;
    }

    /**
     * Get trackingId field of the payment
     *
     * @return string
     */
    public function getTrackingId() {
        return $this->trackingId;
    }

    /**
     * Get the correlation id for the payment
     *
     * @return
     */
    public function getCorrelationId() {
        return $this->correlationId;
    }

    /**
     * Get purchase id for the payment
     *
     * @return
     */
    public function getPurchaseId() {
        return $this->purchaseId;
    }

    /**
     * Get email address of the sender of the payment
     *
     * @return
     */
    public function getSenderEmail() {
        return $this->senderEmail;
    }

    /**
     * Get the detailed status of an invoice payment
     *
     * @return
     */
    public function getInvoiceStatus() {
        return $this->invoiceStatus;
    }

    /**
     * Get the detailed status of an guarantee payment
     *
     * @return
     */
    public function getGuaranteeStatus() {
        return $this->guaranteeStatus;
    }

    /**
     * Get the next deadline of a guarantee payment
     *
     * @return
     */
    public function getGuaranteeDeadlineTimestamp() {
        return $this->guaranteeDeadlineTimestamp;
    }

    public function __toString() {
        $receiversString = "";
        foreach ($this->receivers as $receiver) {
            $receiversString = $receiversString . "\t". $receiver . "\n";
        }

        $orderItemsString = "";

        foreach ($this->orderItems as $orderItem) {
            $orderItemsString = $orderItemsString . "\t" . $orderItem . "\n";
        }

        return "token: " . $this->token . "\n" .
               "type: " . $this->type . "\n" .
               "status: " . $this->status . "\n" .
               "currencyCode: " . $this->currencyCode . "\n" .
               "custom: " . $this->custom . "\n" .
               "correlationId: " . $this->correlationId . "\n" .
               "purchaseId: " . $this->purchaseId . "\n" .
               "senderEmail: " . $this->senderEmail . "\n" .
               "receivers: \n" . $receiversString .
               "orderItems: \n" . $orderItemsString;
    }
}

?>

