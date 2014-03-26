<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

require '../lib/paysonapi.php';
// Your agent ID and md5 key
$agentID = "1";
$md5Key = "fddb19ac-7470-42b6-a91d-072cb1495f0a";

// Fetch the token that are returned
$token = $_GET["TOKEN"];

// Initialize the API in test mode
$credentials = new PaysonApi_PaysonCredentials($agentID, $md5Key);
$api = new PaysonApi($credentials, TRUE);

// Get the details about this purchase
$detailsResponse = $api->paymentDetails(new PaysonApi_PaymentDetailsData($token));

// First we verify that the call to payson succeeded.
if ($detailsResponse->getResponseEnvelope()->wasSuccessful()) {

    // Get the payment details from the response
    $details = $detailsResponse->getPaymentDetails();

    // If the call to Payson went well we also have to check the actual status
    // of the transfer
    if ($details->getType() == "TRANSFER" && $details->getStatus() == "COMPLETED") {
        echo "Purchase has been completed <br /><h4>Details</h4><pre>" . $details . "</pre>";
    } elseif ($details->getType() == "INVOICE" && $details->getStatus() == "PENDING" && $details->getInvoiceStatus() == "ORDERCREATED") {
        echo "Invoice has been created <br /><h4>Details</h4><pre>" . $details . "</pre>";
    } else if ($details->getStatus() == "ERROR") {
        echo "An error occured has occured <br /><h4>Details</h4><pre>" . $details . "</pre>";
    }

    /* Below are the other data that can be retreived from payment details
      $details->getCustom();
      $details->getShippingAddressName();
      $details->getShippingAddressStreetAddress();
      $details->getShippingAddressPostalCode();
      $details->getShippingAddressCity();
      $details->getShippingAddressCountry();
      $details->getToken();
      $details->getType();
      $details->getStatus();
      $details->getCurrencyCode();
      $details->getTrackingId();
      $details->getCorrelationId();
      $details->getPurchaseId();
      $details->getSenderEmail();
      $details->getInvoiceStatus();
      $details->getGuaranteeStatus();
      $details->getReceiverFee();
     *
     */
} else {
    $detailsErrors = $detailsResponse->getResponseEnvelope()->getErrors();
    ?>
    <h3>Error</h3>
    <table>
        <tr>
            <th>
                Error id
            </th>

            <th>
                Message
            </th>

            <th>
                Parameter
            </th>
        </tr>
        <?php
        foreach ($detailsErrors as $error) {
            echo "<tr>";
            echo "<td>" . $error->getErrorId() . '</td>';
            echo "<td>" . $error->getMessage() . '</td>';
            echo "<td>" . $error->getParameter() . '</td>';
            echo "</tr>";
        }
        ?>
    </table>
    <?php
}
?>
