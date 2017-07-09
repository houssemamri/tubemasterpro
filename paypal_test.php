<?php 
session_start();
error_reporting(0);
$p = $_GET['p'];
$uid = $_GET['uid'];
$user_advance_payer = false;
$msg = "";
$baseurl =  "http://" . $_SERVER['SERVER_NAME'];

require __DIR__ . '/paypal/sample/bootstrap.php';
use PayPal\Api\Plan;
use PayPal\Api\PaymentDefinition;
use PayPal\Api\MerchantPreferences;
use PayPal\Api\Currency;
use PayPal\Api\ChargeModel;

use PayPal\Api\PatchRequest;
use PayPal\Api\Patch;
//use PayPal\Common\PPModel;
use PayPal\Common\PayPalModel;

use PayPal\Api\Agreement;
use PayPal\Api\Payer;
use PayPal\Api\ShippingAddress;

use PayPal\Api\ExecutePayment;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\Sale;

//- connect database users
$servername = "localhost";
$username   = "root";
$password   = "my5QLpw";
$dbname     = "nathann6_tubelf";

//$password 	= "root";
//$dbname		= "myvideoads";
// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

$sql = "SELECT * FROM users,users_groups WHERE users_groups.group_id=2 AND users_groups.user_id=users.id AND users.has_review=1";
$result = $conn->query($sql);
$users = $result->num_rows;

//- Check if client affiliate
$is_client = false;
if ( isset($uid) && !empty($uid) ) {
	$user_sql = "select has_review from users where id = '".$uid."' LIMIT 1";
    $new_res  = $conn->query($user_sql);
    $fetch    = $new_res->fetch_assoc();

	$aff_sql = "select aff_id from affiliates where user_id_aff = '".$uid."' LIMIT 1";
	$check_new_user = $conn->query($aff_sql);
	if( $check_new_user->num_rows > 0 ){
	    $is_client = true;
	}
}

if(!empty($uid)){

	/* check if user has advance payment or use promo code */
	$sql = "select advanced_pay_date, advanced_pay_amt from users where id = '{$uid}' and advanced_pay_amt != '' LIMIT 1";
	$advanced_payer  = $conn->query($sql);
	if($advanced_payer->num_rows > 0){

		$avpayer    	= $advanced_payer->fetch_assoc();
		$unix_pay_day 	= strtotime($avpayer['advanced_pay_date']);
		$unix_time_now	= time();

		if($avpayer['advanced_pay_amt'] != "0" && $unix_pay_day > $unix_time_now){
			$user_advanced_pay_amt 	= $avpayer['advanced_pay_amt'];
			$user_advanced_pay_date	= $avpayer['advanced_pay_date'];
			$user_advance_payer = true;
		}

	}

	//check promo code
	 $date_today = date("Y-m-d",time());
	$sql = "select u.use_promocode,p.option_desc,p.discount_amt,p.promo_code_id,p.is_onetime
	from 
		users as u, 
		promo_code as p,
		promo_code_claimed as pcc
where 
		u.id = '{$uid}' 
	and 
		u.use_promocode = p.promo_code_id 
	and 
		u.use_promocode != '0' 
	and
		pcc.user_id = u.id
	and
		pcc.secret_code_id = p.promo_code_id
	and
	 	p.is_live = '1' and (p.start_date <= '$date_today'  and p.end_date >= '$date_today')
LIMIT 1";

	$check_promocode = $conn->query($sql);
	if($check_promocode->num_rows > 0){
		$cpc = $check_promocode->fetch_assoc();
		
		$_SESSION['promo_code_id'] = $cpc['promo_code_id'];
		$user_use_promocode 	= true;
		$promo_code_desc 		= $cpc['option_desc'];
		$discount_amt			= $cpc['discount_amt'];
		$discount_amt_per		= $discount_amt / 100;
		$is_onetime				= $cpc['is_onetime'];
	}else{
		$discount_amt_per = 0;
		$user_use_promocode = false;
		$_SESSION['promo_code_id'] = 0;
		$is_onetime	 = 0;
	}
}
$conn->close();

