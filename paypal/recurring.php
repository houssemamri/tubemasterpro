<?php
session_start();
$p = $_GET['p'];
$msg = "";
$baseurl =  "http://" . $_SERVER['SERVER_NAME'];

require __DIR__ . '/sample/bootstrap.php';
use PayPal\Api\Plan;
use PayPal\Api\PaymentDefinition;
use PayPal\Api\MerchantPreferences;
use PayPal\Api\Currency;
use PayPal\Api\ChargeModel;

use PayPal\Api\PatchRequest;
use PayPal\Api\Patch;
use PayPal\Common\PPModel;

use PayPal\Api\Agreement;
use PayPal\Api\Payer;
use PayPal\Api\ShippingAddress;

use PayPal\Api\ExecutePayment;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\Sale;
if($p == "subscribe"){
//create plan 
// Create a new instance of Plan object
$plan = new Plan();

// # Basic Information
// Fill up the basic information that is required for the plan
$plan->setName('Australia WOW Youtube Ads')
    ->setDescription('Your Description here!!!')
    ->setType('INFINITE');

// # Payment definitions for this billing plan.
$paymentDefinition = new PaymentDefinition();

// The possible values for such setters are mentioned in the setter method documentation.
// Just open the class file. e.g. lib/PayPal/Api/PaymentDefinition.php and look for setFrequency method.
// You should be able to see the acceptable values in the comments.
$paymentDefinition->setName('Regular Payments')
    ->setType('REGULAR')
    ->setFrequency('MONTH')
    ->setFrequencyInterval("1")
    ->setCycles("0")
    ->setAmount(new Currency(array('value' => 147, 'currency' => 'USD')));

// Charge Models
    /*
$chargeModel = new ChargeModel();
$chargeModel->setType('SHIPPING')
    ->setAmount(new Currency(array('value' => 10, 'currency' => 'USD')));

$paymentDefinition->setChargeModels(array($chargeModel));
*/
$merchantPreferences = new MerchantPreferences();
$baseUrl = getBaseUrl();
// ReturnURL and CancelURL are not required and used when creating billing agreement with payment_method as "credit_card".
// However, it is generally a good idea to set these values, in case you plan to create billing agreements which accepts "paypal" as payment_method.
// This will keep your plan compatible with both the possible scenarios on how it is being used in agreement.
$merchantPreferences->setReturnUrl("$baseUrl/recurring.php?p=return&success=true")
    ->setCancelUrl("$baseUrl/recurring.php?p=return&success=false")
    ->setAutoBillAmount("yes")
    ->setInitialFailAmountAction("CONTINUE")
    ->setMaxFailAttempts("0")
    ->setSetupFee(new Currency(array('value' => 0, 'currency' => 'USD')));


$plan->setPaymentDefinitions(array($paymentDefinition));
$plan->setMerchantPreferences($merchantPreferences);

// For Sample Purposes Only.
$request = clone $plan;
// ### Create Plan
try {
    $output =  $plan->create($apiContext);
} catch (Exception $ex) {
    ResultPrinter::printError("Created Plan", "Plan", null, $request, $ex);
    exit(1);
}

	if($output){

		$obj = $output->toJSON();
		$paypal_obj = (array) json_decode($obj);
		$_SESSION['plan_id'] = $paypal_obj['id'];
		echo "<script>window.location.replace('$baseurl/recurring.php?p=confirm_plan');</script>";
	}

}
if($p == "confirm_plan")
{
	$plan_id = $_SESSION['plan_id'];
	if($plan_id == ""){
		$p = "";
		$msg = "Invalid PLAN ID, please try again.";
	}else{
		 $plan = Plan::get($plan_id, $apiContext);
		$obj = $plan->toJSON();
		$confirm_obj = (array) json_decode($obj);		 
		$p = "confirm_plan";
	}
}
if($p == "cancel_request"){
	$_SESSION['plan_id'] = "";
	$_SESSION['token'] = "";
	$p = "";
	$msg = "Payment Cancelled";
}
if($p == "confirm_payment"){
	$plan_id = $_SESSION['plan_id'];
	if($plan_id == ""){
		$p = "";
		$msg = "Invalid PLAN ID, please try again.";
	}else{
		$plan = Plan::get($plan_id, $apiContext);

		$obj = $plan->toJSON();
		$confirm_obj = (array) json_decode($obj);

		if($confirm_obj['state'] == "CREATED"){

		    $patch = new Patch();
		    $value = new PPModel('{
			       "state":"ACTIVE"
			     }');

		    $patch->setOp('replace')
		        ->setPath('/')
		        ->setValue($value);
		    $patchRequest = new PatchRequest();
	    	$patchRequest->addPatch($patch);
	    	$plan->update($patchRequest, $apiContext);


		}

		echo "<script>window.location.replace('$baseurl/recurring.php?p=checkout');</script>";
	}	
}
if($p == "checkout"){

	$time_now_month = date("Y-m-d",time());
	$time_now_time = date("H:i:s",time());
	$combine_time =  $time_now_month . "T" . $time_now_time . "Z";

	$plan_id = $_SESSION['plan_id'];
	if($plan_id == ""){
		$p = "";
		$msg = "Invalid PLAN ID, please try again.";
	}else{
			$agreement = new Agreement();
			$agreement->setName('Base Agreement')
			    ->setDescription('Basic Agreement Description')
			    ->setStartDate("$combine_time");

			// Add Plan ID
			// Please note that the plan Id should be only set in this case.
			$plan = new Plan();
			$plan->setId($plan_id);
			$agreement->setPlan($plan);

			// Add Payer
			$payer = new Payer();
			$payer->setPaymentMethod('paypal');
			$agreement->setPayer($payer);

			$agreement = $agreement->create($apiContext);
			$obj = $agreement->toJSON();
			$paypal_obj = (array) json_decode($obj);

			$paypal_url = $paypal_obj['links'][0]->href;
			echo "<script>window.location.replace('". $paypal_url ."');</script>";

	}
}

