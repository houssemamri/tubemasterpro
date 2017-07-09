<?php defined('BASEPATH') OR exit('No direct script access allowed');
session_start();
class Auth extends MX_Controller {

    public $data;

    function __construct()
    {
        parent::__construct();
        $this->load->library('authentication', NULL, 'ion_auth');
        $this->load->library('form_validation');
        $this->load->library('template');
        $this->load->helper('url');

        $this->load->database();

        //Load session
        if (substr(CI_VERSION, 0, 1) == '2')
        {
            $this->load->library('session');
        }
        else
        {
            $this->load->driver('session');
        }

        $this->form_validation->set_error_delimiters($this->config->item('error_start_delimiter', 'ion_auth'), $this->config->item('error_end_delimiter', 'ion_auth'));

        $this->lang->load('auth');
        $this->load->helper('language');
        $this->baseurl = $this->config->config['base_url']; 
        $this->load->model('Logs_model', 'logs'); 
    }

    //redirect if needed, otherwise display the user list
    function index()
    {

        //echo 'index == '.$this->session->userdata('user_session_id');
        if (!$this->ion_auth->logged_in())
        {
            if ( $this->session->flashdata('message') ) {
                $this->session->set_flashdata('message', $this->session->flashdata('message') );
            }
            //redirect them to the login page
            //redirect('auth/login', 'refresh');
            redirect(site_url(''));
        }
        /*elseif (!$this->ion_auth->is_admin()) //remove this elseif if you want to enable this for non-admins
        {
            //redirect them to the home page because they must be an administrator to view this
            return show_error('You must be an administrator to view this page.');
        }*/
        else if ( $this->ion_auth->in_group(3) || $this->ion_auth->is_admin() ) {
            redirect('dashboard/keyword_search', 'refresh');
        }
        else
        {
            //check user if has unlimited coupon
            $user_get_promo = $this->ion_auth->user()->row();
            if($user_get_promo->use_promocode != 0){
                $sql = "select discount_amt,is_onetime, option_desc from promo_code where promo_code_id = '". $user_get_promo->use_promocode."' LIMIT 1";
                $check_promo = $this->db->query($sql);
                    if($check_promo->num_rows() > 0){
                        
                        $cp = $check_promo->row_array();
                        if($cp['discount_amt'] == 100 && $cp['is_onetime'] == 0){
                             redirect('dashboard/keyword_search', 'refresh');
                            
                        }
                    }
            } 

            //set the flash data error message if there is one
            $this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');

            //list the users
            $this->data['users'] = $this->ion_auth->users()->result();
            foreach ($this->data['users'] as $k => $user)
            {
                $this->data['users'][$k]->groups = $this->ion_auth->get_users_groups($user->id)->result();
            }

            if ($this->ion_auth->in_group('video_admin'))
            {
                redirect('videoadmin', 'refresh');
            }
            // $this->_render_page('auth/index', $this->data);
            
            $this->load->model('members_model');
            $user = $this->ion_auth->user()->row();
            $member = $this->members_model->get_subscription_details($user->id);

            if ( $member ) {
      
                $url = "http://localhost/paypal.php?p=check_details&id=".$member->plan_id;
                $subscription_status = strtoupper(file_get_contents( $url ));
                 $check_status = array('ACTIVE','PENDING');
                
                 //if ( $subscription_status == "ACTIVE" ) {
                 if (!in_array(strtoupper($subscription_status), $check_status)) {
                    redirect('dashboard/keyword_search', 'refresh');
                 }
                 else {
                    $update = $this->members_model->update_status($user->id, $subscription_status);
                    redirect('subscription', 'refresh');
                }
            }
            else {
                redirect('subscription', 'refresh');
                
            }
            
        }
    }

