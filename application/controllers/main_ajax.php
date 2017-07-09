<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
session_start();
class Main_ajax extends Ajax_Controller {

    function __construct () {
        parent::__construct();
        $this->load->library('authentication', NULL, 'ion_auth');
        $this->load->database();
        $user = $this->ion_auth->user()->row();
        $this->load->model('Logs_model', 'logs'); 
        $this->load->helper(array('url','cookie'));

        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
        
        //Load session
        if (substr(CI_VERSION, 0, 1) == '2')
        {
            $this->load->library('session');
        }
        else
        {
            $this->load->driver('session');
        }
        $this->baseurl = $this->config->config['base_url']; 
        
    }
    
    function logged_in () {
        $return['logged_in'] = $this->ion_auth->logged_in();
        $return['activate']  = $this->session->flashdata('activate');
        $return['message']   = $this->session->flashdata('message');
        echo json_encode($return);
        //echo $this->ion_auth->logged_in();
        die();
    }

    function delete () {
        $id = $this->input->get('id');
        $this->ion_auth->delete_user($id);
        echo 'DELETED';die();
    }
    
    function login () {
        $return = array();
        
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
                $return['user'] = $user;
                $return['valid']= true;

                $this->logs->insert_logs("login", "Login Successful!");
                echo json_encode($return);
                die(); 
            }
            
            /* CHECK PAYPAL ACCOUNT */
            //check user has paypal data
            