if($p == "return"){

	$return_type = $_GET['success']; 
	$token = $_GET['token'];
	$plan_id = $_SESSION['plan_id'];
	if($return_type == "true"){
		$_SESSION['token'] = $token;
	    $agreement = new \PayPal\Api\Agreement();
	    $agreement->execute($token, $apiContext);

	    $agreement = \PayPal\Api\Agreement::get($agreement->getId(), $apiContext);
		$obj = $agreement->toJSON();
		$paypal_obj = (array) json_decode($obj);	

		$paypal_return_id = $paypal_obj['id'];	
		$_SESSION['paypal_return_id'] = $paypal_return_id;

		$p ="success_payment";

	}else{
		$_SESSION['plan_id'] = "";
		$p = "";
		$msg = "Payment Cancelled";
	}
}
if($p == "check_plan_details"){

	$plan_id		= $_SESSION['plan_id'];
	$paypal_ret_id 	= $_SESSION['paypal_return_id'];
	
 	$agreement = \PayPal\Api\Agreement::get($paypal_ret_id, $apiContext);
	$obj = $sale->toJSON();
	$paypal_obj = (array) json_decode($obj);
	echo "<pre>";
	print_r($paypal_obj);
	echo "</pre>";

}
?>
<?php if($p == ""){ ?>
<h3>SUBSCRIBE TO AUSTRALIA WOW for $147/ Month</h3>
<?php if($msg){ echo "<h4>$msg</h4>";} ?>
<a href="recurring.php/?p=subscribe">Subscribe now!</a>
<?php } 

if($p == "confirm_plan"){
?>
<h3>SUBSCRIBE TO AUSTRALIA WOW for $147/ Month</h3>
<h4>Confirm your plan</h4>
<p><b>ID:</b> <?php echo $confirm_obj['id'] ?> <br>
<b>Name:</b> Subscription for Australia WOW Youtube Ads <br>
<b>Type:</b> <?php echo $confirm_obj['payment_definitions'][0]->amount->value; ?> USD/ Month <p/>
<p> <a href="recurring.php/?p=cancel_request"><b>Cancel Request</b></a> | <a href="recurring.php/?p=confirm_payment"><b>Confirm payment</b></a> <p/>	
<?php
}
if($p == "checkout"){
?>
<h3>SUBSCRIBE TO AUSTRALIA WOW for $147/ Month</h3>
<h4>Transfering to paypal... please wait</h4>
<?php
}
if($p == "success_payment"){
?>
<h3>THANK YOU FOR SUBSCRIBING!</h3>
<p>
	To test your application, use this id. <br>
	<b>Plan id click to view:</b> <a href="recurring.php/?p=check_plan_details"><?php echo $paypal_return_id ?></a> <br>
</p>

<?php	
}

?>