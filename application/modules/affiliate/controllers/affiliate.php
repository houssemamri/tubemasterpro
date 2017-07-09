<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Affiliate extends MY_Controller {

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
        
        $this->template->set_title('Affiliate Signup');
        $this->template->add_css('bfh/css/bootstrap-formhelpers.min.css');
        $this->template->add_css('intl-tel-input/intlTelInput.css');
        $this->template->add_js('bfh/bootstrap-formhelpers.min.js');
        $this->template->add_js('intl-tel-input/intlTelInput.min.js');
        $this->template->add_js('jquery.validify.js');
        $this->template->add_js('jquery.countdown.min.js');
        $this->template->add_js('modules/affiliate.js');
        
		// $this->user = $this->ion_auth->user()->row();
		$this->check_permission();
		$this->baseurl = $this->config->config['base_url']; 

		$user = $this->ion_auth->user()->row();

		if($user->su_is_aff == 1){
			redirect('dashboard', 'refresh');
		}
    }
    
    function index () {
	    if ( !$this->ion_auth->logged_in() ) {
            //redirect them to the login page
            redirect('auth/signup', 'refresh');
        }
        else {
            redirect('affiliate/dashboard', 'refresh');
        }
    }
    
    function get_users () {
	    $users = $this->ion_auth->users('3')->result();
	  
    }
    
    function signup () {
	    if ( $this->ion_auth->logged_in() ) { // && !$this->ion_auth->in_group(3) ) {
	    	$user = $this->ion_auth->user()->row();

	    	if($user->is_aff_tos == '2'){
	    		$o['msg_type'] = 'danger'; 
	    		$o['msg'] = "Sorry you cannot apply anymore, you decline our Terms and Condition";

		        $o['pending_account_table'] = true; 
		        $this->data['o'] = $o;
		        $this->_render_page('affiliate/dashboard', $this->data);

	    	}elseif ( !$user->is_aff && !$user->aff_status) {
		    	$this->data['title'] = "Affiliate - Sign Up";
		    	$this->data['user'] = $user;
		
		        $tables = $this->config->item('tables','ion_auth');
		
		        //validate form input
	
		        $this->form_validation->set_rules('mobile', $this->lang->line('create_user_validation_mobile_label'), 'required|xss_clean');
		        $this->form_validation->set_rules('whatsApp', $this->lang->line('create_user_validation_whatsApp_label'), 'required|xss_clean');
		        $this->form_validation->set_rules('website', $this->lang->line('create_user_validation_website_label'), 'required|xss_clean');
		        $this->form_validation->set_rules('twitter', $this->lang->line('create_user_validation_twitter_label'), 'required|xss_clean');
		        $this->form_validation->set_rules('fb', $this->lang->line('create_user_validation_fb_label'), 'required|xss_clean');
		        $this->form_validation->set_rules('ln', $this->lang->line('create_user_validation_ln_label'), 'required|xss_clean');
				$this->form_validation->set_rules('paypal_email', $this->lang->line('create_user_validation_email_label'), 'required|valid_email|is_unique['.$tables['users'].'.paypal_email]');

		        if ($this->form_validation->run() == true)
		        {
		            $username = strtolower($this->input->post('first_name')) . ' ' . strtolower($this->input->post('last_name'));
		            $email    = strtolower($this->input->post('email'));
		
		            $additional_data = array(
		                'first_name' 	=> $this->input->post('first_name'),
		                'last_name'  	=> $this->input->post('last_name'),
		                'company'    	=> $this->input->post('company'),
		                'country'    	=> $this->input->post('country'),
		                'paypal_email'  => $this->input->post('paypal_email'),
		                'aff_added'    	=> time()
		            );
		        }

		        if ($this->form_validation->run() == true && $this->ion_auth->register($username, $password, $email, $additional_data))
		        {
		            //check to see if we are creating the user
		            //redirect them back to the admin page
		            $this->session->set_flashdata('message', $this->ion_auth->messages());
		            redirect("auth", 'refresh');
		        }
		        else
		        {
		            //display the create user form
		            //set the flash data error message if there is one
		            $this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
					
		            $this->data['company'] = array(
		                'name'  => 'company',
		                'id'    => 'company',
		                'type'  => 'text',
		                'class' => 'validify_required validify_alpha',
		                'value' => $this->form_validation->set_value('company'),
		            );
		            $this->data['country'] = array(
		                'name'  => 'country',
		                'id'    => 'country',
		                'type'  => 'select',
		                'class' => 'validify_required',
		                'value' => $this->form_validation->set_value('country'),
		            );
		            $this->data['mobile'] = array(
		                'name'  => 'mobile',
		                'id'    => 'mobile',
		                'type'  => 'text',
		                'class' => 'validify_required',
		                'value' => $this->form_validation->set_value('mobile'),
		            );
		            $this->data['whatsApp'] = array(
		                'name'  => 'whatsApp',
		                'id'    => 'whatsApp',
		                'type'  => 'text',
		                'class' => 'validify_numeric',
		                'value' => $this->form_validation->set_value('whatsApp'),
		            );		            		            		            
		            $this->data['email'] = array(
		                'name'  => 'email',
		                'id'    => 'email',
		                'type'  => 'text',
		                'class' => 'validify_required validify_email',
		                'value' => $this->form_validation->set_value('email'),
		            );
		            $this->data['website'] = array(
		                'name'  => 'website',
		                'id'    => 'website',
		                'type'  => 'text',
		                'class' => 'validify_required validify_website',
		                'data-toggle'	=> 'popover',
		                'data-placement'=> 'top',
		                'data-trigger'	=> 'focus',
		                'data-content'  => 'http:// or https://www.yourdomainname.com',
		                'value' => $this->form_validation->set_value('website'),
		            );
		            $this->data['twitter'] = array(
		                'name'  => 'twitter',
		                'id'    => 'twitter',
		                'type'  => 'text',
		                'class' => 'validify_required validify_twitter',
		                'data-toggle'	=> 'popover',
		                'data-placement'=> 'top',
		                'data-trigger'	=> 'focus',
		                'data-content' 	=> 'http:// or https://twitter.com/youridhere',
		                'value' => $this->form_validation->set_value('twitter'),
		            );
		            $this->data['fb'] = array(
		                'name'  => 'fb',
		                'id'    => 'fb',
		                'type'  => 'text',
		                'class' => 'validify_required validify_facebook',
		                'data-toggle'	=> 'popover',
		                'data-placement'=> 'top',
		                'data-trigger'	=> 'focus',
		                'data-content'  => 'http:// or https://www.facebook.com/youridhere',
		                'value' => $this->form_validation->set_value('fb'),
		            );
		            $this->data['ln'] = array(
		                'name'  => 'ln',
		                'id'    => 'ln',
		                'type'  => 'text',
		                'class' => 'validify_required',
		                'data-toggle'	=> 'popover',
		                'data-placement'=> 'top',
		                'data-trigger'	=> 'focus',
		                'data-content'	=> 'Your LinkedIn Profile URL',
		                'value' => $this->form_validation->set_value('ln'),
		            );
				    
				    $this->data['paypal_email'] = array(
		                'name'  => 'paypal_email',
		                'id'    => 'paypal_email',
		                'type'  => 'text',
		                'class' => 'validify_required validify_email',
		                'placeholder' => 'Your paypal email address',
		                'value' => $this->form_validation->set_value('paypal_email'),
		            );
		
		            $this->_render_page('affiliate/signup', $this->data);
		        }
	        }
	        else {
		        redirect('affiliate/dashboard', 'refresh');
	        }

	    }
	    else {
            redirect('dashboard', 'refresh');
	    }
    }
    
    public function dashboard(){

    	$this->template->set_title('Affiliate Dashboard');
        $user = $this->ion_auth->user()->row();

        if($user->aff_status == ''){
        	 redirect("affiliate/signup", 'refresh'); 
        }
    	if($user->aff_status == 'pending' and $user->is_aff_tos == '1'){
    		$aff_add_date = date("F d, Y", $user->aff_added);
    		$o['msg_type'] = 'info'; 
    		$o['msg'] = "you applied to become an affiliate on $aff_add_date.
				<br><br>We are currently processing your application";
	        $o['pending_account_table'] = true; 
	        $this->data['o'] = $o;
	        $this->_render_page('affiliate/dashboard', $this->data);

    	}

    	if($user->aff_status == 'pending' and $user->is_aff_tos == '0'){
    		$aff_add_date = date("F d, Y", $user->aff_added);
    		$o['msg_type'] = 'danger'; 
    		$o['msg'] = "We have emailed you our Terms and Condition for Affiliate application
				<br><br>Please review and accept our terms and condition. <br> <br> 
				Did not received the email? <a href='" . $this->baseurl. "affiliate/send_aff_confirmation'>Click Here</a> to resend.";
	        $o['pending_account_table'] = true; 
	        $this->data['o'] = $o;
	        $this->_render_page('affiliate/dashboard', $this->data);

    	}

    	if($user->aff_status == 'reject'){
    		$o['msg_type'] = 'danger'; 
    		$o['msg'] = "Sorry but your application has been rejected.";

	        $o['pending_account_table'] = true; 
	        $this->data['o'] = $o;
	        $this->_render_page('affiliate/dashboard', $this->data);

    	}

    	if($user->aff_status == 'approved'){
    		 $this->dashboard_approved();
    	}       	        
    }

    public function dashboard_approved(){
    	$user = $this->ion_auth->user()->row();

        $sess_msg       = $this->session->flashdata('msg');
        $sess_msg_type  = $this->session->flashdata('msg_type');

        if($sess_msg){
            $o['msg_sess'] = $sess_msg;
            $o['msg_type_sess'] = $sess_msg_type;
            $o['session_msg_table'] = true;
        }


    	if($user->aff_status != 'approved'){
    		$o['msg_type'] = 'danger'; 
    		$o['msg'] = "Sorry, You are not allowed to view this page.";

	        $o['pending_account_table'] = true; 
	        $this->data['o'] = $o;
	        $this->_render_page('affiliate/dashboard', $this->data);
	        return;
    	}

    	$sql = "select count(user_id) as count_aff from affiliates where user_id = '$user->id' and aff_status = '1' LIMIT 1";

    	$check_aff = $this->db->query($sql);
    	if($check_aff->num_rows() == 0){
    		$o['affiliate_count'] = 0;
    	}else{
    		$get_aff = $check_aff->row_array();
    		$o['affiliate_count'] = number_format($get_aff['count_aff'],0);
    	}

    	/* check API Details*/
    	/*
        $sql = "select id,user_id,plan_id,return_id,p_status from paypal 
                    where 
                        user_id = '$user->id' and ppstatus = '1' and p_status = 'ACTIVE' LIMIT 1";
          $get_ppal = $this->db->query($sql);
          if($get_ppal->num_rows() == 0){
                redirect('dashboard', 'refresh');
          }else{
            $gp = $get_ppal->row_array();

            $check_billing_details =  file_get_contents($this->baseurl ."paypal.php?p=check_billing_agreement&id={$gp['return_id']}");
            $confirm_obj = (array) json_decode($check_billing_details);

            echo "<pre>";
            echo "paypal.php?p=check_billing_agreement&id={$gp['return_id']}";
            print_r($confirm_obj);
            echo "</pre>";
            die();
        }
        */

    	/* check all approved */
    	$sql = "select aff.user_id_aff,aff.date_added,afp.amt as aff_amt,
		(select CONCAT(first_name, ' ',last_name) from users where id = afp.sender_id LIMIT 1) as user_aff,
		pp.amt,pp.curr,pp.p_status,pp.date_confirmed
		from 
		affiliates as aff,
		aff_payout as afp,
		paypal as pp
		where 
			aff.user_id = '$user->id'
		and
			afp.receiver_id = aff.user_id
		and
			pp.user_id = aff.user_id_aff
		and
			pp.p_status = 'ACTIVE'
		and	
			aff.aff_status = '1'
		GROUP BY user_id_aff
		order by 
			afp.date_transaction desc";
		$check_com = $this->db->query($sql);
		if($check_com->num_rows() == 0){
			$o['active_count_users'] = 0;
    		$o['msg_type'] = 'danger'; 
    		$o['msg'] = "No active affiliate users";
    		$o['pending_account_table'] = true; 
		}else{
			$o['active_count_users'] = $check_com->num_rows();
			$show_ud = array();
			foreach($check_com->result_array() as $cc){
				$cc['total_payout_user'] = $this->compute_user_total_comission($cc['user_id_aff']);
				$cc['date_confirmed'] = date('m-d-Y', $cc['date_confirmed']);
				$show_ud[] = $cc;
			}
			$o['show_ud'] = $show_ud;
			$o['show_user_aff_table'] = true;
		}
    	$o['is_aff'] = $user->is_aff;
	    $o['show_affiliate_table'] = true; 
    	$this->data['u'] = $user;
	    $this->data['o'] = $o;	    
	    $this->_render_page('affiliate/dashboard', $this->data);
    }

    function updetails(){
    	$user = $this->ion_auth->user()->row();

    	if($user->aff_status != 'approved'){

    		$o['msg_type'] = 'danger'; 
    		$o['msg'] = "Sorry, You are not allowed to view this page.";

	        $o['pending_account_table'] = true; 
	        $this->data['o'] = $o;
	        $this->_render_page('affiliate/dashboard', $this->data); 
	         return;   		
    	}	

        $this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
		
        $this->data['company'] = array(
            'name'  => 'company',
            'id'    => 'company',
            'type'  => 'text',
            'class' => 'validify_required validify_alpha',
            'value' => $user->company,
        );
        $this->data['country'] = array(
            'name'  => 'country',
            'id'    => 'country',
            'type'  => 'select',
            'class' => 'validify_required',
            'value' => $user->country,
        );
        //<input id="mobile" type="tel" name="mobile" class="form-control validify_required validify_mobile">
        $this->data['mobile'] = array(
            'name'  => 'mobile',
            'id'    => 'mobile',
            'type'  => 'tel',
            'class' => 'form-control validify_required validify_mobile',
            'value' => $user->mobile,
        );
        $this->data['whatsApp'] = array(
            'name'  => 'whatsApp',
            'id'    => 'whatsApp',
            'type'  => 'text',
            'class' => 'validify_numeric',
            'value' => $user->whatsapp,
        );		            		            		            
        $this->data['website'] = array(
            'name'  => 'website',
            'id'    => 'website',
            'type'  => 'text',
            'class' => 'validify_required validify_website',
            'placeholder' => 'https://www.yourdomainname.com',
            'value' => $user->website,
        );
        $this->data['twitter'] = array(
            'name'  => 'twitter',
            'id'    => 'twitter',
            'type'  => 'text',
            'class' => 'validify_required validify_twitter',
            'placeholder' => 'https://twitter.com/youridhere',
            'value' => $user->twitter,
        );
        $this->data['fb'] = array(
            'name'  => 'fb',
            'id'    => 'fb',
            'type'  => 'text',
            'class' => 'validify_required validify_facebook',
            'placeholder' => 'https://www.facebook.com/youridhere',
            'value' => $user->facebook,
        );
        $this->data['ln'] = array(
            'name'  => 'ln',
            'id'    => 'ln',
            'type'  => 'text',
            'class' => 'validify_required validify_linkedin',
            'placeholder' => 'https://www.linkedin.com/profile/view?id=youridhere',
            'value' => $user->linkedin,
        );
	    
	    $this->data['paypal_email'] = array(
            'name'  => 'paypal_email',
            'id'    => 'paypal_email',
            'type'  => 'text',
            'class' => 'validify_required validify_email',
            'placeholder' => 'Your paypal email address',
            'value' => $user->paypal_email,
        );

	    $this->data['update_button'] = array(
            'name'  => 'update_button',
            'id'    => 'update_button',
            'type'  => 'hidden',
            'value' => $user->id . '_update_affiliate_account'
        );        
        
	    $this->template->set_title('Affiliate update');
        $this->_render_page('affiliate/affupdatedet', $this->data);
    }

    function uppixels(){

    	$user = $this->ion_auth->user()->row();
    	$update_button = $this->input->post('update_button');
    	$this->form_validation->set_rules('user_pixel', $this->lang->line('create_user_validation_fname_label'), 'xss_clean');
    	
    	$pixel = $this->input->post('user_pixel');

        $sess_msg       = $this->session->flashdata('msg');
        $sess_msg_type  = $this->session->flashdata('msg_type');

        if($sess_msg){
            $o['msg_sess'] = $sess_msg;
            $o['msg_type_sess'] = $sess_msg_type;
            $o['session_msg_table'] = true;
        }

    	if($user->aff_status != 'approved'){

    		$o['msg_type'] = 'danger'; 
    		$o['msg'] = "Sorry, You are not allowed to view this page.";

	        $o['pending_account_table'] = true; 
	        $this->data['o'] = $o;
	        $this->_render_page('affiliate/dashboard', $this->data); 
	         return;   		
    	}	

        if ($this->form_validation->run() == true)
        {
			if($update_button == $user->id . "_update_pixel")
			{

	    	    $data = array(
	            'user_pixel'   => $this->cleanup_pixel($pixel)
	    	    );
	        	//$this->ion_auth->update($user->id, $data);
                $this->db->where('id', $user->id);
                $this->db->update('users', $data);
	        	$this->session->set_flashdata('msg', "Update successful");
	        	$this->session->set_flashdata('msg_type', 'success');  
	        }
	        else{
	        	$this->session->set_flashdata('msg', "Error occured, please try again");
	        	$this->session->set_flashdata('msg_type', 'danger');  
	        }
	        redirect('affiliate/uppixels', 'refresh');

        }else{
        	$this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
		
	        $this->data['user_pixel'] = array(
	            'name'  => 'user_pixel',
	            'id'    => 'user_pixel',
	            'type'  => 'text',
	            'class' => 'validify_required validify_alphanumeric',
	            'rows'  => '15',
      			'cols'  => '15',
	            'value' => $this->pixel2orig($user->user_pixel),
	        );

		    $this->data['update_button'] = array(
	            'name'  => 'update_button',
	            'id'    => 'update_button',
	            'type'  => 'hidden',
	            'value' => $user->id . '_update_pixel'
	        );   

		    $this->data['o'] = $o;	  
	        $this->template->set_title('Update Pixels');
	        $this->_render_page('affiliate/updatepixels', $this->data);
	    }
    }

    function payout(){
        $p          = strtolower($this->input->post('p'));
       	$user = $this->ion_auth->user()->row();
        $this->check_permission();

        $sess_msg       = $this->session->flashdata('msg');
        $sess_msg_type  = $this->session->flashdata('msg_type');
        if($sess_msg){
            $msg = $sess_msg;
            $msg_type = $sess_msg_type;
        }

        $sql = "select distinct(date_transaction), 
                        (select count(date_transaction) from aff_payout 
                        where 
                                date_transaction = aff_payout.date_transaction
                            and 
                                payout_status = 'SUCCESS'
                            and
                                aff_sent = '1'
                            and	
                            	receiver_id = '$user->id'
                        ) 
                    as 
                        total_transaction 
                from 
                    aff_payout 
                where
                    aff_sent = '1' 
                and
                	receiver_id = '$user->id'
                order by date_transaction desc";

        $check_payout = $this->db->query($sql);
        if($check_payout->num_rows() == 0){
            $msg = "No Payout found.";
            $msg_type = 'warning';

        }else{
            $show_payout = array();
            foreach($check_payout->result_array() as $cp){
                $show_payout[] = $cp;
            }
            $o['show_payout'] = $show_payout;
	        $o['gc'] = $u;    
	        $o['show_payout_table'] = true;

        }
         $o['msg_type'] = $msg_type;
        $o['msg'] = $msg;
        $o['title'] = "Payout";
        $o['page'] = 'payout';
        $o['user'] = $user;
        $o['baseurl'] = $this->baseurl;    
        $this->data['o'] = $o;
       $this->_render_page('affiliate/payout', $this->data);  
    }


    function view_payout_date()
    {
    	$user = $this->ion_auth->user()->row();
    	$payout_date   = $this->uri->segment(3);
        $sess_msg       = $this->session->flashdata('msg');
        $sess_msg_type  = $this->session->flashdata('msg_type');

        if($sess_msg){
            $o['msg_sess'] = $sess_msg;
            $o['msg_type_sess'] = $sess_msg_type;
            $o['session_msg_table'] = true;
        }

        $sql = "select sender_id,receiver_id,pid,date_transaction,amt,payout_status,payout_id
        from 
            aff_payout as ap
        where 
        	aff_sent = '1' 
        and 
            ap.receiver_id = '$user->id'
        and
        	ap.date_transaction = '$payout_date'
        order by pid asc";

        $check_payout = $this->db->query($sql);
        if($check_payout->num_rows() == 0){
            $msg        = "No paypal payment, please try again later";
            $msg_type   = 'info'; 

        }else{
            $total_paid = 0;
            $total_transaction = 0;
            $show_payout = array();
            foreach($check_payout->result_array() as $cp){
                $aff_name = (array) $this->ion_auth->user($cp['sender_id'])->row();
                $receiver_name = $aff_name['first_name'] . " " . $aff_name['last_name'];
                $cp['aff_name'] = $receiver_name;
                $total_paid = $total_paid + intval($cp['amt']);

                $total_transaction = $total_transaction + 1;
                $show_payout[] = $cp;
            }
            $o['show_payout'] = $show_payout;
        
	        $o['total_transaction'] = $total_transaction;
	        $o['total_paid'] = $total_paid;
	        $o['gc'] = $u;    
	        $o['show_payout_user_transaction_det'] = true;
	        $o['title'] = "Payout date: $payout_date";
	        $o['page'] = 'payout';
	        $o['user'] = $user;
        }

	    	$o['msg_type'] = $msg_type;
	        $o['msg'] = $msg;        
        	$o['baseurl'] = $this->baseurl;    
        	$this->data['o'] = $o;
      		$this->_render_page('affiliate/payout', $this->data);
    }

    function paypaltransaction_details(){

       $transaction_id      = $this->input->post('transaction_id');
        $user   =  $this->ion_auth->user()->row();
        $this->check_permission(); 
      
        require 'paypal/sample/bootstrap.php';
        $payouts = new \PayPal\Api\Payout();
        // ### Get Payout Batch Status
        try {
            $output = \PayPal\Api\Payout::get($transaction_id, $apiContext);
        } catch (Exception $ex) {
            ResultPrinter::printError("Get Payout Batch Status", "PayoutBatch", null, $payoutBatchId, $ex);
            exit(1);
        }

        $obj = $output->toJSON();
        $paypal_obj = (array) json_decode($obj);
        $this->data['pp'] = $paypal_obj;
        $o['baseurl'] = $this->baseurl;    
        
        $this->data['o'] = $o;
        $this->load->view('div/transaction_details', $this->data);  

    }

    function view_signup_users_details(){
    	$user_id          = $this->input->post('user_id');
    	$search_type      = strtolower($this->input->post('search_type'));
    	$user   =  $this->ion_auth->user()->row();
        $this->check_permission(); 

        if($search_type == "signups")
        {
        	$sql = "select (select CONCAT(first_name, ' ',last_name) from users where id = affiliates.user_id_aff LIMIT 1) as user_aff 
        			from 
        				affiliates where user_id = '$user->id' and aff_status = '1' ";

        }else{
	    	$sql = "select aff.user_id_aff,aff.date_added,afp.amt as aff_amt,
			(select CONCAT(first_name, ' ',last_name) from users where id = afp.sender_id LIMIT 1) as user_aff,
			pp.amt,pp.curr,pp.p_status,pp.date_confirmed
			from 
			affiliates as aff,
			aff_payout as afp,
			paypal as pp
			where 
				aff.user_id = '$user->id'
			and
				afp.receiver_id = aff.user_id
			and
				pp.user_id = aff.user_id_aff
			and
				pp.p_status = 'ACTIVE'
			and	
				aff.aff_status = '1'";	
        }
        $check_user = $this->db->query($sql);
        if($check_user->num_rows() == 0){
        	echo "No Result Found, please try again";
        }else{
        	$o['sr'] = $check_user->result_array();
        }
        $o['baseurl'] = $this->baseurl;    
        $this->data['o'] = $o;
        $this->load->view('div/user_details', $this->data);          
    }

    function viewusertransaction(){
    	$user_id    = $this->input->post('user_id');
    	$user   	=  $this->ion_auth->user()->row();
        $this->check_permission(); 

        $sql = "select date_transaction,payout_id, amt,payout_status from aff_payout where receiver_id = '$user->id' 
        	and sender_id = '$user_id'  order by date_transaction desc";
        $check_trans = $this->db->query($sql);
        if($check_trans->num_rows() == 0){
        	echo "No Transaction Found.";
        }else{
        	$show_trans = array();
        	$total_trans = 0;
        	foreach($check_trans->result_array() as $st){
        		$total_trans = $total_trans + $st['amt'];
        		$show_trans[] = $st;
        	}
        	$o['total_trans'] = $total_trans;
        	$o['show_trans'] = $show_trans;
	        $o['baseurl'] = $this->baseurl;    
	        $this->data['o'] = $o;
	        $this->load->view('div/uset_trans_det', $this->data);            
        }


    }

    function send_aff_confirmation(){
    	$user   	=  $this->ion_auth->user()->row();

    	if($user->is_aff_tos == 0 and $user->aff_status = 'pending'){

    		$this->email_confirmation();
    		$aff_add_date = date("F d, Y", $user->aff_added);
    		$o['msg_type'] = 'success'; 
    		$o['msg'] = "Affiliate Terms and Condition successfully sent to your email.";
	        $o['pending_account_table'] = true; 
	        $this->data['o'] = $o;
	        $this->_render_page('affiliate/dashboard', $this->data);
    	}else{
    		$aff_add_date = date("F d, Y", $user->aff_added);
    		$o['msg_type'] = 'info'; 
    		$o['msg'] = "Cannot send confirmation, please try again later.";
	        $o['pending_account_table'] = true; 
	        $this->data['o'] = $o;
	        $this->_render_page('affiliate/dashboard', $this->data);
    	}
    }
    function compute_user_total_comission($user_id){
    	$user   	=  $this->ion_auth->user()->row();
        $this->check_permission(); 

        $sql = "select SUM(amt) as total_amt from aff_payout where receiver_id = '$user->id' 
        	and sender_id = '$user_id' LIMIT 1";
        $check_trans = $this->db->query($sql);
        if($check_trans->num_rows() == 0){
        	return 0;
        }else{
        	$show_trans = $check_trans->row_array();
        	return $show_trans['total_amt'];           
        }
    }


    function check_permission(){

        if (!$this->ion_auth->logged_in())
        {
            return show_error('Permission denied, you are not allowed to view this page or try to login again');
        }  
    }

    function cleanup($word)
    {
        $word = trim($word);
        $word = strip_tags($word, " <script> </script> ");
        $word = addslashes($word);
        return $word;
    } 

    function cleanup_pixel($word){
        $word = trim($word);
        $word = str_replace('<script>', '[js]', $word);
        $word = str_replace('</script>','[/js]', $word);
		$word = str_replace('<noscript>','[/ns]', $word);
		$word = str_replace('</noscript>','[/ns]', $word);
        $word = addslashes($word);
        return $word;
    } 

     function pixel2orig($word){
        $word = str_replace('[js]','<script>', $word);
        $word = str_replace('[/js]','</script>', $word);
		$word = str_replace('[/ns]','<noscript>', $word);
		$word = str_replace('[/ns]','</noscript>', $word);
        $word = stripslashes($word);
        return $word;
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
						<h3> <br> Hi, $user->first_name  $user->last_name</h3>
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
			//$this->email->to('australiawow@gmail.com');
			
			$this->email->subject('TubeTarget Pro Affiliate TOS');
			$this->email->message($email_template);	
			$this->email->send();

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