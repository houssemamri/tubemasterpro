      <?php
session_start();
error_reporting(0);
defined('BASEPATH') OR exit('No direct script access allowed');

class Subscription extends MX_Controller {
   
    public $data;

    function __construct()
    {
        parent::__construct();
        $this->load->library('authentication', NULL, 'ion_auth');
        $this->load->library('form_validation');
        $this->load->helper('url');
        $this->load->library('template');
        $this->load->database();

        $this->form_validation->set_error_delimiters($this->config->item('error_start_delimiter', 'ion_auth'), $this->config->item('error_end_delimiter', 'ion_auth'));

        $this->lang->load('auth');
        $this->load->helper('language');
        $this->baseurl = $this->config->config['base_url'];    



        if (!$this->ion_auth->logged_in())
        {
            //redirect them to the login page
            redirect('/', 'refresh');
        }
        
        /* PAYPAL */

       // require __DIR__ . '../paypal/sample/bootstrap.php';
    }

    //redirect if needed, otherwise display the user list
    function index()
    {
      $user = $this->ion_auth->user()->row();
      
      
            //check subscription JVZOO
      $this->load->model('members_model');
      $member = $this->members_model->get_subscription_details($user->id);

        if ( $member ) {
              $check_status = array('ACTIVE','PENDING');
          if (in_array(strtoupper($member->p_status), $check_status)) {
            redirect('dashboard', 'refresh');
          }
        }
        
      //check user has paypal data
      $sql = "select id,user_id,plan_id,return_id,p_status from paypal where user_id = '$user->id' and ppstatus = '1' LIMIT 1";
      $get_ppal = $this->db->query($sql);
      if($get_ppal->num_rows() == 0){
            $this->promocode();
      }else{
        $gp = $get_ppal->row_array();
        $gp['p_status'] = strtoupper($gp['p_status']);


        if($gp['p_status'] == 'CREATED' or $gp['p_status'] == ''){
            //$check_billing_details =  file_get_contents($this->baseurl ."paypal.php?p=check_billing_details&id={$gp['plan_id']}");
            $check_billing_details =  file_get_contents("http://localhost/paypal.php?p=check_billing_details&id={$gp['plan_id']}&uid=".$user->id);
            $confirm_obj = (array) json_decode($check_billing_details);
            if($confirm_obj['state'] == 'ACTIVE'){
              $_SESSION['plan_id'] = $gp['plan_id'];
              redirect($this->baseurl . "paypal.php?p=confirm_plan&uid=".$user->id, 'refresh');
              //$sql = "delete from paypal where user_id = '$user->id' and id = '{$gp['id']}'";
              //$del_q = $this->db->query($sql);
              //$this->promocode();
            }else{
              $sql = "delete from paypal where user_id = '$user->id' and id = '{$gp['id']}'";
              $del_q = $this->db->query($sql);
              $this->promocode();
            }
             //$_SESSION['plan_id'] = $gp['plan_id'];
            //redirect($this->baseurl . "paypal.php?p=confirm_plan", 'refresh');

        }
        elseif( strtoupper($gp['p_status']) == "ACTIVE" or strtoupper($gp['p_status']) == "PENDING")
        {
             redirect("dashboard", 'refresh');
        } else{
           $this->promocode();
        }

      }
    }



    function validation(){

      $user = $this->ion_auth->user()->row();
      //check user has paypal data
      $sql = "select id,user_id,plan_id,return_id,p_status from paypal where user_id = '$user->id' and ppstatus = '1' LIMIT 1";
        // $sql = "select id,user_id,plan_id,return_id,p_status from paypal where user_id = '945' and ppstatus = '1' LIMIT 1";
      $get_ppal = $this->db->query($sql);
      if($get_ppal->num_rows() == 0){
            $this->create_subscription();
      }else{
        $gp = $get_ppal->row_array();
        $gp['p_status'] = strtoupper($gp['p_status']);

        if($gp['p_status'] == 'CREATED' or $gp['p_status'] == ''){
            //$check_billing_details =  file_get_contents($this->baseurl ."paypal.php?p=check_billing_details&id={$gp['plan_id']}");
            $check_billing_details =  file_get_contents("http://localhost/paypal.php?p=check_billing_details&id={$gp['plan_id']}&uid=".$user->id);
            $confirm_obj = (array) json_decode($check_billing_details);

            if($confirm_obj['state'] == 'ACTIVE'){
              $_SESSION['plan_id'] = $gp['plan_id'];
              redirect($this->baseurl . "paypal.php?p=confirm_plan&uid=".$user->id, 'refresh');
            }else{
              $sql = "delete from paypal where user_id = '$user->id' and id = '{$gp['id']}'";
              $del_q = $this->db->query($sql);
              $this->create_subscription();
            }
             //$_SESSION['plan_id'] = $gp['plan_id'];
            //redirect($this->baseurl . "paypal.php?p=confirm_plan", 'refresh');

        }
        elseif( strtoupper($gp['p_status']) == "ACTIVE" or strtoupper($gp['p_status']) == "PENDING")
        {
             redirect("dashboard", 'refresh');
        } else{
           $this->create_subscription();
        }

      }
    }

