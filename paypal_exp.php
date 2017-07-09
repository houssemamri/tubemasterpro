<?php 
session_start();
error_reporting(0);
$p = $_GET['p'];
$msg = "";
$baseurl =  "http://" . $_SERVER['SERVER_NAME'];

require __DIR__ . '/paypal/sample/bootstrap.php';

use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;

use PayPal\Api\ExecutePayment;
use PayPal\Api\PaymentExecution;

// ### Payer
// A resource representing a Payer that funds a payment
// For paypal account payments, set payment method
// to 'paypal'.

if($p == "create_payment"){

	$payer = new Payer();
	$payer->setPaymentMethod("paypal");

	// ### Itemized information
	// (Optional) Lets you specify item wise
	// information
	$item1 = new Item();
	$item1->setName('Upload video ad production')
	    ->setCurrency('USD')
	    ->setQuantity(1)
	    ->setPrice(397);

	$itemList = new ItemList();
	$itemList->setItems(array($item1));

	// ### Amount
	// Lets you specify a payment amount.
	// You can also specify additional details
	// such as shipping, tax.
	$amount = new Amount();
	$amount->setCurrency("USD")
	    ->setTotal(397);

	// ### Transaction
	// A transaction defines the contract of a
	// payment - what is the payment for and who
	// is fulfilling it. 
	$transaction = new Transaction();
	$transaction->setAmount($amount)
	    ->setItemList($itemList)
	    ->setDescription("Payment description")
	    ->setInvoiceNumber(uniqid());

	// ### Redirect urls
	// Set the urls that the buyer must be redirected to after 
	// payment approval/ cancellation.
	$baseUrl = getBaseUrl();
	$redirectUrls = new RedirectUrls();
	$redirectUrls->setReturnUrl("$baseUrl/paypal_exp.php?p=return&success=true")
	    ->setCancelUrl("$baseUrl/paypal_exp.php?p=return&success=false");

	// ### Payment
	// A Payment Resource; create one using
	// the above types and intent set to 'sale'
	$payment = new Payment();
	$payment->setIntent("sale")
	    ->setPayer($payer)
	    ->setRedirectUrls($redirectUrls)
	    ->setTransactions(array($transaction));

	try {
	    $output = $payment->create($apiContext);
	} catch (Exception $ex) {
	    ResultPrinter::printError("Created Payment Using PayPal. Please visit the URL to Approve.", "Payment", null, $request, $ex);
	    exit(1);
	}

	$obj = $output->toJSON();
	$exp_pay_detail = (array) json_decode($obj);
	
	$_SESSION['exp_pay_detail'] = $exp_pay_detail;
	echo "<script>window.location.replace('$baseurl/subscription/confirm_express_payment');</script>";

}

if($p == "return"){

	$return_type 	= $_GET['success']; 
	$token 			= $_GET['token'];
	$payment_id		= $_GET['paymentId'];
	$det 			= $_SESSION['exp_pay_detail'];


	if (isset($_GET['success']) && $_GET['success'] == 'true') {

		$paymentId = $payment_id;
    	$payment = Payment::get($paymentId, $apiContext);
	    // ### Payment Execute
	    // PaymentExecution object includes information necessary
	    // to execute a PayPal account payment.
	    // The payer_id is added to the request query parameters
	    // when the user is redirected from paypal back to your site
	    $execution = new PaymentExecution();
	    $execution->setPayerId($_GET['PayerID']);

	    // Execute the payment
	    // (See bootstrap.php for more on `ApiContext`)
	    $result = $payment->execute($execution, $apiContext);

		try {
		    $payment = Payment::get($paymentId, $apiContext);
		} catch (Exception $ex) {
		    ResultPrinter::printError("Get Payment", "Payment", null, null, $ex);
		    exit(1);
		}

		$obj = $payment->toJSON();
		$get_return_payment = (array) json_decode($obj);	
		$_SESSION['get_return_payment'] = $get_return_payment;
		echo "<script>window.location.replace('$baseurl/subscription/express_finalized_payment');</script>";
	}else{
		 echo "<script>window.location.replace('$baseurl/subscription/express_cancel_payment');</script>";
	}

}
/*

// ### Get redirect url
// The API response provides the url that you must redirect
// the buyer to. Retrieve the url from the $payment->getLinks()
// method
foreach ($payment->getLinks() as $link) {
    if ($link->getRel() == 'approval_url') {
        $approvalUrl = $link->getHref();
        break;
    }
}

ResultPrinter::printResult("Created Payment Using PayPal. Please visit the URL to Approve.", "Payment", "<a href='$approvalUrl' >$approvalUrl</a>", $request, $payment);

return $payment;
*/
?>