if($p == "subscribe"){
//create plan 
// Create a new instance of Plan object
$plan = new Plan();

if($user_advance_payer)
{
	if($user_use_promocode){
		$original_pay_amt		= $user_advanced_pay_amt;
		$new_amount_deduction 	= ($user_advanced_pay_amt * $discount_amt_per);
		$user_advanced_pay_amt	= $original_pay_amt - $new_amount_deduction;
		
		$setDescription = "USD $user_advanced_pay_amt ($discount_amt% Promo Discount from SRP USD $original_pay_amt) for 1 (one) calendar month subscription to TubeMasterPro system.";
	}else{
		$setDescription = 'USD' . $user_advanced_pay_amt .' for 1 (one) calendar month subscription to TubeMasterPro system.';
	}

	$_SESSION['original_payment_amt'] = $user_advanced_pay_amt; //save to session for saving on paypal db.
	$_SESSION['discount_per'] = $discount_amt_per;
	$_SESSION['discount_amt'] = $user_advanced_pay_amt;

	$plan->setName('TubeMasterPro')
	    ->setDescription($setDescription)
	    ->setType('INFINITE');
}else{
	// # Basic Information
	// Fill up the basic information that is required for the plan
	if ( $fetch['has_review'] && $users < 50 && !$is_client ) {
		//$original_pay_amt		= 147;
		$original_pay_amt		= 67;
		if($user_use_promocode){
			
			$new_amount_deduction 	= ($original_pay_amt * $discount_amt_per);
			$user_pay_amt			= $original_pay_amt - $new_amount_deduction;

			$setDescription = "USD $user_pay_amt ($discount_amt% Promo Discount from SRP USD $original_pay_amt) for 1 (one) calendar month subscription to TubeMasterPro system.";
		}else{
			$user_pay_amt = $original_pay_amt;
			$setDescription = "USD $user_pay_amt for 1 (one) calendar month subscription to TubeMasterPro system (Standard Edition).";
		}

		$_SESSION['original_payment_amt'] = $original_pay_amt; //save to session for saving on paypal db.
		$_SESSION['discount_per'] = $discount_amt_per;
		$_SESSION['discount_amt'] = $user_pay_amt;

	$plan->setName('TubeMasterPro')
	    ->setDescription($setDescription)
	    ->setType('INFINITE');
	}
	else {
		//$original_pay_amt		= 247;
		$original_pay_amt		= 67;
		if($user_use_promocode){
			
			$new_amount_deduction 	= ($original_pay_amt * $discount_amt_per);
			$user_pay_amt			= $original_pay_amt - $new_amount_deduction;

			$setDescription = "USD $user_pay_amt ($discount_amt% Promo Discount from SRP USD $original_pay_amt) for 1 (one) calendar month subscription to TubeMasterPro system.";
		}else{
			$user_pay_amt = $original_pay_amt;
			$setDescription = "USD $user_pay_amt for 1 (one) calendar month subscription to TubeMasterPro system.";
		}

		$_SESSION['original_payment_amt'] = $original_pay_amt; //save to session for saving on paypal db.
		$_SESSION['discount_per'] = $discount_amt_per;
		$_SESSION['discount_amt'] = $user_pay_amt;

	$plan->setName('TubeMasterPro')
	    ->setDescription($setDescription)
	    ->setType('INFINITE');
	}
}

// # Payment definitions for this billing plan.
$paymentDefinition = new PaymentDefinition();

// The possible values for such setters are mentioned in the setter method documentation.
// Just open the class file. e.g. lib/PayPal/Api/PaymentDefinition.php and look for setFrequency method.
// You should be able to see the acceptable values in the comments.
if($user_advance_payer)
{
	$paymentDefinition->setName('Regular Payments')
	    ->setType('REGULAR')
	    ->setFrequency('MONTH')
	    ->setFrequencyInterval("1")
	    ->setCycles("0")
	    ->setAmount(new Currency(array('value' => $user_advanced_pay_amt, 'currency' => 'USD')));
}else{

	if ( $fetch['has_review'] && $users < 50 && !$is_client ) {

		if($is_onetime == 1){
			//$user_pay_amt_initial = 147;
			$user_pay_amt_initial = 67;
		}else{
			$user_pay_amt_initial = $user_pay_amt;
		}

	$paymentDefinition->setName('Regular Payments')
	    ->setType('REGULAR')
	    ->setFrequency('MONTH')
	    ->setFrequencyInterval("1")
	    ->setCycles("0")
	    ->setAmount(new Currency(array('value' => $user_pay_amt_initial, 'currency' => 'USD')));
	}
	else {
		if($is_onetime == 1){
			//$user_pay_amt_initial = 247;
			$user_pay_amt_initial = 67;
		}else{
			$user_pay_amt_initial = $user_pay_amt;
		}
	$paymentDefinition->setName('Regular Payments')
	    ->setType('REGULAR')
	    ->setFrequency('MONTH')
	    ->setFrequencyInterval("1")
	    ->setCycles("0")
	    ->setAmount(new Currency(array('value' => $user_pay_amt_initial, 'currency' => 'USD')));
	}
}
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



if($user_advance_payer)
{
	$merchantPreferences->setReturnUrl("$baseUrl/paypal.php?p=return&success=true")
	    ->setCancelUrl("$baseUrl/paypal.php?p=return&success=false")
	    ->setAutoBillAmount("yes")
	    ->setInitialFailAmountAction("CONTINUE")
	    ->setMaxFailAttempts("0")
	    ->setSetupFee(new Currency(array('value' => 0, 'currency' => 'USD')));
}else{

	if ( $fetch['has_review'] && $users < 50 && !$is_client ) {

		$merchantPreferences->setReturnUrl("$baseUrl/paypal.php?p=return&success=true")
		    ->setCancelUrl("$baseUrl/paypal.php?p=return&success=false")
		    ->setAutoBillAmount("yes")
		    ->setInitialFailAmountAction("CONTINUE")
		    ->setMaxFailAttempts("0")
		    ->setSetupFee(new Currency(array('value' => $user_pay_amt, 'currency' => 'USD')));
	}
	else {
	$merchantPreferences->setReturnUrl("$baseUrl/paypal.php?p=return&success=true")
	    ->setCancelUrl("$baseUrl/paypal.php?p=return&success=false")
	    ->setAutoBillAmount("yes")
	    ->setInitialFailAmountAction("CONTINUE")
	    ->setMaxFailAttempts("0")
	    ->setSetupFee(new Currency(array('value' => $user_pay_amt, 'currency' => 'USD')));	
	}
}

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
		
		echo "<script>window.location.replace('$baseurl/paypal.php?p=confirm_plan');</script>";
	}

}
if($p == "confirm_plan")
{
	$plan_id = $_SESSION['plan_id'];

	if($plan_id == ""){
		$p = "";
		$_SESSION['plan_id'] = "";
		$_SESSION['plan_created'] = "";
		die("Invalid PLAN ID, please try again.");
	}else{
		$plan = Plan::get($plan_id, $apiContext);
		$obj = $plan->toJSON();
		$confirm_obj = (array) json_decode($obj);
	
		$_SESSION['plan_created'] = $confirm_obj;
		
		echo "<script>window.location.replace('$baseurl/subscription/confirm_plan');</script>";

	}
}
if($p == "cancel_request"){
	$_SESSION['plan_id'] = "";
	$_SESSION['plan_created'] = "";
	$_SESSION['token'] = "";
	$p = "";
	echo "<script>window.location.replace('$baseurl/subscription/send_confirmation');</script>";

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
		    $value = new PayPalModel('{
			       "state":"ACTIVE"
			     }');

		    $patch->setOp('replace')
		        ->setPath('/')
		        ->setValue($value);
		    $patchRequest = new PatchRequest();
	    	$patchRequest->addPatch($patch);
	    	$plan->update($patchRequest, $apiContext);


		}

		echo "<script>window.location.replace('$baseurl/paypal.php?p=checkout&uid=$uid');</script>";
	}	
}
if($p == "checkout"){

	if($user_advance_payer){
		$str_to_time_month = strtotime($user_advanced_pay_date);
		$time_now_month = date("Y-m-d",$str_to_time_month);
		$time_now_time = date("H:i:s",time());
		$combine_time =  $time_now_month . "T" . $time_now_time . "Z";
	}else{
		$time_now_month = date("Y-m-d H:i:s",mktime(date('H'),date('i'),date('s'),date('m') + 1,date('d'),date('Y')));
		$combine_time = gmdate("Y-m-d\TH:i:s\Z", strtotime($time_now_month)); 
	}

	$plan_id = $_SESSION['plan_id'];
	if($plan_id == ""){
		$p = "";
		$msg = "Invalid PLAN ID, please try again.";
	}else{

			$agreement = new Agreement();
			$agreement->setName('TubeMasterPro')
			    ->setDescription('One calendar month subscription to TubeMasterPro system. Thanks heaps!')
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

			// ### Create Agreement
			try {
			    // Please note that as the agreement has not yet activated, we wont be receiving the ID just yet.
				$agreement = $agreement->create($apiContext);
				$obj = $agreement->toJSON();
				$paypal_obj = (array) json_decode($obj);

			    // ### Get redirect url
			    // The API response provides the url that you must redirect
			    // the buyer to. Retrieve the url from the $agreement->getApprovalLink()
			    // method
			    $approvalUrl = $agreement->getApprovalLink();

			} catch (Exception $ex) {
			    ResultPrinter::printError("Created Billing Agreement.", "Agreement", null, $request, $ex);
			    exit(1);
			}


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

		echo "<script>window.location.replace('$baseurl/paypal.php?p=check_plan_details');</script>";

	}else{
		$_SESSION['plan_id'] = "";
		$_SESSION['plan_created'] = "";
		$_SESSION['token'] = "";
		$p = "";
		echo "<script>window.location.replace('$baseurl/subscription/send_confirmation');</script>";
	}
}
if($p == "check_plan_details"){

	$plan_id		= $_SESSION['plan_id'];
	$paypal_ret_id 	= $_SESSION['paypal_return_id'];
	
 	$agreement = \PayPal\Api\Agreement::get($paypal_ret_id, $apiContext);
	$obj = $agreement->toJSON();
	$paypal_obj = (array) json_decode($obj);
	$_SESSION['final_details'] = $paypal_obj;
	echo "<script>window.location.replace('$baseurl/subscription/final_details');</script>";
}