    function create_subscription(){

      if(!$this->ion_auth->logged_in()){
          return show_error('You are not logged in, please login to continue');
      }else{
         $user = $this->ion_auth->user()->row();
         redirect($this->baseurl . "paypal.php?p=subscribe&uid=".$user->id, 'refresh');
      }
    }

    function confirm_plan(){

        $plan_id                = $_SESSION['plan_id'];
        $plan_created           = $_SESSION['plan_created'];

        $original_payment_amt   = $_SESSION['original_payment_amt'];
        $discount_per           = $_SESSION['discount_per'];
        $discount_amt           = $_SESSION['discount_amt'];
        $promo_code_id           = $_SESSION['promo_code_id'];

        if($discount_amt_per == "") { $discount_amt_per = 0;}
        if($discount_amt == "") { $discount_amt = 0;}
        if($discount_per == "") { $discount_per = 0;}


        if($plan_id == ""){
          $o['baseurl'] = $this->baseurl;
            $this->data['o'] = $o; 
            $this->data['session_error_table'] = true;
            $this->_render_page('signup/confirm_plan', $this->data);         
        }
        else{
                $user = $this->ion_auth->user()->row();

                if($user->advanced_pay_date != 0 && $user->advanced_pay_amt != 0){
                  $is_advance_payer = 1;
                }else{
                  $is_advance_payer = 0;
                }
              //check user has paypal data
              $sql = "select id,user_id,plan_id,return_id,p_status,original_amt,discount_per,discount_amt,promo_code_id from paypal where user_id = '$user->id' and plan_id = '$plan_id' and ppstatus = '1' LIMIT 1";
              
            $get_ppal = $this->db->query($sql);
              if($get_ppal->num_rows() == 0){

                   $data = array(
                       'user_id' => $user->id,
                       'plan_id' => $plan_id ,
                       'p_status' => $plan_created['state'],
                       'ppstatus' => 1,
                       'is_advance_payer' => $is_advance_payer,
                       'advance_pay_date' => $user->advanced_pay_date,
                       'date_added' => time(),
                       'original_amt' => $original_payment_amt,
                       'discount_per' => $discount_per,
                       'discount_amt' => $discount_amt,
                       'promo_code_id'=> $promo_code_id
                    );
                    $this->db->insert('paypal', $data); 

                    $pp_data['id']            = $this->db->insert_id();
                    $pp_data['original_amt']  = $original_payment_amt;
                    $pp_data['discount_per']  = $discount_per;
                    $pp_data['discount_amt']  = $discount_amt;
                    $pp_data['promo_code_id'] = $promo_code_id;
                    $this->data['ppd'] = $pp_data;

              }else{
                  $pp_data = $get_ppal->row_array();
                  $promo_code_id = $pp_data['promo_code_id'];
                  
                  $this->data['ppd'] = $pp_data;
                 
              }

              // check promo code 
              $sql = "select * from promo_code where promo_code_id = '$promo_code_id' LIMIT 1";
              $check_promo_code = $this->db->query($sql);
              if($check_promo_code->num_rows() > 0){
                $this->data['cpc'] = $check_promo_code->row_array();
              }
              $o['baseurl'] = $this->baseurl;
              $this->data['o'] = $o; 
          
                $this->data['is_subscription_table'] = true;

                $this->data['plan_created'] = $plan_created;
                $this->data['confirm_table'] = true;
                $this->_render_page('signup/confirm_plan', $this->data);
        }
    }

