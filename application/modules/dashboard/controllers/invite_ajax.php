<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Invite_ajax extends Ajax_Controller {

	function __construct () {
		parent::__construct();
        $this->load->library('authentication', NULL, 'ion_auth');
        $this->load->model('Logs_model', 'logs'); 
        // $this->load->model('target_model');
        // $user = $this->ion_auth->user()->row();
        
        // echo $this->target_model->create();
        // die();
	}

	function send_invites () {
        $user   = $this->ion_auth->user()->row();
		$emails = array(
			$this->input->post('email1'),
			$this->input->post('email2')
		);
		
		$this->load->library('email');
		
		$config['protocol']  = 'smtp';
		$config['smtp_host'] = 'localhost';
		$config['smtp_port'] = '25';
		// $config['smtp_user'] = 'support';
		// $config['smtp_pass'] = 'TripOut2015';
		$config['mailtype']  = 'html';
		$config['charset']   = 'iso-8859-1';
		$config['wordwrap']  = TRUE;
		
		$this->email->initialize($config);
	
		$this->email->from('support@tubemasterpro.com', 'TubeMasterPro');
		$this->email->to($emails);
		
		$subject = ucfirst($user->first_name) . ' ' . ucfirst($user->last_name)  . ' recommended we show you this!';
		$fname   = ucfirst($user->first_name);
		$body = "
			Hooroo! $fname suggested we ping you about our TubeMasterPro software. It's hardcore YouTube&trade; marketing software that finds monetized videos in your niche, and then lets you build your entire targeted YouTube&trade; video campaign in literally, minutes. <br><br>
			Anyhoo, if you're interested in a free 7 day trial, <a style=\"background-color:#5cb85c; padding:5px 10px;border-radius:5px;text-decoration:none;color:white;\" href=\"http://www.tubemasterpro.com\" >HERE'S THE LINK</a> <br><br>
			Cheers!<br><br>
			Nathan Hague<br>
			<a style=\"text-decoration:none;\" href=\"http://www.tubemasterpro.com\" >www.TubeMasterPro.com</a> 
		";
		
		$this->email->subject($subject);
		$this->email->message($body);	
		
		if ( $this->email->send() ) {
			$data = array(
				'has_invites' => 1
			);
			$this->ion_auth->update($user->id, $data);
			echo true;
		}
		else {
			echo false;
		}
		//echo $this->email->print_debugger();
		die();
		// echo json_encode(array( 'error' => 'test'));die();
	}
}