if($p == "confirm_cancel_payment"){
	$plan_id		= $_SESSION['plan_id'];
	$return_id		= $_SESSION['return_id'];

	if($plan_id == ""){
		$p = "";
		die("Invalid PLAN ID, please try again.");
	}else{
		$plan = Plan::get($plan_id, $apiContext);

		$obj = $plan->toJSON();
		$confirm_obj = (array) json_decode($obj);

		if($confirm_obj['state'] == "ACTIVE"){

		    $patch = new Patch();
		    $value = new PayPalModel('{
			       "state":"DELETED"
			     }');

		    $patch->setOp('replace')
		        ->setPath('/')
		        ->setValue($value);
		    $patchRequest = new PatchRequest();
	    	$patchRequest->addPatch($patch);
	    	$plan->update($patchRequest, $apiContext);

	    	$_SESSION['plan_state'] = "DELETED";
		}

		echo "<script>window.location.replace('$baseurl/subscription/cancel_complete');</script>";
	}
}

if($p == "check_details"){
	$plan_id		= $_GET['id'];
	if($plan_id == ""){

		die("Invalid PLAN ID, please try again.");
	}else{
		$plan = Plan::get($plan_id, $apiContext);

		$obj = $plan->toJSON();
		$confirm_obj = (array) json_decode($obj);

		echo $confirm_obj['state'];

	}	
}