    function send_confirmation(){
        $user         = $this->ion_auth->user()->row();
        $p              = $this->input->post('p');
        $pp_id          = $this->input->post('pp_id');
        $plan_id        = $_SESSION['plan_id'];
        $plan_created   = $_SESSION['plan_created'];
        $user = $this->ion_auth->user()->row();
        if($plan_id == ""){
            $this->data['session_error_table'] = true;
            $this->_render_page('signup/confirm_plan', $this->data);         
        }else{
            if($p == "cancel"){
                $_SESSION['plan_id'] = "";
                $_SESSION['plan_created'] = "";
                //$this->data['cancel_table'] = true;
                //$o['baseurl'] = $this->baseurl;
                //$this->data['o'] = $o; 
                //$this->_render_page('signup/confirm_plan', $this->data);  
                $sql = "delete from paypal where user_id = '$user->id' and id = '$pp_id'";
                $del_q = $this->db->query($sql);
               redirect($this->baseurl . "subscription", 'refresh'); 
                      
            }
            if($p == "confirm"){
                 redirect($this->baseurl . "paypal.php?p=confirm_payment&uid=".$user->id, 'refresh');
            }            
        }
}
function final_details()
{
    $user = $this->ion_auth->user()->row();
    $plan_id        = $_SESSION['plan_id'];
    $plan_created   = $_SESSION['plan_created'];
    $final_details  = $_SESSION['final_details'];  

        if($plan_id == "" and $this->ion_auth->logged_in()){
            $this->data['session_error_table'] = true;
            $this->_render_page('signup/confirm_plan', $this->data);         
        }else{
            
            //check user has paypal data
            

            $sql = "select * from paypal 
                    where 
                        user_id = '$user->id' and plan_id = '$plan_id' and ppstatus = '1' LIMIT 1";
                        
            $get_ppal = $this->db->query($sql);
            if($get_ppal->num_rows() == 0){
               return show_error('Error occured, please try again');
            }else{
                $gp = $get_ppal->row_array();
            
                if(strtoupper($gp['p_status']) == 'CREATED'){
                    $this->db->trans_start();

                    //next_billing_date_check + 1 day
                    $next_bill_unix = strtotime($final_details['agreement-details']->next_billing_date);
                    $next_billing_date_check =  date("Y-m-d", $next_bill_unix);  // 1 day
                    //$aff_date_check = date("Y-m-d", $next_bill_unix  + (60 * 60 * 24 * 6));  // 7 day
                    
                    $get_date_today = time();
                    $nextMonday= strtotime("next Monday",$get_date_today);
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
                       'return_id' => $final_details['id'],
                       'p_status' => strtoupper($final_details['state']),
                       'start_date' => $final_details['start_date'],    
                       'amt' => $final_details['plan']->payment_definitions[0]->amount->value,
                       'curr' => $final_details['plan']->payment_definitions[0]->amount->currency,
                       'date_confirmed' => time(),
                       'next_billing_date' => $final_details['agreement-details']->next_billing_date,
                       'next_billing_date_check' => $next_billing_date_check,
                       'pay_cycle' => 1,
                       'aff_date_check' => $aff_date_check,
                       'is_aff_check' => 0
                        );

                    $this->db->where('id', $gp['id']);
                    $this->db->update('paypal', $data);
                    
                    if ( $this->ion_auth->in_group(3) ) {
                         $this->ion_auth->remove_from_group(3, $user->id);
                         $this->ion_auth->add_to_group(2, $user->id);
                    }

                    $this->db->trans_complete();
                    
                    $o['baseurl'] = $this->baseurl;
                    $this->data['o'] = $o;  
                    $this->data['finish_table'] = true;
                    $this->_render_page('signup/pp_details.php', $this->data);
                }
                else{
                    if ( strtoupper($gp['p_status']) == "ACTIVE" ) {
                          redirect('dashboard');
                    }
                    else {
                          return show_error('Error occured, please try again');
                    }
                }


                
            }       
        }  

}

