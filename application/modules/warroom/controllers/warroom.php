<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
session_start();
class Warroom extends MY_Controller {

	protected $user;
	public $data;
	
	function __construct() {
        parent::__construct();
        $this->load->library('authentication', NULL, 'ion_auth');
        $this->load->library('template');
        
        $this->load->library('form_validation');
        $this->load->helper('url');

        $this->load->database();

        $this->form_validation->set_error_delimiters($this->config->item('error_start_delimiter', 'ion_auth'), $this->config->item('error_end_delimiter', 'ion_auth'));

        $this->lang->load('auth');
        $this->load->helper('language');
        
        $this->template->set_title('War Room Signup');
        $this->template->add_css('bfh/css/bootstrap-formhelpers.min.css');
        $this->template->add_js('bfh/bootstrap-formhelpers.min.js');
        $this->template->add_js('jquery.validify.js');
        $this->template->add_js('jquery.countdown.min.js');
        $this->template->add_js('modules/warroom.js');
        
		// $this->user = $this->ion_auth->user()->row();
    }
    
    function index () {
	    if ( !$this->ion_auth->logged_in() ) {
            //redirect them to the login page
            redirect('warroom/signup', 'refresh');
        }
        else {
            redirect('dashboard', 'refresh');
        }
    }
    
    function get_users () {
	    $users = $this->ion_auth->users('3')->result();
	    echo count($users);
	    /*
echo '<pre>';
	    print_r($users);
	    echo '</pre>';
*/
    }
    
    function signup () {

    	$signup_token  = md5(time() . "_" . rand(00000,99999));
        $_SESSION['signup_token'] = $signup_token;

	    if ( !$this->ion_auth->logged_in() ) {
	    	$users = $this->ion_auth->users('3')->result();
	    	
	    	//if ( count($users) < 100 ) {
		    	$this->data['title'] = "Special - Sign Up";
		
		        $tables = $this->config->item('tables','ion_auth');
		
		        //validate form input
		        $this->form_validation->set_rules('first_name', $this->lang->line('create_user_validation_fname_label'), 'required|xss_clean');
		        $this->form_validation->set_rules('last_name', $this->lang->line('create_user_validation_lname_label'), 'required|xss_clean');
		        $this->form_validation->set_rules('email', $this->lang->line('create_user_validation_email_label'), 'required|valid_email|is_unique['.$tables['users'].'.email]');
		        //$this->form_validation->set_rules('phone', $this->lang->line('create_user_validation_phone_label'), 'required|xss_clean');
		        $this->form_validation->set_rules('company', $this->lang->line('create_user_validation_company_label'), 'required|xss_clean');
		        $this->form_validation->set_rules('password', $this->lang->line('create_user_validation_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|max_length[' . $this->config->item('max_password_length', 'ion_auth') . ']|matches[password_confirm]');
		        $this->form_validation->set_rules('password_confirm', $this->lang->line('create_user_validation_password_confirm_label'), 'required');
		
		        if ($this->form_validation->run() == true)
		        {
		            $username = strtolower($this->input->post('first_name')) . ' ' . strtolower($this->input->post('last_name'));
		            $email    = strtolower($this->input->post('email'));
		            $password = $this->input->post('password');
		
		            $additional_data = array(
		                'first_name' => $this->input->post('first_name'),
		                'last_name'  => $this->input->post('last_name')
		            );
		            
		            $group = array('3');
		        }
		        if ($this->form_validation->run() == true && $this->ion_auth->register($username, $password, $email, $additional_data, $group))
		        {
		            //check to see if we are creating the user
		            //redirect them back to the admin page

            		$this->session->set_flashdata('message', '<p>Activation Email Sent</p>');
		            //$this->session->set_flashdata('message', $this->ion_auth->messages());
		            redirect("auth", 'refresh');
		        }
		        else
		        {
		            //display the create user form
		            //set the flash data error message if there is one
		            $this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
		
		            $this->data['first_name'] = array(
		                'name'  => 'first_name',
		                'id'    => 'first_name',
		                'type'  => 'text',
		                'class' => 'validify_required validify_alpha',
		                'value' => $this->form_validation->set_value('first_name'),
		            );
		            $this->data['last_name'] = array(
		                'name'  => 'last_name',
		                'id'    => 'last_name',
		                'type'  => 'text',
		                'class' => 'validify_required validify_alpha',
		                'value' => $this->form_validation->set_value('last_name'),
		            );
		            $this->data['email'] = array(
		                'name'  => 'email',
		                'id'    => 'email',
		                'type'  => 'text',
		                'class' => 'validify_required validify_email',
		                'value' => $this->form_validation->set_value('email'),
		            );
		            $this->data['password'] = array(
		                'name'  => 'password',
		                'id'    => 'password',
		                'type'  => 'password',
		                'class' => 'validify_required validify_password',
		                'value' => $this->form_validation->set_value('password'),
		            );
		            $this->data['password_confirm'] = array(
		                'name'  => 'password_confirm',
		                'id'    => 'password_confirm',
		                'type'  => 'password',
		                'class' => 'validify_required validify_password_confirm',
		                'value' => $this->form_validation->set_value('password_confirm'),
		            );
					 $o['signup_token'] = $signup_token;
					 $this->data['o'] = $o;
		            $this->_render_page('warroom/signup', $this->data);
		        }
	        // }
	        // else {
		       //  $this->_render_page('warroom/signup_close', $this->data);
	        // }
	    }
	    else {
            redirect('dashboard', 'refresh');
	    }
    }
    
    function _render_page($view, $data=null, $render=false)
    {

        // $this->viewdata = (empty($data)) ? $this->data: $data;

        // $view_html = $this->load->view($view, $this->viewdata, $render);

        // if (!$render) return $view_html;

        $data = (empty($data)) ? $this->data : $data;
        if ( ! $render)
        {
            $this->load->library('template');

            if ( ! in_array($view, array('auth/index')))
            {
                $this->template->set_layout('pagelet');
            }

            if ( ! empty($data['title']))
            {
                $this->template->set_title($data['title']);
            }

            $this->template->load_view($view, $data);
        }
        else
        {
            return $this->load->view($view, $data, TRUE);
        }
    }
}