if($p == "check_billing_details"){

	$plan_id		= $_GET['id'];
	
	if($plan_id == ""){
		die("Invalid PLAN ID, please try again.");
	}else{
		$plan = Plan::get($plan_id, $apiContext);

		$obj = $plan->toJSON();
		$confirm_obj = (array) json_decode($obj);

		echo "<pre>";
		print_r($confirm_obj);
		echo "</pre>";

	}	
}

if($p == "check_billing_agreement"){

	$plan_id		= $_GET['id'];
	$page			= $_GET['page'];
	if($plan_id == ""){
		die("Invalid PLAN ID, please try again.");
	}else{
		 
		$agreement = \PayPal\Api\Agreement::get($plan_id, $apiContext);
		$obj = $agreement->toJSON();
		$paypal_obj = (array) json_decode($obj);
		echo "<pre>";
		print_r($paypal_obj);
				echo "<hr>";
		$params = array('page_size' => '20', 'status' => 'ACTIVE', 'page' => $page);
		$planList = Plan::all($params, $apiContext);
			$confirm_obj = (array) json_decode($planList);
		print_r($confirm_obj);
		echo "</pre>";

	}

}

if($p == "check_billing_transaction"){
	$plan_id		= $_GET['id'];
	// Adding Params to search transaction within a given time frame.
	$params = array('start_date' => date('Y-m-d', strtotime('-15 years')), 'end_date' => date('Y-m-d', strtotime('+5 days')));
	if($plan_id == ""){
		die("Invalid PLAN ID, please try again.");
	}else{
		$result = Agreement::searchTransactions($plan_id, $params, $apiContext);
		$obj = $result->toJSON();
		$paypal_obj = (array) json_decode($obj);

		echo $obj;
		
	}

}
if($p == "get_list_payment"){
	$params = array('count' => 10, 'start_index' => 5);
    $payments = Payment::all($params, $apiContext);
	$obj = $payments->toJSON();
	$paypal_obj = (array) json_decode($obj);

	echo $payments;    
}
if($p == "test_get"){
	$url		= $_GET['url'];

	if($url == ""){
		die("No URL Found...");
	}else{
		 
		if (!$data = file_get_contents("$url")) {
		      $error = error_get_last();
		      echo "HTTP request failed. Error was: " . $error['message'];
		} else {
		      echo "Everything went better than expected";
		}

		echo "<hr>";
		echo "CURL TEST <Br>";
		   $ch = curl_init();
	    curl_setopt($ch, CURLOPT_HEADER, 0);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($ch, CURLOPT_URL, $url);

	    $data = curl_exec($ch);
	    curl_close($ch);

	    print_r($data);
	}

}
?>