function cancel_subscription(){
      $user = $this->ion_auth->user()->row();
      $p  = $this->input->post('p');
      //check user has paypal data
      $sql = "select id,user_id,plan_id,return_id,p_status from paypal where user_id = '$user->id' and p_status = 'ACTIVE' and ppstatus = '1' LIMIT 1";
      $get_ppal = $this->db->query($sql);
      if($get_ppal->num_rows() == 0){
           return show_error('Error occured, please try again');
      }else{
        $gp = $get_ppal->row_array();
        $_SESSION['plan_id'] = $gp['plan_id'];
        $_SESSION['return_id'] = $gp['return_id'];
        $this->data['gp'] = $gp;

        if($p == "confirm_cancel"){
            redirect("$this->baseurl/paypal.php?p=confirm_cancel_payment&uid=".$user->id, 'refresh');
        }else{
          
          $o['baseurl'] = $this->baseurl;
          $this->data['o'] = $o; 
           $this->data['is_subscription_table'] = true;
            $this->data['cancel_confirm'] = true;
            $this->_render_page('signup/cancel_subscription.php', $this->data);
        }
    }   
}

function cancel_complete(){
   $user = $this->ion_auth->user()->row();  
   $plan_id       = $_SESSION['plan_id'];
   $plan_state    = $_SESSION['plan_state'];

   if($plan_state == "DELETED"){
        
     //check user has paypal data
        $sql = "select * from paypal 
                where 
                    user_id = '$user->id' and plan_id = '$plan_id' and ppstatus = '1' LIMIT 1";
        $get_ppal = $this->db->query($sql);
        if($get_ppal->num_rows() == 0){
           return show_error('Error occured, please try again');
        }else{
            $gp = $get_ppal->row_array();
        
                $data = array(
                    'ppstatus' => 2,
                    'p_status' => $plan_state,
                     'date_cancelled' => time()
                    );

                $this->db->where('id', $gp['id']);
                $this->db->update('paypal', $data);
                
                $_SESSION['plan_id']        = "";
                $_SESSION['plan_state']     = "";
                $_SESSION['plan_created']   = "";
                $_SESSION['final_details']  = "";
                $_SESSION['return_id']  = "";
        
                $_SESSION['original_payment_amt'] = "";
                $_SESSION['discount_per'] = "";
                $_SESSION['discount_amt'] = "";
                $_SESSION['promo_code_id'] = "";    
        
        $o['baseurl'] = $this->baseurl;
        $this->data['o'] = $o;    
         $this->data['is_subscription_table'] = true;
                $this->data['cancel_complete_table'] = true;
                $this->_render_page('signup/cancel_subscription.php', $this->data);
            }      
       }
}

/* PAYPAL EXPRESS FOR FILE UPLOAD */

function video_upload_payment(){
    
    $_SESSION['exp_pay_detail']     = "";
    $_SESSION['get_return_payment'] = "";
        
   $user = $this->ion_auth->user()->row();

      if(!$this->ion_auth->logged_in()){
          return show_error('You are not logged in, please login to continue');
      }else{
        $sql = "select id,user_id from paypal_exp where user_id = '$user->id' and ppstatus = 'created' LIMIT 1";
        $get_ppal = $this->db->query($sql);
        if($get_ppal->num_rows() == 0){
          redirect($this->baseurl . "paypal_exp.php?p=create_payment", 'refresh');
        }else{
          $show_pending = $get_ppal->row_array();

          $this->db->delete('paypal_exp', array('id' => $show_pending['id'])); 
          redirect($this->baseurl . "paypal_exp.php?p=create_payment", 'refresh');
        }
    }
}

