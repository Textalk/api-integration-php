<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

/*
 * Payson API Integration example for PHP
 *
 * More information can be found att https://api.payson.se
 *
 */

/*
 * On every page you need to use the API you
 * need to include the file lib/paysonapiTest.php
 * from where you installed it.
 */

require_once '../lib/paysonapiTest.php';

/* Every interaction with Payson goes through the PaysonApi object which you set up as follows */
$credentials = new PaysonCredentials("3", "54e5eb76-3888-4673-a5ba-be2f4187c3d5");
$api = new PaysonApi($credentials);

/*
 * To initiate a direct payment the steps are as follows
 *  1. Set up the details for the payment
 *  2. Initiate payment with Payson
 *  3. Verify that it suceeded
 *  4. Forward the user to Payson to complete the payment
 */

/*
 * Step 1: Set up details
 */
// URLs to which Payson sends the user depending on the success of the payment
$returnUrl = "http://". $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']."return.php";
$cancelUrl = "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']."cancel.php";
// URL to which Payson issues the IPN
$ipnUrl = "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']."ipnTest.php";

// Details about the receiver
$receiver = new Receiver(
    "test@payson.se", // The email of the account to receive the money
    105); // The amount you want to charge the user, here in SEK (the default currency)
$receivers = array($receiver);

// Details about the user that is the sender of the money
$sender = new Sender("test-shopper@payson.se", "Firsname", "Lastname");

//print("\nPay:\n");

$payData = new PayData($returnUrl, $cancelUrl, $ipnUrl, "description", $sender, $receivers);

//Set the list of products
//$orderItems = array(new OrderItem("Book collection", 80, 1, 0.25, "001"), new OrderItem("Shipping", 5, 1, 0.0, "002"));
//$payData->setOrderItems($orderItems);

//Set the payment method
$constraints = array(FundingConstraint::BANK);
$payData->setFundingConstraints($constraints);

//Set the payer of Payson fees
$payData->setFeesPayer("PRIMARYRECEIVER");

// Set currency code
$payData->setCurrencyCode("EUR");

// Set locale code
$payData->setLocaleCode("EN");

// Set guarantee options
$payData->setGuaranteeOffered("OPTIONAL");

/*
 * Step 2 initiate payment
 */
$payResponse = $api->pay($payData);


/*
 * Step 3: verify that it suceeded
 */
if ($payResponse->getResponseEnvelope()->wasSuccessful())
{
    /*
     * Step 4: forward user
     */
    header("Location: " . $api->getForwardPayUrl($payResponse));
}else{
    print_r($payResponse->getResponseEnvelope()->getErrors());
	
}
?>