            $sql = "select id,user_id,plan_id,return_id,p_status from paypal 
                        where 
                            user_id = '$user->id' and ppstatus = '1' and p_status in ('ACTIVE','PENDING') LIMIT 1";
              $get_ppal = $this->db->query($sql);
              if($get_ppal->num_rows() == 0){
                    
                        $return['user'] = $user;
                        $return['valid']= true;

                        $this->logs->insert_logs("login", "Login Successful!");
                        echo json_encode($return);
                        die();
              }
              else {
                $gp = $get_ppal->row_array();
                $gp['p_status'] = strtoupper($gp['p_status']);

                //$check_billing_details =  file_get_contents("http://localhost/paypal.php?p=check_billing_details&id={$gp['plan_id']}");
                $check_billing_details =  file_get_contents($this->baseurl . "paypal.php?p=check_billing_details&id={$gp['plan_id']}");
                $confirm_obj = (array) json_decode($check_billing_details);

                if($confirm_obj['state'] == ""){
                    $return['user'] = $user;
                    $return['valid']= false;
                    $return['message']= 'Cannot connect to paypal server, please try again...';
                    //$this->logs->insert_logs("login", "Cannot connect to paypal server!");
                    echo json_encode($return);
                    die();
                }
                // 
                if($confirm_obj['state'] == 'ACTIVE'){

                    //$check_billing_agreement =  file_get_contents("http://localhost/paypal.php?p=check_billing_agreement&id={$gp['return_id']}");
                    $check_billing_agreement =  file_get_contents($this->baseurl  . "paypal.php?p=check_billing_agreement&id={$gp['return_id']}");
                    $conf_bill_ag = (array) json_decode($check_billing_agreement);      

                    if($conf_bill_ag['state'] == ""){
                        $return['user'] = $user;
                        $return['valid']= true;
                        //$return['message']= 'Cannot connect to paypal server, please try again...';
                        //$this->logs->insert_logs("login", "Cannot connect to paypal server!");
                        echo json_encode($return);
                        die();
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
                        $return['user'] = $user;
                        $return['valid']= true;

                        $this->logs->insert_logs("login", "Login Successful!");
                    }else{
                       // $disable_user_account = true;
                        $return['user'] = $user;
                        $return['valid']= true;
                    }   

                }elseif($confirm_obj['state'] == 'PENDING'){
                    $this->logs->insert_logs("login", "Login Successful!");
                    echo json_encode($return);
                    die();
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

                    $return['user'] = $user;
                    $return['valid']= false;
                    $return['message']= 'Subscription Elapsed!';
                }
            }
        }
        else
        {
            //if the login was un-successful
            //redirect them back to the login page
            $error = trim( $this->ion_auth->errors(), '<p></p>' );
            $return['error'] = $error;
            if ( $error === 'You need to activate your account!' ) {
                $this->session->set_flashdata('activate_email', $this->input->post('identity') );
                $msg  = $error;
                $msg .= ' Click <a id="tmp-resend" href="#" data-url="'.base_url('confirmation/resend/').'" >HERE</a> to resend the activation email.';
                $this->session->set_flashdata('message', $msg);
                $return['message'] = $msg;
            }
            else {
                $this->session->set_flashdata('message', $this->ion_auth->errors());
                $return['message'] = $this->ion_auth->errors();
            }
            $return['valid']= false;
        }
        
        echo json_encode($return);
        die();
    }
    
    function signup () {
        // $users = $this->db->get_where('users_groups', array('group_id' => 2))->result();
        // $users = $this->ion_auth->users(array('2'))->result();
        
            $affiliate_name = $_COOKIE['aff_affiliate'];
            if($affiliate_name != ""){
                $sql = "select user_pixel from users where is_aff = '$affiliate_name'  and aff_status = 'approved' and is_aff_tos = '1'  LIMIT 1";
                $check_pixel = $this->db->query($sql);

                if($check_pixel->num_rows() > 0){
            
                   $show_pix = $check_pixel->row_array();

                     $this->data['user_pixel'] = $this->pixel2orig($show_pix['user_pixel']);
                        $this->data['user_pixel_signup'] = true;
                        $is_affiliate_signup = 1;
                }else{
                    $is_affiliate_signup = 0;
                }
            }else{
                $is_affiliate_signup = 0;
            }
        //if ( count($users) < 50 ) {
  
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
                echo "ERROR|ERROR|Invalid session token, Please refresh page and try again.";
            }else{

                    if ($this->form_validation->run() == true)
                    {

                        $additional_data = array(
                            'first_name' => $first_name,
                            'last_name'  => $last_name,
                            'su_is_aff'  => $is_affiliate_signup
                        );
                    
                        // if ( count($users) < 50 ) {
                        $group = array('3');
                        // }
                        // else {
                        //     $group = array('2');
                        // }
            
                        if ( $this->ion_auth->register($username, $password, $email, $additional_data, $group) ) {
                            $this->logs->insert_logs("signup", "Signup Successful!");

                            if($affiliate_name != ""){
                                $get_user_aff_id    = $this->get_aff_cookie($affiliate_name);
                                $sql = "select id from users where email = '$email' LIMIT 1";

                                $check_new_user = $this->db->query($sql);
                                if($check_new_user->num_rows() > 0){
                                    $new_u = $check_new_user->row_array();
                 
                                    if($get_user_aff_id != ""){

                                        $data = array(
                                           'user_id_aff' => $new_u['id'],
                                           'user_id' => $get_user_aff_id ,
                                           'date_added' => time()
                                        );
                                        $this->db->insert('affiliates', $data);
                                    } 
                                    setcookie("aff_affiliate", "", time() - 3600); 
                                }
                                
                                 
                            }

                            $_SESSION['signup_token'] = "";
                            echo "SUCCESS|SUCCESS|Thank you! Now go check your inbox (and sometimes your spam folder).";
                        }
                        else {
                            $this->logs->insert_logs("signup", "Signup Unsuccessful!");
                            echo "ERROR|ERROR|Signup Failed, Please refresh page and try again.";
                           
                           
                        }
                    }else{
                       echo "ERROR|ERROR|Error while saving your account. Please check all fields if correct and try again."; 
                    }

            }
        // }
        // else {
           //  echo false;
        // }
        die();
    }

    function forgot_password() {
        $this->load->database();
        $this->load->library('authentication', NULL, 'ion_auth');
        $this->lang->load('auth');

        $return = array();
        // get identity from username or email
        if ( $this->config->item('identity', 'ion_auth') == 'username' ){
            $identity = $this->ion_auth->where('username', strtolower($this->input->get_post('email')))->users()->row();
        }
        else
        {
            $identity = $this->ion_auth->where('email', strtolower($this->input->get_post('email')))->users()->row();
        }
        if(empty($identity)) {
            $this->ion_auth->set_message('forgot_password_email_not_found');
            //$this->session->set_flashdata('message', $this->ion_auth->messages());
            //$this->response->script('$("#infoMessage").html("'.$this->ion_auth->messages().'");$("#infoMessage").addClass("alert alert-info")');
            $return['msg']  = $this->ion_auth->messages();

            $this->logs->insert_logs("forgot_password", $this->ion_auth->messages());
            $return['valid']= false;
        }

        //run the forgotten password method to email an activation code to the user
        $forgotten = $this->ion_auth->forgotten_password($identity->{$this->config->item('identity', 'ion_auth')});
        if ($forgotten)
        {
            $this->logs->insert_logs("forgot_password", "Forgot Password Successful");
            $return['valid']= true;
        }
        else
        {
            //$this->session->set_flashdata('message', $this->ion_auth->errors());
            $errors = $this->ion_auth->errors();
            $this->logs->insert_logs("forgot_password", $errors);
            $return['msg']  = $errors;
            $return['valid']= false;
        }

        echo json_encode($return);
        die();
    }

     function get_aff_cookie($affiliate_id)
    {
         /* check affiliate id */
         $sql = "select id from users where is_aff = '$affiliate_id' and aff_status = 'approved' and is_aff_tos = '1'  LIMIT 1";
         $check_aff = $this->db->query($sql);
         if($check_aff->num_rows() == 0){
           return "";
         }else{
            $show_aff_id = $check_aff->row_array();
            return $show_aff_id['id'];      
         }
    }   
     function pixel2orig($word){
        $word = str_replace('[js]','<script>', $word);
        $word = str_replace('[/js]','</script>', $word);
        $word = str_replace('[/ns]','<noscript>', $word);
        $word = str_replace('[/ns]','</noscript>', $word);
        $word = stripslashes($word);
        return $word;
    }  

    function cleanup($word)
    {
        $word = trim($word);
        $word = strip_tags($word, " <STRONG> <EM> <U> <BR> <n> \n ");
        $word = addslashes($word);
        return $word;
    }      
}