function confirm_express_payment(){
  $user = $this->ion_auth->user()->row();
  $exp_pay_detail  = $_SESSION['exp_pay_detail'];  
  if(!$this->ion_auth->logged_in()){
      return show_error('You are not logged in, please login to continue');
  }else{
        $invoice_id = $exp_pay_detail['transactions'][0]->invoice_number;
        $amt = $exp_pay_detail['transactions'][0]->amount->total;
        $curr = $exp_pay_detail['transactions'][0]->amount->currency;
       $data = array(
           'user_id' => $user->id ,
           'invoice_id' => $invoice_id,
           'ppstatus' => $exp_pay_detail['state'],
           'date_added' => time(),
           'amt' => $amt,
           'curr' => $curr,
           'is_read' => 1,
           'pp_id'=> $exp_pay_detail['id']
        );
        $this->db->insert('paypal_exp', $data); 

        redirect($exp_pay_detail['links'][1]->href, 'refresh');

  }
}
function express_finalized_payment(){
  $user               = $this->ion_auth->user()->row();
  $exp_pay_detail     = $_SESSION['exp_pay_detail'];  
  $get_return_payment = $_SESSION['get_return_payment'];
  if(!$this->ion_auth->logged_in() && $get_return_payment == ""){
      return show_error('You are not logged in, please login to continue');
  }else{
    $this->db->trans_start();

    $trans_id = $get_return_payment['id'];
    $sql = "select id,pp_id,ppstatus,user_id from paypal_exp where ppstatus = 'created' and pp_id='$trans_id' and user_id = '$user->id' LIMIT 1";
    $check_exp = $this->db->query($sql);
    if($check_exp->num_rows == 0){
       return show_error('Invalid transaction ID, please try again');
    }else{
    
      $get_det = $check_exp->row_array();
      $transaction_sales = $get_return_payment['transactions'][0]->related_resources[0]->sale->id;
      $data = array(
               'ppstatus' => $get_return_payment['state'],
               'pp_return_id' => $transaction_sales
            );
      $this->db->where('id', $get_det['id']);
      $this->db->update('paypal_exp', $data); 

        //echo "redirect to upload video template";
       
        //clear sessions
        $_SESSION['exp_pay_detail']     = "";
        $_SESSION['get_return_payment'] = "";
    }
    $this->db->trans_complete();
  redirect($this->baseurl . "dashboard/upload_videos", 'refresh');
  }
}

function express_cancel_payment(){
      //clear sessions
      $_SESSION['exp_pay_detail']     = "";
      $_SESSION['get_return_payment'] = "";  
    echo "redirect to cancel template";
}