  //log the user in
    function login()
    {
        //echo 'login == '.$this->session->userdata('user_session_id');
        $this->template->add_js('jquery.validify.js');
        $this->template->add_js('modules/warroom.js');

        $this->data['title'] = "Login";

        //validate form input
        $this->form_validation->set_rules('identity', 'Identity', 'required');
        $this->form_validation->set_rules('password', 'Password', 'required');

        if ($this->form_validation->run() == true)
        {
            
            //check to see if the user is logging in
            //check for "remember me"
            $remember = (bool) $this->input->post('remember');

            if ($this->ion_auth->login($this->input->post('identity'), $this->input->post('password'), $remember))
            {
                //if the login is successful
                //redirect them back to the home page
                $this->session->set_flashdata('message', $this->ion_auth->messages());
                $user = $this->ion_auth->user()->row();

                if ( !$user->first_login ) {
                    $data = array(
                        'first_login' => time()
                     );
                    $this->ion_auth->update( $user->id, $data );
                }
                
                if ( $user->invites_counter < 2 ) {
	            	$invites_counter = $user->invites_counter + 1;
	                $data = array(
	                    'invites_counter' => $invites_counter
	                 );
	                $this->ion_auth->update( $user->id, $data );
	            }

                if($user->is_jvzoo == 1){
                    //check if user paid
                     redirect('dashboard', 'refresh');
                }
                
                /* CHECK PAYPAL ACCOUNT */
                //check user has paypal data
                
                $sql = "select id,user_id,plan_id,return_id,p_status from paypal 
                            where 
                                user_id = '$user->id' and ppstatus = '1' and p_status in ('ACTIVE','PENDING') LIMIT 1";

                  
                  $get_ppal = $this->db->query($sql);
                  if($get_ppal->num_rows() == 0){
                        redirect('dashboard', 'refresh');
                      
                  }else{
                    $gp = $get_ppal->row_array();
                    $gp['p_status'] = strtoupper($gp['p_status']);

                   //$check_billing_details =  file_get_contents("http://localhost/paypal.php?p=check_billing_details&id={$gp['plan_id']}");
                    $check_billing_details =  file_get_contents($this->baseurl . "paypal.php?p=check_billing_details&id={$gp['plan_id']}");
                    $confirm_obj = (array) json_decode($check_billing_details);

//die();
                    if($confirm_obj['state'] == ""){
                        	return show_error('Cannot connect to paypal server, please try again...');
                    }
                    // 
                    if($confirm_obj['state'] == 'ACTIVE'){

                       // $check_billing_agreement =  file_get_contents("http://localhost/paypal.php?p=check_billing_agreement&id={$gp['return_id']}");
                        $check_billing_agreement =  file_get_contents($this->baseurl . "paypal.php?p=check_billing_agreement&id={$gp['return_id']}");
                        $conf_bill_ag = (array) json_decode($check_billing_agreement);      
                        //echo $this->baseurl . "paypal.php?p=check_billing_agreement&id={$gp['return_id']}";

                    
                        if($conf_bill_ag['state'] == ""){
                            //return show_error('Cannot connect to paypal server, please try again...');
                        }
						
                        if(strtoupper($conf_bill_ag['state']) == 'ACTIVE'){
   
                            $next_bill_unix = strtotime($conf_bill_ag['agreement-details']->next_billing_date);
                            $next_billing_date_check =  date("Y-m-d", $next_bill_unix);  // 1 day
                            //$aff_date_check = date("Y-m-d", $next_bill_unix  + (60 * 60 * 24 * 6));  // 7 day
                            
                            $nextMonday= strtotime("next Monday",$next_bill_unix);
                            $secondMonday=strtotime("next Monday",$nextMonday); 
                            $thirdMonday=strtotime("next Monday",$secondMonday); 
                            $get_difference = $nextMonday - $next_bill_unix;
                            //172800 - 2days
                            if($get_difference < 172800)
                            {
                              $aff_date_check = date('Y-m-d',$thirdMonday);
                            }else{
                              $aff_date_check = date('Y-m-d',$secondMonday);
                            }

                            $data = array(
                               'next_billing_date' => $conf_bill_ag['agreement-details']->next_billing_date,
                               'next_billing_date_check' => $next_billing_date_check,
                               'pay_cycle' => $conf_bill_ag['agreement-details']->cycles_completed,
                               'aff_date_check' => $aff_date_check,
                               'p_status'      => $confirm_obj['state']
                            );
                            $this->db->where('id', $gp['id']);
                            $this->db->update('paypal', $data);

                            redirect('dashboard', 'refresh');
                        }else{
                           // $disable_user_account = true;
                           //redirect('dashboard', 'refresh');
                            if(strtoupper($conf_bill_ag['state']) == 'CANCELLED'){
                                $disable_user_account = true;
                            }else{
                                
                                redirect('dashboard', 'refresh');
                            }
                        }   

                    }elseif($confirm_obj['state'] == 'PENDING'){
                        redirect('dashboard', 'refresh');
                    }
                    else{

                       $disable_user_account = true;
                    }

                    if( $disable_user_account)
                    {
                         $this->db->trans_start();
                        /* UPDATE PAYPAL STATUS*/

                        $data = array(
                           'ppstatus' => '2',
                           'date_cancelled' => time(),
                           'p_status'      => 'INACTIVE'
                        );
                        $this->db->where('id', $gp['id']);
                        $this->db->update('paypal', $data);

                        /* if user has affiliate, disable it!*/
                        if($user->is_aff != '' and $user->aff_status == 'approved'){
                            $data = array(
                               'aff_notes' => 'admin - Subscription Lapsed <br>' . $user->aff_notes ,
                               'aff_status'      => 'rejected'
                            );
                            $this->db->where('id', $user->id);
                            $this->db->update('users', $data);

                            //update user affiliate status 0
                            $sql = $this->db->query("update affiliates set aff_status = '0' where user_id = '$user->id'");

                            $this->data['show_aff_elapsed'] = true;
                        }else{
                            $this->data['show_user_elapsed'] = true;
                        }
                        $this->db->trans_complete();
                        $this->session->set_flashdata('message', $msg);
                        $this->_render_page('signup/subscription_elapsed', $this->data);
                    }  
                }
                $this->logs->insert_logs("login", "Login Successful");
            }
            else
            {
                //if the login was un-successful
                //redirect them back to the login page
                $error = trim( $this->ion_auth->errors(), '<p></p>' );
                if ( $error === 'You need to activate your account!' ) {
                    $this->session->set_flashdata('activate_email', $this->input->post('identity') );
                    $msg  = $error;
                    $msg .= ' Click <a href="'.base_url('confirmation/resend/').'" >HERE</a> to resend the activation email.';
                    $this->session->set_flashdata('message', $msg);

                }
                else {
                    $this->session->set_flashdata('message', $this->ion_auth->errors());
                }
                
                redirect('auth/login', 'refresh'); //use redirects instead of loading views for compatibility with MY_Controller libraries
            }
            
        }
        else
        {
            //check if email activation exists
            if ( $this->session->flashdata('activate_email') ) {
                $this->session->set_flashdata('activate_email', $this->session->flashdata('activate_email'));
            }
            
            //the user is not logging in so display the login page
            //set the flash data error message if there is one
            $this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');

            $this->data['identity'] = array('name' => 'identity',
                'id' => 'identity',
                'type' => 'text',
                'value' => $this->form_validation->set_value('identity'),
            );
            $this->data['password'] = array('name' => 'password',
                'id' => 'password',
                'type' => 'password',
            );

            $this->_render_page('auth/login', $this->data);
        }
    }
    
