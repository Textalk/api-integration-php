<?php
require_once '../lib/paysonapiTest.php';

$your_user_id = "3";
$your_user_key = "54e5eb76-3888-4673-a5ba-be2f4187c3d5";

$postData = file_get_contents("php://input");

// Set up API
$credentials = new PaysonCredentials($your_user_id, $your_user_key);
$api = new PaysonApi($credentials);

// Validate the request
$response =  $api->validate($postData);

if($response->isVerified()){
    // IPN request is verified with Payson

    // Check details to find out what happened with the payment
    $details = $response->getPaymentDetails();
    myFile($response->getPaymentDetails()->getToken());
    myFile($response->getPaymentDetails()->getType());
    myFile($response->getPaymentDetails()->getStatus());
    myFile($response->getPaymentDetails()->getTrackingId());
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
	*/
}

function myFile($arg, $arg2 = NULL) {
	    $myFile = "ipnAnrop.txt";
	    if($myFile == NULL){
	    	$myFile =  fopen($myFile, "w+");
	    	fwrite($fh, "\r\n".date("Y-m-d H:i:s"));   
	    }
	    $fh = fopen($myFile, 'a') or die("can't open file");
	    fwrite($fh, "\r\n".date("Y-m-d H:i:s")." **");
	    fwrite($fh, $arg.'**');
	    fwrite($fh, $arg2);
	    fclose($fh);
}	
?>