// PROMO CODE HERE!

    function promocode(){
       $this->template->add_js('modules/promocode.js'); 
     
      $user = $this->ion_auth->user()->row();
      $p  = $this->input->post('p');

      if($p == "cancel"){
        $this->validation();
      }
      $o['baseurl'] = $this->baseurl;
      $this->data['o'] = $o; 
      
      $this->data['open_review_table_from_sub'] = true;              
      $this->data['is_subscription_table'] = true;
      $this->data['plan_created'] = $plan_created;
      $this->data['confirm_table'] = true;
      $this->_render_page('signup/promocode', $this->data);
    }

	    function show_promocode(){

       $this->template->add_js('modules/promocode.js'); 
       $user = $this->ion_auth->user()->row();
       if($user->use_promocode == 0){

        $o['enter_promocode_table'] = true;
        $o['baseurl'] = $this->baseurl;    
        $this->data['o'] = $o;
        echo  "ENTERPROMOCODE|" . $this->load->view('signup/div/enter_promocode', $this->data);  
       }else{
          $promo_code_id = $user->use_promocode;  

          $sql = "select discount_amt,is_onetime, option_desc from promo_code where promo_code_id = '$promo_code_id' LIMIT 1";
          $check_promo = $this->db->query($sql);
          if($check_promo->num_rows() > 0){
            $cp = $check_promo->row_array();
           //$message = 'SUCCESS|<div class="alert alert-success" role="alert">Congratulations! You received ' . $cp['discount_amt'] .'% Discount! <br>Note: '. $cp['option_desc'] .'<br> <br><p><a href="' . $this->baseurl . 'subscription/validation" class="btn btn-primary btn-sm">Continue with Discount</a> &nbsp; <a href="#"  class="btn btn-danger btn-sm" onclick="remove_promo_confirm(\''. $promo_code_id.'\');">Remove Promo code</a></p></div>';     
           //$message = 'SUCCESS|<div class="alert alert-success" role="alert">Congratulations! You received ' . $cp['discount_amt'] .'% Discount! <br><br> <p><a href="' . $this->baseurl . 'subscription/validation" class="btn btn-primary btn-sm">NEXT</a> &nbsp; </p></div>';     
            //unlimited 100% 
            if($cp['is_ontime'] == 0 && $cp['discount_amt'] == 100){
              $message = 'SUCCESS|<div class="alert alert-success" role="alert">Congratulations! You received ' . $cp['discount_amt'] .'% Lifetime Discount! <br><br> <p><a href="' . $this->baseurl . 'dashboard/" class="btn btn-primary btn-sm">Continue to Dashboard</a> &nbsp; </p></div>';  
            }else{
              $message = 'SUCCESS|<div class="alert alert-success" role="alert">Congratulations! You received ' . $cp['discount_amt'] .'% Discount! <br><br> <p><a href="' . $this->baseurl . 'subscription/validation" class="btn btn-primary btn-sm">NEXT</a> &nbsp; </p></div>';  
            }

          }else{
             $message = 'NOTAVAILABLE|<div class="alert alert-danger" role="alert">Promo code not available. please click remove button and try again. <br><br> <p><a href="#"  class="btn btn-danger btn-sm" onclick="remove_promo_confirm(\''. $promo_code_id.'\');">Remove Promo code</a></p></div>';     
          }
          echo "$message";
       }
    }

    function submit_promo_code(){
        $user = $this->ion_auth->user()->row();
        $plan_price  = $this->input->post('plan_price');
        $promo_code  = $this->input->post('promo_code');

   

      $date_today = date("Y-m-d",time());
      $sql = "select * from promo_code where secret_code = '$promo_code' 
                and is_live = '1' and (start_date <= '$date_today'  and end_date >= '$date_today') LIMIT 1";
      
      $check_code = $this->db->query($sql);
      if($check_code->num_rows() == 0)
      {           
          $response = "error|Invalid or expired code, please try again";
      }
      else
      {
        $show_code        = $check_code->row_array();
        $other_option     = $show_code['other_option'];
        $option_desc      = $show_code['option_desc'];
        $num_claim        = $show_code['num_claim'];
        $claim_count      = $show_code['claim_count'];
        $discount_amt     = $show_code['discount_amt'];
        $promo_code_id    = $show_code['promo_code_id'];         

        $date_added = time();
        if($num_claim == 0)
        {
          $sql = "select user_id from promo_code_claimed where user_id = '$user->id' and secret_code_id = '$promo_code_id' LIMIT 1";
          $check_code = $this->db->query($sql);
       
          if($check_code->num_rows() == 0){
            

            $this->db->trans_start();
            $this->db->query("INSERT IGNORE INTO promo_code_claimed set user_id = '$user->id', secret_code_id = '$promo_code_id', date_added = '$date_today'");        
            
            //$message = 'success|Congratulations! You received ' . $discount_amt .'% Discount! <br>Note: . ' . $option_desc .'<br><br> <p><a href="' . $this->baseurl . 'subscription/validation" class="btn btn-primary btn-sm">Continue width Discount</a> </p>';         
            if($show_code['is_ontime'] == 0 && $show_code['discount_amt'] == 100){
              $message = 'SUCCESS|<div class="alert alert-success" role="alert">Congratulations! You received ' . $cp['discount_amt'] .'% Lifetime Discount! <br><br> <p><a href="' . $this->baseurl . 'dashboard/" class="btn btn-primary btn-sm">Continue to Dashboard</a> &nbsp; </p></div>';  
            }else{
              $message = 'success|Congratulations! You received ' . $discount_amt .'% Discount! <br><br>  <p><a href="' . $this->baseurl . 'subscription/validation" class="btn btn-primary btn-sm">NEXT</a> </p>';
              //$message = 'success|Congratulations! You received ' . $discount_amt .'% Discount! <br>Note: . ' . $option_desc .'<br><br>  <p><a href="' . $this->baseurl . 'subscription/validation" class="btn btn-primary btn-sm">Continue width Discount</a> &nbsp; <a href="#"  class="btn btn-danger btn-sm" onclick="remove_promo_confirm(\''. $promo_code_id.'\');">Remove Promo code</a></p>';    
            }   
            
            $response = "$message";

            $new_claim_count = $claim_count + 1;
            $data = array('claim_count' => $new_claim_count);   
            $this->db->simple_query("SET NAMES 'utf-8'");  
            $data_where = array('promo_code_id'=> $promo_code_id);    
            $this->db->set($data);
            $this->db->update('promo_code', $data, $data_where);
            $this->db->trans_complete();
          }
          else{
            $message = 'error|You already used this code, please try again';
            $response = "$message";   
          }     
          
        }
        elseif($num_claim != 0 and ($num_claim > $claim_count))
        {
          $this->db->trans_start();
          $sql = "select user_id from promo_code_claimed where user_id = '$user->id' and secret_code_id = '$promo_code_id' LIMIT 1";
          $check_code = $this->db->query($sql);
          
          if($check_code->num_rows() == 0){
    
            $this->db->query("INSERT IGNORE INTO promo_code_claimed set user_id = '$user->id', secret_code_id = '$promo_code_id', date_added = '$date_today'");        
            
            //$message = 'success|Congratulations! You received ' . $discount_amt .'% Discount! <br>Note: . ' . $option_desc .'<br><br>  <p><a href="' . $this->baseurl . 'subscription/validation" class="btn btn-primary btn-sm">Continue with Discount</a> &nbsp; <a href="#"  class="btn btn-danger btn-sm" onclick="remove_promo_confirm(\''. $promo_code_id.'\');">Remove Promo code</a></p>';   
          //  $message = 'success|Congratulations! You received ' . $discount_amt .'% Discount! <br>Note: . ' . $option_desc .'<br><br>  <p><a href="' . $this->baseurl . 'subscription/validation" class="btn btn-primary btn-sm">Continue with Discount</a> </p>';   
            if($show_code['is_ontime'] == 0 && $show_code['discount_amt'] == 100){
              $message = 'SUCCESS|<div class="alert alert-success" role="alert">Congratulations! You received ' . $cp['discount_amt'] .'% Lifetime Discount! <br><br> <p><a href="' . $this->baseurl . 'dashboard/" class="btn btn-primary btn-sm">Continue to Dashboard</a> &nbsp; </p></div>';  
            }else{            
              $message = 'success|Congratulations! You received ' . $discount_amt .'% Discount! <br><br>  <p><a href="' . $this->baseurl . 'subscription/validation" class="btn btn-primary btn-sm">NEXT</a> </p>';   
            } 
            $response = "$message";

            //if 100 % discount FOREVER!
            if($show_code['is_ontime'] == 0 && $show_code['discount_amt'] == 100){   
              $sql = $this->db->query("delete from users_groups where user_id = '$user->id' and group_id = '3'");
            }
            
            $new_claim_count = $claim_count + 1;
            $data = array('claim_count' => $new_claim_count);   
            $this->db->simple_query("SET NAMES 'utf-8'");  
            $data_where = array('promo_code_id'=> $promo_code_id);    
            $this->db->set($data);
            $this->db->update('promo_code', $data, $data_where);

            //add to user
            $data = array('use_promocode' => $promo_code_id);   
            $this->db->simple_query("SET NAMES 'utf-8'");  
            $data_where = array('id'=> $user->id);    
            $this->db->set($data);
            $this->db->update('users', $data, $data_where);
           

          }
          else{
            $message = 'error|You already used this code, please try again';
            $response = "$message";   
          }
           $this->db->trans_complete();
        }
        else
        {
          $message = 'error|The Code you are using is already used, please try again';
          $response = "$message";       
        }        
      }
      

      echo $response;
    }

    function remove_promo(){
        $user = $this->ion_auth->user()->row();
        $promo_code  = $this->input->post('promo_code');
        $date_today = date("Y-m-d",time());
        $this->db->trans_start();
       
        $sql = "select * from promo_code where promo_code_id = '$promo_code' 
          and is_live = '1' and (start_date <= '$date_today'  and end_date >= '$date_today') LIMIT 1";

        $check_code = $this->db->query($sql);
        if($check_code->num_rows() > 0)
        {           
          $show_code        = $check_code->row_array();
  
          $new_claim_count = $show_code['claim_count'] - 1;

          if($new_claim_count <= 0){
            $new_claim_count = 0;
          }


          //if 100 % discount FOREVER!
          if($show_code['is_ontime'] == 0 && $show_code['discount_amt'] == 100){   
                 $data = array(
                     'user_id' => $user->id,
                     'group_id' => 3
                  );
                  $this->db->insert('users_groups', $data); 

          }
          
          $data = array('claim_count' => $new_claim_count);   
          $this->db->simple_query("SET NAMES 'utf-8'");  
          $data_where = array('promo_code_id'=> $promo_code);    
          $this->db->set($data);
          $this->db->update('promo_code', $data, $data_where);

          //add to user
          $data = array('use_promocode' => 0);   
          $this->db->simple_query("SET NAMES 'utf-8'");  
          $data_where = array('id'=> $user->id);    
          $this->db->set($data);
          $this->db->update('users', $data, $data_where);

          //delete row from promo code claimed
          $sql = $this->db->query("delete from promo_code_claimed where user_id = '$user->id' and secret_code_id = '$promo_code'");

        }
        $this->db->trans_complete();
      

    }
// END OF PROMOCODE
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