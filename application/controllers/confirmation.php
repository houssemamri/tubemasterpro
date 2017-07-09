<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Confirmation extends MX_Controller {
   
    public $data;

    function __construct() {
        parent::__construct();
        $this->load->library('authentication', NULL, 'ion_auth'); 
    }
    
    function resend () {
        $this->load->database();
	    $this->load->library('email');
	    $email = $this->session->flashdata('activate_email');
	    $sql   = "select id,first_name,activation_code from users where email = '$email' LIMIT 1";
        $query = $this->db->query($sql);
        $user  = '';
        if( $query->num_rows() > 0 ){
        	$user = $query->result()[0];
        	$data = array(
	            'id'         => $user->id,
	            'first_name' => $user->first_name,
	            'activation' => $user->activation_code
	        );
		    
		    $message = $this->load->view($this->config->item('email_templates', 'ion_auth').$this->config->item('email_activate', 'ion_auth'), $data, true);
			
			$config['protocol']  = 'smtp';
			$config['smtp_host'] = 'localhost';
			$config['smtp_port'] = '25';
			// $config['smtp_port'] = '26';
			// $config['smtp_user'] = 'nathan@nathanhague.com';
			// $config['smtp_pass'] = '$Wolfman1';
			$config['mailtype']  = 'html';
			$config['charset']   = 'iso-8859-1';
			$config['wordwrap']  = TRUE;
			
			$this->email->initialize($config);
		    $this->email->clear();
	        $this->email->from($this->config->item('admin_email', 'ion_auth'), $this->config->item('site_title', 'ion_auth'));
	        $this->email->to($email);
	        $this->email->subject($this->config->item('site_title', 'ion_auth') . ' - ' . $this->lang->line('email_activation_subject'));
	        $this->email->message($message);
			
			$this->email->send();
			
			$return = 'Activation email sent! Please check your email. Thanks!';
			$this->session->set_flashdata('message', $return);
        }
        else {
        	$return = 'Email not found!';
			$this->session->set_flashdata('message', $return);
        }
	    
		//redirect('auth/login', 'refresh');
		echo $return;
        die();
    }

    //confirm affiliate confirmation	
	function aff_application(){
		$type   = $this->uri->segment(3);
		$key   = $this->uri->segment(4);
		$sql = "select id,is_aff_tos from users where is_aff_key = '$key' and is_aff_tos = '0' LIMIT 1";
		$check_aff = $this->db->query($sql);
		if($check_aff->num_rows() == 0){
			$this->session->set_flashdata('message', 'You\'re most welcome to use TubeTargetPro as a regular user now though! No worries');
		}else{
			$aff_conf = $check_aff->row_array();
			if($type == "accept"){
				$is_aff_tos = 1;
			}else{
				$is_aff_tos = 2;
			}
		    $data = array(
            'is_aff_tos'   => $is_aff_tos
    	    );
            $this->db->where('id', $aff_conf['id']);
            $this->db->update('users', $data);
            if($is_aff_tos == 1){
				$this->session->set_flashdata('message', 'Thanks for applying.  We will email you once your application is valid or not.');
			}else{
				$this->session->set_flashdata('message', 'You rejected our TOS. <br> You\'re most welcome to use TubeTargetPro as a regular user now though! No worries');
			}
		}
		redirect('auth/login', 'refresh');
	}

}