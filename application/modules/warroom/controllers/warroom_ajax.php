<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
session_start();
class Warroom_ajax extends Ajax_Controller {

	function __construct () {
		parent::__construct();
        $this->load->library('authentication', NULL, 'ion_auth');
        $this->load->model('Logs_model', 'logs'); 

        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');       
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
		$users = $this->ion_auth->users('3')->result();
    


        $username       = strtolower($this->cleanup($this->input->post('first_name'))) . ' ' . strtolower($this->cleanup($this->input->post('last_name')));
        $email          = strtolower($this->cleanup($this->input->post('email')));
        $password       =   $this->cleanup($this->input->post('password'));
        $signuptoken    =   $this->cleanup($this->input->post('signuptoken'));
        $first_name     =   $this->cleanup($this->input->post('first_name'));
        $last_name      =   $this->cleanup($this->input->post('last_name'));

        $this->form_validation->set_rules('first_name', 'first_name', 'required|xss_clean');
        $this->form_validation->set_rules('last_name','last_name', 'required|xss_clean');
        $this->form_validation->set_rules('email','email', 'required|valid_email');
        $this->form_validation->set_rules('password','password', 'required');


        $session_signup_token = $_SESSION['signup_token'];

        if($signuptoken != $session_signup_token){
        	$this->response->script("$('#invalid_token').modal('show')");
           // echo "ERROR|ERROR|Can't process signup at the moment. Please try again later.";
        }else{        

        	if ($this->form_validation->run() == true)
            {
            	$additional_data = array(
                'first_name' => $this->input->post('first_name'),
                'last_name'  => $this->input->post('last_name')
            	);
            	$group = array('3');
    		
		        if ( $this->ion_auth->register($username, $password, $email, $additional_data, $group) ) {
		        
		        	$_SESSION['signup_token'] = "";
		            $this->response->script("$('#show_success').modal('show')");

	                $this->logs->insert_logs("signup", "Warroom Signup Successful!");
		        }
	        		$this->response->script("$('#show_success').modal('show')");
	        	}else{
	        		$this->response->script("$('#signup_error').modal('show')");
	        	}
        	/*
    	//if ( count($users) < 100 ) {
    		
    		$username = strtolower($this->input->post('first_name')) . ' ' . strtolower($this->input->post('last_name'));
            $email    = strtolower($this->input->post('email'));
            $password = $this->input->post('password');

            $additional_data = array(
                'first_name' => $this->input->post('first_name'),
                'last_name'  => $this->input->post('last_name')
            );
            
            $group = array('3');
    		
	        if ( $this->ion_auth->register($username, $password, $email, $additional_data, $group) ) {
	            $this->response->script("$('#show_success').modal('show')");

                $this->logs->insert_logs("signup", "Warroom Signup Successful!");
	        }
	        */
        	$this->response->send();
        }
	}

    function cleanup($word)
    {
        $word = trim($word);
        $word = strip_tags($word, " <STRONG> <EM> <U> <BR> <n> \n ");
        $word = addslashes($word);
        return $word;
    }  	
}