<?php

session_start();
error_reporting(E_ALL);
ini_set("display_errors", 1);

require '../lib/paysonapi.php';
// Your agent ID and md5 key
$agentID = "1";
$md5Key = "fddb19ac-7470-42b6-a91d-072cb1495f0a";

// Get the POST data
$postData = file_get_contents("php://input");

file_put_contents("test.txt", $postData);

// Set up API
$credentials = new PaysonApi_PaysonCredentials($agentID, $md5Key);
$api = new PaysonApi($credentials, TRUE);

// Validate the request
$response = $api->validate($postData);

if ($response->isVerified()) {
    // IPN request is verified with Payson
    // Check details to find out what happened with the payment
    $details = $response->getPaymentDetails();

    // After we have checked that the response validated we have to check the actual status
    // of the transfer
    if ($details->getType() == "TRANSFER" && $details->getStatus() == "COMPLETED") {
        // Handle completed card & bank transfers here
    } elseif ($details->getType() == "INVOICE" && $details->getStatus() == "PENDING" && $details->getInvoiceStatus() == "ORDERCREATED") {
        // Handle accepted invoice purchases here
    } else if ($details->getStatus() == "ERROR") {
        // Handle errors here
    }
    /*
      //More info
      $response->getPaymentDetails()->getCustom();
      $response->getPaymentDetails()->getShippingAddressName();
      $response->getPaymentDetails()->getShippingAddressStreetAddress();
      $response->getPaymentDetails()->getShippingAddressPostalCode();
      $response->getPaymentDetails()->getShippingAddressCity();
      $response->getPaymentDetails()->getShippingAddressCountry();
      $response->getPaymentDetails()->getToken();
      $response->getPaymentDetails()->getType();
      $response->getPaymentDetails()->getStatus();
      $response->getPaymentDetails()->getCurrencyCode();
      $response->getPaymentDetails()->getTrackingId();
      $response->getPaymentDetails()->getCorrelationId();
      $response->getPaymentDetails()->getPurchaseId();
      $response->getPaymentDetails()->getSenderEmail();
      $response->getPaymentDetails()->getInvoiceStatus();
      $response->getPaymentDetails()->getGuaranteeStatus();
      $details->getReceiverFee();
     */
}
?>
