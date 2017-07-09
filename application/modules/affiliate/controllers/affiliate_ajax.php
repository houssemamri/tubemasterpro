<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Affiliate_ajax extends Ajax_Controller {

	function __construct () {
		parent::__construct();
        $this->load->library('authentication', NULL, 'ion_auth');
        $this->baseurl = $this->config->config['base_url']; 
	}
	
	function email_check () {
		$email = $this->input->post('email');
		if (!$this->ion_auth->email_check($email)) {
			echo true;
		}
		else {
			echo false;
		}
		die();
	}
	
	function signup () {
		$user = $this->ion_auth->user()->row();

		$aff_key = md5(time() . "_" . $user->id);
        $data = array(
            'company'   => $this->input->post('company'),
            'country'   => $this->input->post('country'),
            'mobile' 	=> $this->input->post('mobile'),
            'whatsapp'  => $this->input->post('whatsApp'),
            'website'   => $this->input->post('website'),
            'twitter'   => $this->input->post('twitter'),
            'facebook'  => $this->input->post('fb'),
            'linkedin'  => $this->input->post('ln'),
            'linkedin'  => $this->input->post('ln'),
            'paypal_email'  => $this->input->post('paypal_email'),
            'aff_added'    	=> time(),
            'aff_status'=> 'pending',
          	'is_aff_tos'	=> 0,
		    'is_aff_key'	=> $aff_key
        );
		
        if ( $this->ion_auth->update($user->id, $data) ) {
        	$body  = "<html><body>";
        	$body .= "<h1>Hey Nathan!</h1>";
			$body .= "<p>User ".$user->first_name." with an email of ".$user->email." wants to be an affiliate.</p>";
			$body .= "<p><a href='".base_url('dashboard/affiliates')."'>Manage Affiliates</a></p>";
			$body .= "<p>TubeMasterPro Team</p>";
			$body .= "</body></html>";
			
        	$this->load->library('email');
		
			$config['protocol'] = 'smtp';
            $config['smtp_host'] = 'localhost';
            $config['smtp_port'] = '25';
            $config['charset'] = 'iso-8859-1';
            $config['wordwrap'] = TRUE;
			//$config['mailtype'] = 'html';
			
			$this->email->initialize($config);
		
			$this->email->from($user->email, $user->username);
			$this->email->to('support@tubemasterpro.com');
			
			$this->email->subject('New Affiliate Application');
			$this->email->message($body);	
			$this->email->send();

			/* email confirmation to applicant*/
            $this->email_confirmation();

            $this->response->script("$('#show_success').modal('show')");


        }
        else {
            $this->response->script("$('#show_failed').modal('show')");
        }
	}

	function update_details(){
		$user = $this->ion_auth->user()->row();
		$update_button = $this->input->post('update_button');

		if($update_button == $user->id . "_update_affiliate_account"){
        $data = array(
            'company'   => $this->input->post('company'),
            'country'   => $this->input->post('country'),
            'mobile' 	=> $this->input->post('mobile'),
            'whatsapp'  => $this->input->post('whatsApp'),
            'website'   => $this->input->post('website'),
            'twitter'   => $this->input->post('twitter'),
            'facebook'  => $this->input->post('fb'),
            'linkedin'  => $this->input->post('ln'),
            'linkedin'  => $this->input->post('ln'),
            'paypal_email'  => $this->input->post('paypal_email')
        );
        $this->ion_auth->update($user->id, $data);
        	$this->session->set_flashdata('msg', "Update successful");
        	$this->session->set_flashdata('msg_type', 'success');  
        }
        else{
        	$this->session->set_flashdata('msg', "Error occured, please try again");
        	$this->session->set_flashdata('msg_type', 'danger');  
        }
        redirect('affiliate/dashboard_approved', 'refresh');
	}	

 function email_confirmation(){
    	$user   	=  $this->ion_auth->user()->row();

    	$email_template = "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
<html xmlns=\"http://www.w3.org/1999/xhtml\">
<head>
<!-- If you delete this meta tag, Half Life 3 will never be released. -->
<meta name=\"viewport\" content=\"width=device-width\" />

<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" />
<title>Affiliate Terms and Condition</title>
	
<style>
*{margin:0;padding:0}*{font-family:\"Helvetica Neue\",\"Helvetica\",Helvetica,Arial,sans-serif}img{max-width:100%}.collapse{margin:0;padding:0}body{-webkit-font-smoothing:antialiased;-webkit-text-size-adjust:none;width:100% !important;height:100%}a{color:#2ba6cb} .btn{display: inline-block;padding: 6px 12px;margin-bottom: 0;font-size: 14px;font-weight: normal;line-height: 1.428571429;text-align: center;white-space: nowrap;vertical-align: middle;cursor: pointer;border: 1px solid transparent;border-radius: 4px;-webkit-user-select: none;-moz-user-select: none;-ms-user-select: none;-o-user-select: none;user-select: none;color: #333;background-color: white;border-color: #CCC;} p.callout{padding:15px;background-color:#fafafa;margin-bottom:15px}.callout a{font-weight:bold;color:#2ba6cb}table.social{background-color:#ebebeb}.social .soc-btn{padding:3px 7px;border-radius:2px; -webkit-border-radius:2px; -moz-border-radius:2px; font-size:12px;margin-bottom:10px;text-decoration:none;color:#FFF;font-weight:bold;display:block;text-align:center}a.fb{background-color:#3b5998 !important}a.tw{background-color:#1daced !important}a.gp{background-color:#db4a39 !important}a.ms{background-color:#000 !important}.sidebar .soc-btn{display:block;width:100%}table.head-wrap{width:100%}.header.container table td.logo{padding:15px}.header.container table td.label{padding:15px;padding-left:0}table.body-wrap{width:100%}table.footer-wrap{width:100%;clear:both !important}.footer-wrap .container td.content p{border-top:1px solid #d7d7d7;padding-top:15px}.footer-wrap .container td.content p{font-size:10px;font-weight:bold}h1,h2,h3,h4,h5,h6{font-family:\"HelveticaNeue-Light\",\"Helvetica Neue Light\",\"Helvetica Neue\",Helvetica,Arial,\"Lucida Grande\",sans-serif;line-height:1.1;margin-bottom:15px;color:#000}h1 small,h2 small,h3 small,h4 small,h5 small,h6 small{font-size:60%;color:#6f6f6f;line-height:0;text-transform:none}h1{font-weight:200;font-size:44px}h2{font-weight:200;font-size:37px}h3{font-weight:500;font-size:27px}h4{font-weight:500;font-size:23px}h5{font-weight:900;font-size:17px}h6{font-weight:900;font-size:14px;text-transform:uppercase;color:#444}.collapse{margin:0 !important}p,ul{margin-bottom:10px;font-weight:normal;font-size:14px;line-height:1.6}p.lead{font-size:17px}p.last{margin-bottom:0}ul li{margin-left:5px;list-style-position:inside}ul.sidebar{background:#ebebeb;display:block;list-style-type:none}ul.sidebar li{display:block;margin:0}ul.sidebar li a{text-decoration:none;color:#666;padding:10px 16px;margin-right:10px;cursor:pointer;border-bottom:1px solid #777;border-top:1px solid #fff;display:block;margin:0}ul.sidebar li a.last{border-bottom-width:0}ul.sidebar li a h1,ul.sidebar li a h2,ul.sidebar li a h3,ul.sidebar li a h4,ul.sidebar li a h5,ul.sidebar li a h6,ul.sidebar li a p{margin-bottom:0 !important}.container{display:block !important;max-width:600px !important;margin:0 auto !important;clear:both !important}.content{padding:15px;max-width:600px;margin:0 auto;display:block}.content table{width:100%}.column{width:300px;float:left}.column tr td{padding:15px}.column-wrap{padding:0 !important;margin:0 auto;max-width:600px !important}.column table{width:100%}.social .column{width:280px;min-width:279px;float:left}.clear{display:block;clear:both}@media only screen and (max-width:600px){a[class=\"btn\"]{display:block !important;margin-bottom:10px !important;background-image:none !important;margin-right:0 !important}div[class=\"column\"]{width:auto !important;float:none !important}table.social div[class=\"column\"]{width:auto !important}}
</style>

</head>
 
<body bgcolor=\"#FFFFFF\">

<!-- HEADER -->
<table class=\"head-wrap\" background=\"$this->baseurl/assets/email/border.png\">
	<tr>
		<td></td>
		<td class=\"header container\" >
				
				<div class=\"content\">
					<table>
						<tr>
							<td><a href=\"<?php echo site_url();?>\"><img src=\"$this->baseurl/assets/email/email_logo.png\" height=\"30\" width=\"142\" /></a></td>
							<td align=\"right\"><h6 class=\"collapse\"></h6></td>
						</tr>
					</table>
				</div>
				
		</td>
		<td></td>
	</tr>
</table><!-- /HEADER -->


<!-- BODY -->
<table class=\"body-wrap\">
	<tr>
		<td></td>
		<td class=\"container\" bgcolor=\"#FFFFFF\">

			<div class=\"content\">
			<table>
				<tr>
					<td>
						<h3><br> Hi, $user->first_name  $user->last_name</h3>
						<p class=\"lead\">Hey!
<br><br>
Thanks for applying to become a TubeMasterPro Affiliate - very much appreciated!</p>
						<p>
We're different to all the other Affiliate programs out there, because we treat you as a Valued Partner, rather than a 'Churn n Burn' number. We also pay you an ongoing commission on TubeMasterPro for as long as your Subscriber remains paying their monthly fees. Yes, recurring income for the life of the Client Subscription!
<br><br>
OK, so that's the exciting stuff. Let's give you the few brief \"things\" you need to bear in mind BEFORE your application to us goes in the application system. Remember you're a Partner now, not a number.
<br><br>
YOU AGREE THAT, IF YOUR APPLICATION TO BECOME A TUBEMASTERPRO AFFILIATE IS SUCCESSFUL:
<br><br>
1. You may only promote TubeMasterPro at the standard price of USD247 <br>
2. Commissions set at USD67 per month per subscriber <br>
3. Commission in (2) paid for the life of the valid Client subscription at the rate set in (2) <br>
4. You will be paid all commissions weekly on Monday 6pm Brisbane (Australia) time. <br>
5. We pay commissions to you on the second week of a client payment to us. This is to prevent us from paying you commissions where a Client cancels their subscription under fraudulent circumstances, leaving TubeMasterPro out of pocket. Yay. So we pay them after the second week to combat this scenario. <br>
6. Commission payments are ONLY in PayPal <br>
7. PayPal fees are borne by you <br>
8. We look after the support for the life of the valid Client subscription <br>
9. Where a client initiates a PayPal dispute, we have a very cool \"ChargeBack Engine\" which helps us, in one click, show PayPal Staff that the Subscriber is (most likely) in the wrong. HOWEVER, as you already know, PayPal will still take the monies from TubeMasterPro pending the outcome of their investigation. Once successful resolved, all monies are returned to TubeMasterPro. So in this scenario, we will pay out any commissions to you for that week as normal but we will deduct the chargeback amount equal to the commission that was paid to you from that week's payment to you. As soon as the case is successfully resolved though, monies automatically placed back to you on that week's commission. At all stages, it is VERY clearly indicated what?s going on to you, so you know we?re not being shady! It's also important to note, that this entire process is automated, which is super-quick and without any human error being introduced into the equation. <br>
10. You will be required to maintain a valid monthly TubeMasterPro subscription too, so to continue your ongoing monthly subscriptions being paid to you. We do this because we introduce ongoing features and training, and to ensure our Affiliates enjoy an ongoing profitable relationship with us.  <br>
11. Where an Affiliate has not paid their monthly subscription, we will cease all commission payments from that point onwards. We will clearly show when your subscription date is - located on your Affiliate Dashboard, to ensure you will always know when the payment date is due. We don't want this scenario to be honest - but we realise circumstances change for some people. We'll clearly show your subscription due date to eliminate payment ambiguaty. <br></p>
						<!-- Callout Panel -->
						<p class=\"callout\" align=\"center\">
							<a class=\"btn\" style=\"background-color: #d6e9c6;color:#000;\" href=\"$this->baseurl/confirmation/aff_application/accept/$user->is_aff_key\">Accept Terms and Condition </a> &nbsp; <a class=\"btn\" style=\"background-color: #f2dede;color:#000;\" href=\"$this->baseurl/confirmation/aff_application/reject/$user->is_aff_key\">Reject Terms and Condition </a>
						</p><!-- /Callout Panel -->					
												
						<!-- social & contact -->
						<table class=\"social\" width=\"100%\">
							<tr>
								<td>
									
									<!-- column 1 -->
									<table align=\"left\" class=\"column\">
										<tr>
											<td>				
												
												<h5 class=\"\">Connect with Us:</h5>
												<p class=\"\"><a href=\"http://www.facebook.com/tubemasterpro\" class=\"soc-btn fb\">Facebook</a> <a href=\"http://www.twitter.com/tubemasterpro\" class=\"soc-btn tw\">Twitter</a></p>
						
												
											</td>
										</tr>
									</table><!-- /column 1 -->	
									
									<!-- column 2 -->
									<table align=\"left\" class=\"column\">
										<tr>
											<td>				
																			
												<h5 class=\"\">Contact Info:</h5>												
												<p>
                Email: <strong><a href=\"emailto:support@tubemasterpro.com\">support@tubemasterpro.com</a></strong></p>
                
											</td>
										</tr>
									</table><!-- /column 2 -->
									
									<span class=\"clear\"></span>	
									
								</td>
							</tr>
						</table><!-- /social & contact -->
						
					</td>
				</tr>
			</table>
			</div><!-- /content -->
									
		</td>
		<td></td>
	</tr>
</table><!-- /BODY -->

<!-- FOOTER -->
<table class=\"footer-wrap\">
	<tr>
		<td></td>
		<td class=\"container\">
			
				<!-- content -->
				<div class=\"content\">
				<table>
				<tr>
					<td align=\"center\">
						<p>
						</p>
					</td>
				</tr>
			</table>
				</div><!-- /content -->
				
		</td>
		<td></td>
	</tr>
</table><!-- /FOOTER -->

</body>
</html>";

			$this->load->library('email');
			$config['protocol']  = 'smtp';
			$config['smtp_host'] = 'localhost';
			$config['smtp_port'] = '25';
			$config['mailtype']  = 'html';
			$config['charset']   = 'iso-8859-1';
			$config['wordwrap']  = TRUE;
			
			$this->email->initialize($config);
			$this->email->from('support@tubemasterpro.com', 'TubeMasterPro Support');
			$this->email->to($user->email);
			
			
			$this->email->subject('TubeTarget Pro Affiliate TOS');
			$this->email->message($email_template);	
			$this->email->send();

    }	
}