    //log the user out
    function logout()
    {
        $this->logs->insert_logs("logout", "Logout Successful");
        $this->data['title'] = "Logout";

        //log the user out
        $logout = $this->ion_auth->logout();

        //redirect them to the login page
        $this->session->set_flashdata('message', $this->ion_auth->messages());
        //redirect('main', 'refresh');
        redirect('/', 'refresh');
    }

    //change password
    function change_password()
    {
        $this->form_validation->set_rules('old', $this->lang->line('change_password_validation_old_password_label'), 'required');
        $this->form_validation->set_rules('new', $this->lang->line('change_password_validation_new_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|max_length[' . $this->config->item('max_password_length', 'ion_auth') . ']|matches[new_confirm]');
        $this->form_validation->set_rules('new_confirm', $this->lang->line('change_password_validation_new_password_confirm_label'), 'required');

        if (!$this->ion_auth->logged_in())
        {
            redirect('auth/login', 'refresh');
        }

        $user = $this->ion_auth->user()->row();

        if ($this->form_validation->run() == false)
        {
            //display the form
            //set the flash data error message if there is one
            $this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');

            $this->data['min_password_length'] = $this->config->item('min_password_length', 'ion_auth');
            $this->data['old_password'] = array(
                'name' => 'old',
                'id'   => 'old',
                'type' => 'password',
            );
            $this->data['new_password'] = array(
                'name' => 'new',
                'id'   => 'new',
                'type' => 'password',
                'pattern' => '^.{'.$this->data['min_password_length'].'}.*$',
            );
            $this->data['new_password_confirm'] = array(
                'name' => 'new_confirm',
                'id'   => 'new_confirm',
                'type' => 'password',
                'pattern' => '^.{'.$this->data['min_password_length'].'}.*$',
            );
            $this->data['user_id'] = array(
                'name'  => 'user_id',
                'id'    => 'user_id',
                'type'  => 'hidden',
                'value' => $user->id,
            );

            //render
            $this->_render_page('auth/change_password', $this->data);
        }
        else
        {
            $identity = $this->session->userdata('identity');

            $change = $this->ion_auth->change_password($identity, $this->input->post('old'), $this->input->post('new'));

            if ($change)
            {
                //if the password was successfully changed
                $this->logs->insert_logs("change_password", "Password Successfully Changed.");
                $this->session->set_flashdata('message', $this->ion_auth->messages());
                $this->logout();
            }
            else
            {
                $this->logs->insert_logs("change_password", $this->ion_auth->errors());
                $this->session->set_flashdata('message', $this->ion_auth->errors());
                redirect('auth/change_password', 'refresh');
            }
        }
    }

    //forgot password
    function forgot_password()
    {
        
        $this->form_validation->set_rules('email', $this->lang->line('forgot_password_validation_email_label'), 'required|valid_email');
        if ($this->form_validation->run() == false)
        {
            $this->template->add_js('jquery.validify.js');
            $this->template->add_js('modules/warroom.js');
            //setup the input
            $this->data['email'] = array('name' => 'email',
                'id' => 'email',
            );

            if ( $this->config->item('identity', 'ion_auth') == 'username' ){
                $this->data['identity_label'] = $this->lang->line('forgot_password_username_identity_label');
            }
            else
            {
                $this->data['identity_label'] = $this->lang->line('forgot_password_email_identity_label');
            }

            //set any errors and display the form
            $this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');
            $this->_render_page('auth/forgot_password', $this->data);
        }
        else
        {
            // get identity from username or email
            if ( $this->config->item('identity', 'ion_auth') == 'username' ){
                $identity = $this->ion_auth->where('username', strtolower($this->input->post('email')))->users()->row();
            }
            else
            {
                $identity = $this->ion_auth->where('email', strtolower($this->input->post('email')))->users()->row();
            }
            if(empty($identity)) {
                $this->ion_auth->set_message('forgot_password_email_not_found');
                $this->session->set_flashdata('message', $this->ion_auth->messages());

                redirect("auth/forgot_password", 'refresh');
            }

            //run the forgotten password method to email an activation code to the user
            $forgotten = $this->ion_auth->forgotten_password($identity->{$this->config->item('identity', 'ion_auth')});

            if ($forgotten)
            {
                //if there were no errors
                $this->session->set_flashdata('message', $this->ion_auth->messages());

                redirect("auth/login", 'refresh'); //we should display a confirmation page here instead of the login page
            }
            else
            {
                $this->session->set_flashdata('message', $this->ion_auth->errors());
                redirect("auth/forgot_password", 'refresh');
            }
        }
    }

    //reset password - final step for forgotten password
    public function reset_password($code = NULL)
    {
        if (!$code)
        {
            show_404();
        }

        $user = $this->ion_auth->forgotten_password_check($code);

        if ($user)
        {
            //if the code is valid then display the password reset form

            $this->form_validation->set_rules('new', $this->lang->line('reset_password_validation_new_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|max_length[' . $this->config->item('max_password_length', 'ion_auth') . ']|matches[new_confirm]');
            $this->form_validation->set_rules('new_confirm', $this->lang->line('reset_password_validation_new_password_confirm_label'), 'required');

            if ($this->form_validation->run() == false)
            {
                //display the form

                //set the flash data error message if there is one
                $this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');

                $this->data['min_password_length'] = $this->config->item('min_password_length', 'ion_auth');
                $this->data['new_password'] = array(
                    'name' => 'new',
                    'id'   => 'new',
                'type' => 'password',
                    'pattern' => '^.{'.$this->data['min_password_length'].'}.*$',
                );
                $this->data['new_password_confirm'] = array(
                    'name' => 'new_confirm',
                    'id'   => 'new_confirm',
                    'type' => 'password',
                    'pattern' => '^.{'.$this->data['min_password_length'].'}.*$',
                );
                $this->data['user_id'] = array(
                    'name'  => 'user_id',
                    'id'    => 'user_id',
                    'type'  => 'hidden',
                    'value' => $user->id,
                );
                $this->data['csrf'] = $this->_get_csrf_nonce();
                $this->data['code'] = $code;

                //render
                $this->_render_page('auth/reset_password', $this->data);
            }
            else
            {
                // do we have a valid request?
               // if ($this->_valid_csrf_nonce() === FALSE || $user->id != $this->input->post('user_id'))
			   	if ($user->id != $this->input->post('user_id'))

                {

                    //something fishy might be up
                    $this->ion_auth->clear_forgotten_password_code($code);

                    show_error($this->lang->line('error_csrf'));

                }
                else
                {
                    // finally change the password
                    $identity = $user->{$this->config->item('identity', 'ion_auth')};

                    $change = $this->ion_auth->reset_password($identity, $this->input->post('new'));

                    if ($change)
                    {
                        //if the password was successfully changed
                        $this->session->set_flashdata('message', $this->ion_auth->messages());
                        $this->logout();
                    }
                    else
                    {
                        $this->session->set_flashdata('message', $this->ion_auth->errors());
                        redirect('auth/reset_password/' . $code, 'refresh');
                    }
                }
            }
        }
        else
        {
            //if the code is invalid then send them back to the forgot password page
            $this->session->set_flashdata('message', $this->ion_auth->errors());
            redirect("auth/forgot_password", 'refresh');
        }
    }


    //activate the user
    function activate($id, $code=false)
    {
        if ($code !== false)
        {
            $activation = $this->ion_auth->activate($id, $code);
        }
        else if ($this->ion_auth->is_admin())
        {
            $activation = $this->ion_auth->activate($id);
        }

        if ($activation)
        {
            //redirect them to the auth page
            $this->session->set_flashdata('message', $this->ion_auth->messages());
            $_SESSION['isFirstTime'] = 1;
            redirect("auth", 'refresh');
        }
        else
        {
            //redirect them to the forgot password page
            $this->session->set_flashdata('message', $this->ion_auth->errors());
            redirect("auth/forgot_password", 'refresh');
        }
    }

    //deactivate the user
    function deactivate($id = NULL)
    {
        $id = (int) $id;

        $this->load->library('form_validation');
        $this->form_validation->set_rules('confirm', $this->lang->line('deactivate_validation_confirm_label'), 'required');
        $this->form_validation->set_rules('id', $this->lang->line('deactivate_validation_user_id_label'), 'required|alpha_numeric');

        if ($this->form_validation->run() == FALSE)
        {
            // insert csrf check
            $this->data['csrf'] = $this->_get_csrf_nonce();
            $this->data['user'] = $this->ion_auth->user($id)->row();

            $this->_render_page('auth/deactivate_user', $this->data);
        }
        else
        {
            // do we really want to deactivate?
            if ($this->input->post('confirm') == 'yes')
            {
                // do we have a valid request?
                if ($this->_valid_csrf_nonce() === FALSE || $id != $this->input->post('id'))
                {
                    show_error($this->lang->line('error_csrf'));
                }

                // do we have the right userlevel?
                if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin())
                {
                    $this->ion_auth->deactivate($id);
                }
            }

            //redirect them back to the auth page
            redirect('auth', 'refresh');
        }
    }

    //create a new user
    function create_user()
    {
        $this->data['title'] = "Create User";

        if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin())
        {
            redirect('auth', 'refresh');
        }

        $tables = $this->config->item('tables','ion_auth');

        //validate form input
        $this->form_validation->set_rules('first_name', $this->lang->line('create_user_validation_fname_label'), 'required|xss_clean');
        $this->form_validation->set_rules('last_name', $this->lang->line('create_user_validation_lname_label'), 'required|xss_clean');
        $this->form_validation->set_rules('email', $this->lang->line('create_user_validation_email_label'), 'required|valid_email|is_unique['.$tables['users'].'.email]');
        $this->form_validation->set_rules('phone', $this->lang->line('create_user_validation_phone_label'), 'required|xss_clean');
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
                'last_name'  => $this->input->post('last_name'),
                'company'    => $this->input->post('company'),
                'phone'      => $this->input->post('phone'),
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

            $this->data['first_name'] = array(
                'name'  => 'first_name',
                'id'    => 'first_name',
                'type'  => 'text',
                'value' => $this->form_validation->set_value('first_name'),
            );
            $this->data['last_name'] = array(
                'name'  => 'last_name',
                'id'    => 'last_name',
                'type'  => 'text',
                'value' => $this->form_validation->set_value('last_name'),
            );
            $this->data['email'] = array(
                'name'  => 'email',
                'id'    => 'email',
                'type'  => 'text',
                'value' => $this->form_validation->set_value('email'),
            );
            $this->data['company'] = array(
                'name'  => 'company',
                'id'    => 'company',
                'type'  => 'text',
                'value' => $this->form_validation->set_value('company'),
            );
            $this->data['phone'] = array(
                'name'  => 'phone',
                'id'    => 'phone',
                'type'  => 'text',
                'value' => $this->form_validation->set_value('phone'),
            );
            $this->data['password'] = array(
                'name'  => 'password',
                'id'    => 'password',
                'type'  => 'password',
                'value' => $this->form_validation->set_value('password'),
            );
            $this->data['password_confirm'] = array(
                'name'  => 'password_confirm',
                'id'    => 'password_confirm',
                'type'  => 'password',
                'value' => $this->form_validation->set_value('password_confirm'),
            );

            $this->_render_page('auth/create_user', $this->data);
        }
    }

    //edit a user
    function edit_user($id)
    {
        $this->data['title'] = "Edit User";

        if (!$this->ion_auth->logged_in() || (!$this->ion_auth->is_admin() && !($this->ion_auth->user()->row()->id == $id)))
        {
            redirect('auth', 'refresh');
        }

        $user = $this->ion_auth->user($id)->row();
        $groups=$this->ion_auth->groups()->result_array();
        $currentGroups = $this->ion_auth->get_users_groups($id)->result();

        //validate form input
        $this->form_validation->set_rules('first_name', $this->lang->line('edit_user_validation_fname_label'), 'required|xss_clean');
        $this->form_validation->set_rules('last_name', $this->lang->line('edit_user_validation_lname_label'), 'required|xss_clean');
        $this->form_validation->set_rules('phone', $this->lang->line('edit_user_validation_phone_label'), 'required|xss_clean');
        $this->form_validation->set_rules('company', $this->lang->line('edit_user_validation_company_label'), 'required|xss_clean');
        $this->form_validation->set_rules('groups', $this->lang->line('edit_user_validation_groups_label'), 'xss_clean');

        if (isset($_POST) && !empty($_POST))
        {
            // do we have a valid request?
            if ($this->_valid_csrf_nonce() === FALSE || $id != $this->input->post('id'))
            {
                show_error($this->lang->line('error_csrf'));
            }

            $data = array(
                'first_name' => $this->input->post('first_name'),
                'last_name'  => $this->input->post('last_name'),
                'company'    => $this->input->post('company'),
                'phone'      => $this->input->post('phone'),
            );

            // Only allow updating groups if user is admin
            if ($this->ion_auth->is_admin())
            {
                //Update the groups user belongs to
                $groupData = $this->input->post('groups');

                if (isset($groupData) && !empty($groupData)) {

                    $this->ion_auth->remove_from_group('', $id);

                    foreach ($groupData as $grp) {
                        $this->ion_auth->add_to_group($grp, $id);
                    }

                }
            }

            //update the password if it was posted
            if ($this->input->post('password'))
            {
                $this->form_validation->set_rules('password', $this->lang->line('edit_user_validation_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|max_length[' . $this->config->item('max_password_length', 'ion_auth') . ']|matches[password_confirm]');
                $this->form_validation->set_rules('password_confirm', $this->lang->line('edit_user_validation_password_confirm_label'), 'required');

                $data['password'] = $this->input->post('password');
            }

            if ($this->form_validation->run() === TRUE)
            {
                $this->ion_auth->update($user->id, $data);

                //check to see if we are creating the user
                //redirect them back to the admin page
                $this->session->set_flashdata('message', "User Saved");
                if ($this->ion_auth->is_admin())
                {
                    redirect('auth', 'refresh');
                }
                else
                {
                    redirect('/', 'refresh');
                }
            }
        }

        //display the edit user form
        $this->data['csrf'] = $this->_get_csrf_nonce();

        //set the flash data error message if there is one
        $this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));

        //pass the user to the view
        $this->data['user'] = $user;
        $this->data['groups'] = $groups;
        $this->data['currentGroups'] = $currentGroups;

        $this->data['first_name'] = array(
            'name'  => 'first_name',
            'id'    => 'first_name',
            'type'  => 'text',
            'value' => $this->form_validation->set_value('first_name', $user->first_name),
        );
        $this->data['last_name'] = array(
            'name'  => 'last_name',
            'id'    => 'last_name',
            'type'  => 'text',
            'value' => $this->form_validation->set_value('last_name', $user->last_name),
        );
        $this->data['company'] = array(
            'name'  => 'company',
            'id'    => 'company',
            'type'  => 'text',
            'value' => $this->form_validation->set_value('company', $user->company),
        );
        $this->data['phone'] = array(
            'name'  => 'phone',
            'id'    => 'phone',
            'type'  => 'text',
            'value' => $this->form_validation->set_value('phone', $user->phone),
        );
        $this->data['password'] = array(
            'name' => 'password',
            'id'   => 'password',
            'type' => 'password'
        );
        $this->data['password_confirm'] = array(
            'name' => 'password_confirm',
            'id'   => 'password_confirm',
            'type' => 'password'
        );

        $this->_render_page('auth/edit_user', $this->data);
    }

    // create a new group
    function create_group()
    {
        $this->data['title'] = $this->lang->line('create_group_title');

        if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin())
        {
            redirect('auth', 'refresh');
        }

        //validate form input
        $this->form_validation->set_rules('group_name', $this->lang->line('create_group_validation_name_label'), 'required|alpha_dash|xss_clean');
        $this->form_validation->set_rules('description', $this->lang->line('create_group_validation_desc_label'), 'xss_clean');

        if ($this->form_validation->run() == TRUE)
        {
            $new_group_id = $this->ion_auth->create_group($this->input->post('group_name'), $this->input->post('description'));
            if($new_group_id)
            {
                // check to see if we are creating the group
                // redirect them back to the admin page
                $this->session->set_flashdata('message', $this->ion_auth->messages());
                redirect("auth", 'refresh');
            }
        }
        else
        {
            //display the create group form
            //set the flash data error message if there is one
            $this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));

            $this->data['group_name'] = array(
                'name'  => 'group_name',
                'id'    => 'group_name',
                'type'  => 'text',
                'value' => $this->form_validation->set_value('group_name'),
            );
            $this->data['description'] = array(
                'name'  => 'description',
                'id'    => 'description',
                'type'  => 'text',
                'value' => $this->form_validation->set_value('description'),
            );

            $this->_render_page('auth/create_group', $this->data);
        }
    }

    //edit a group
    function edit_group($id)
    {
        // bail if no group id given
        if(!$id || empty($id))
        {
            redirect('auth', 'refresh');
        }

        $this->data['title'] = $this->lang->line('edit_group_title');

        if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin())
        {
            redirect('auth', 'refresh');
        }

        $group = $this->ion_auth->group($id)->row();

        //validate form input
        $this->form_validation->set_rules('group_name', $this->lang->line('edit_group_validation_name_label'), 'required|alpha_dash|xss_clean');
        $this->form_validation->set_rules('group_description', $this->lang->line('edit_group_validation_desc_label'), 'xss_clean');

        if (isset($_POST) && !empty($_POST))
        {
            if ($this->form_validation->run() === TRUE)
            {
                $group_update = $this->ion_auth->update_group($id, $_POST['group_name'], $_POST['group_description']);

                if($group_update)
                {
                    $this->session->set_flashdata('message', $this->lang->line('edit_group_saved'));
                }
                else
                {
                    $this->session->set_flashdata('message', $this->ion_auth->errors());
                }
                redirect("auth", 'refresh');
            }
        }

        //set the flash data error message if there is one
        $this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));

        //pass the user to the view
        $this->data['group'] = $group;

        $this->data['group_name'] = array(
            'name'  => 'group_name',
            'id'    => 'group_name',
            'type'  => 'text',
            'value' => $this->form_validation->set_value('group_name', $group->name),
        );
        $this->data['group_description'] = array(
            'name'  => 'group_description',
            'id'    => 'group_description',
            'type'  => 'text',
            'value' => $this->form_validation->set_value('group_description', $group->description),
        );

        $this->_render_page('auth/edit_group', $this->data);
    }


    function _get_csrf_nonce()
    {
        $this->load->helper('string');
        $key   = random_string('alnum', 8);
        $value = random_string('alnum', 20);
        $this->session->set_flashdata('csrfkey', $key);
        $this->session->set_flashdata('csrfvalue', $value);

        return array($key => $value);
    }

    function _valid_csrf_nonce()
    {
        if ($this->input->post($this->session->flashdata('csrfkey')) !== FALSE &&
            $this->input->post($this->session->flashdata('csrfkey')) == $this->session->flashdata('csrfvalue'))
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }

    function unsubscribe_email(){
        $subscription_token = $this->uri->segment(3);
        if($subscription_token == "demo123456"){

            $this->session->set_flashdata('message',"DEMO: You successfully unsubsribed on our email list.");
            redirect('auth/login/' . $code, 'refresh');
        }else{
            $sql = "select id,email from users where subscribe_token = '$subscription_token' and is_subscribe = '1' LIMIT 1";
            $check_sub = $this->db->query($sql);
            if($check_sub->num_rows() == 0){
                $this->session->set_flashdata('message',"User subscription not found or user is already unsubscribe on our email list");
                redirect('auth/login/' . $code, 'refresh');
            }else{
                $unsub = $check_sub->row_array();

                $data = array('is_subscribe' => 0);
                $this->db->where('id', $unsub['id']);
                $this->db->update('users', $data);   
                
                $this->session->set_flashdata('message',"You successfully unsubsribed on our email list.");
                redirect('auth/login/' . $code, 'refresh');
            }
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
