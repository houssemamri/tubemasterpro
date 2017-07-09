<?php defined('BASEPATH') OR exit('No direct script access allowed');
error_reporting(0);
class Affiliateadmin extends MX_Controller {

    public $data;

    function __construct()
    {
        parent::__construct();
        $this->load->library('authentication', NULL, 'ion_auth');
        $this->load->library('form_validation');
        $this->load->helper('url','form');
        $this->load->library('template');
        $this->template->add_js('contactform.js');
        $this->load->database();

        $this->form_validation->set_error_delimiters($this->config->item('error_start_delimiter', 'ion_auth'), $this->config->item('error_end_delimiter', 'ion_auth'));

        $this->lang->load('auth');
        $this->load->helper('language');
        $this->load->helper('captcha');

        $this->baseurl = $this->config->config['base_url']; 
        $this->group_name = array('video_admin','admin');
        $this->user  = $this->ion_auth->user()->row();

        // $this->smtp_host = "box342.bluehost.com";
        // $this->smtp_user = "nathan@nathanhague.com";
        // $this->smtp_pass = "$Wolfman1";  
        $this->smtp_host = "localhost";
    }

    function index(){
        $user = (array) $this->user;
        $this->check_permission();

        $this->pending();      
    }

    function pending(){
        $user = (array) $this->user;
        $this->check_permission();

        $sess_msg       = $this->session->flashdata('msg');
        $sess_msg_type  = $this->session->flashdata('msg_type');
        if($sess_msg){
            $msg = $sess_msg;
            $msg_type = $sess_msg_type;
        }


        $count_affiliate_request = $this->count_user_affiliate(0);
        $o['count_affiliate_request'] = number_format($count_affiliate_request,0);

        $count_affiliate_approved = $this->count_user_affiliate(1);
        $o['count_affiliate_approved'] = number_format($count_affiliate_approved,0);        

        $count_affiliate_rejected = $this->count_user_affiliate(2);
        $o['count_affiliate_rejected'] = number_format($count_affiliate_rejected,0); 


        /* check pending*/
        $sql = "select id,first_name, last_name, aff_added 
                from users where aff_status = 'pending' and is_aff_tos = '1' order by id asc";
           
        $check_pending = $this->db->query($sql);
        if($check_pending->num_rows() == 0){
            $msg = "No Pending videos found";
            $msg_type = 'warning';
        }else{
           $show_pend = array();
           foreach($check_pending->result_array() as $cp){
                $cp['aff_added'] = date('m-d-Y H:i a', $cp['aff_added']);
                $cp['user_name'] = $cp['first_name'] . " " . $cp['last_name'];
                $show_pend[] = $cp;
           }
           $o['show_pend'] = $show_pend;

           $o['show_table_list'] = true;

        }
        $o['affiliate_admin_header'] = true;
        $o['msg_type'] = $msg_type;
        $o['msg'] = $msg;
        $o['title'] = "Pending Affiliate Request";
        $o['page'] = 'pending';
        $o['user'] = $user;
        $o['baseurl'] = $this->baseurl;    
        $this->data['o'] = $o;
        $this->load->view('videoadmin/affadmin', $this->data);         
    }


    function check_request(){
        $p          = strtolower($this->input->post('p'));
        $user = (array) $this->user;
        $this->check_permission(); 
         $id   = $this->uri->segment(3);

        $count_affiliate_request = $this->count_user_affiliate(0);
        $o['count_affiliate_request'] = number_format($count_affiliate_request,0);

        $count_affiliate_approved = $this->count_user_affiliate(1);
        $o['count_affiliate_approved'] = number_format($count_affiliate_approved,0);

        $count_affiliate_rejected = $this->count_user_affiliate(2);
        $o['count_affiliate_rejected'] = number_format($count_affiliate_rejected,0); 
       

        $sql = "select id,first_name,last_name,company,phone,mobile,country,whatsapp,aff_status,email,
                    facebook,twitter,google,linkedin,website,aff_added,paypal_email,aff_notes
                from 
                    users where id = '$id' and aff_status in ('pending','rejected') LIMIT 1";
        $check_pending = $this->db->query($sql);
        if($check_pending->num_rows() == 0){
            $msg        = "Invalid ID, please try again";
            $msg_type   = 'danger'; 
            $path       = "affiliateadmin/";
            $this->redirect_link($path,$msg,$msg_type); 

        }else{

            $u = $check_pending->row_array();
            $o['gc'] = $u;
 

             
            if($p == "submit_request")
            {
                $this->db->trans_start();
                
                $upload_status       = $this->cleanup($this->input->post('upload_status'));   
                $user_request_id     = $this->cleanup($this->input->post('user_request_id'));   
                $notes               = $this->cleanup($this->input->post('notes'));    
                if($upload_status == 'approved'){
                    $create_random_words = $user_request_id . "" . random_string('alnum', 6);
                }else{
                    $create_random_words = '';
                }
                $data = array(
                   'is_aff' => $create_random_words,
                   'aff_notes' => $notes,
                   'aff_status' => $upload_status
                );     
                $this->db->where('id', $user_request_id);
                $this->db->update('users', $data);
                $this->db->trans_complete();
                

                if($upload_status != 'pending'){
                /* SEND EMAIL TO USER */
                    $this->load->library('email');
                    $config['protocol'] = 'smtp';
                    $config['smtp_host'] = $this->smtp_host;
                    // $config['smtp_user'] = $this->smtp_user;
                    // $config['smtp_pass'] = $this->smtp_pass;
                    // $config['smtp_port'] = '26';

                    $config['smtp_port'] = '25';
                    $config['charset'] = 'iso-8859-1';
                    $config['wordwrap'] = TRUE;
                    $config['mailtype'] = 'html';

                    $this->email->initialize($config);
                    if($notes != ""){
                        $additional_notes = "<br><br> Additional Notes: <br> $notes";
                    }
                    if($upload_status == "rejected"){
                        $subject = "Sorry - Your Affiliate Application UNSUCCESSFUL";
                        $content = "Hi {$u['firstn_name']} <br><br> Sorry, but after careful review of your application to be a TubeMasterPro Affiliate, we have decided to not to award you Affiliate status. <br><br> Our Reason:<br> $additional_notes<br><br> This decision is final. <br><br>We wish you all the best!<br><br> - TubeMasterPro Team";
                    }
                    if($upload_status == "approved"){
                        $subject = "SUCCESS! Affiliate Application APPROVED!";
                        $content = "Hi {$u['firstn_name']} <br><br> Just a quick note to say \"well done!\" on becoming a Trusted Affiliate of TubeMasterPro - very much appreciated. <br><br> When you login to your account now, you will see your AFFILIATE menu now live. You can use that link to start selling. <br><br> Any issues, please use the SUPPORT messing system - we have a dedicated room for Affiliates or you can chat one on one with us here at Support. <br><br> Happy Selling {$u['firstn_name']}! <br><br> - TubeMasterPro Team";
                    }                
                    $this->email->from("support@tubemasterpro.com", "TubeMasterPro");
                    $this->email->to("{$u['email']}"); 
                    //$this->email->to('info@topappstoday.com'); 
                    $this->email->subject("$subject");
                    $this->email->message("$content");  
					$this->email->send();
                }

                $this->session->set_flashdata('msg', 'Update successful');
                $this->session->set_flashdata('msg_type', 'success'); 
                redirect($this->baseurl . "affiliateadmin", 'refresh');
            }

            $o['affiliate_admin_header'] = true;
            $o['show_request_table'] = true;
            $o['msg_type'] = $msg_type;
            $o['msg'] = $msg;
            $o['title'] = "Pending Affiliate Request";

            $o['page'] = $u['aff_status'];
            $o['user'] = $user;
            $o['baseurl'] = $this->baseurl;    
            $this->data['o'] = $o;
            $this->load->view('videoadmin/affadmin', $this->data);    
        } 
    }

    function rejected(){
        $user = (array) $this->user;
        $this->check_permission();

        $sess_msg       = $this->session->flashdata('msg');
        $sess_msg_type  = $this->session->flashdata('msg_type');
        if($sess_msg){
            $msg = $sess_msg;
            $msg_type = $sess_msg_type;
        }


        $count_affiliate_request = $this->count_user_affiliate(0);
        $o['count_affiliate_request'] = number_format($count_affiliate_request,0);

        $count_affiliate_approved = $this->count_user_affiliate(1);
        $o['count_affiliate_approved'] = number_format($count_affiliate_approved,0);        

        $count_affiliate_rejected = $this->count_user_affiliate(2);
        $o['count_affiliate_rejected'] = number_format($count_affiliate_rejected,0); 


        /* check pending*/
        $sql = "select id,first_name, last_name, aff_added, aff_status 
                from users where aff_status = 'rejected' order by id asc";
           
        $check_pending = $this->db->query($sql);
        if($check_pending->num_rows() == 0){
            $msg = "No Rejected users found";
            $msg_type = 'warning';
        }else{
           $show_pend = array();
           foreach($check_pending->result_array() as $cp){
                $cp['aff_added'] = date('m-d-Y H:i a', $cp['aff_added']);
                $cp['user_name'] = $cp['first_name'] . " " . $cp['last_name'];
                $show_pend[] = $cp;
           }
           $o['show_pend'] = $show_pend;

           $o['show_table_list'] = true;

        }
        $o['affiliate_admin_header'] = true;
        $o['msg_type'] = $msg_type;
        $o['msg'] = $msg;
        $o['title'] = "Rejected users";
        $o['page'] = 'rejected';
        $o['user'] = $user;
        $o['baseurl'] = $this->baseurl;    
        $this->data['o'] = $o;
        $this->load->view('videoadmin/affadmin', $this->data);   

    }
    function affiliate_member(){
        $user = (array) $this->user;
        $this->check_permission();

        $sess_msg       = $this->session->flashdata('msg');
        $sess_msg_type  = $this->session->flashdata('msg_type');
        if($sess_msg){
            $msg = $sess_msg;
            $msg_type = $sess_msg_type;
        }


        $count_affiliate_request = $this->count_user_affiliate(0);
        $o['count_affiliate_request'] = number_format($count_affiliate_request,0);

        $count_affiliate_approved = $this->count_user_affiliate(1);
        $o['count_affiliate_approved'] = number_format($count_affiliate_approved,0);        

        $count_affiliate_rejected = $this->count_user_affiliate(2);
        $o['count_affiliate_rejected'] = number_format($count_affiliate_rejected,0); 


        /* check pending*/
        $sql = "select id,first_name, last_name, aff_added 
                from users where aff_status = 'approved' order by id asc";
           
        $check_pending = $this->db->query($sql);
        if($check_pending->num_rows() == 0){
            $msg = "No Pending videos found";
            $msg_type = 'warning';
        }else{
           $show_pend = array();
           foreach($check_pending->result_array() as $cp){
                $cp['aff_added'] = date('m-d-Y H:i a', $cp['aff_added']);
                $cp['user_name'] = $cp['first_name'] . " " . $cp['last_name'];
                $show_pend[] = $cp;
           }
           $o['show_pend'] = $show_pend;

           $o['approved_users_table'] = true;

        }
        $o['affiliate_admin_header'] = true;
        $o['msg_type'] = $msg_type;
        $o['msg'] = $msg;
        $o['title'] = "Approved Affiliate Members";
        $o['page'] = 'approved';
        $o['user'] = $user;
        $o['baseurl'] = $this->baseurl;    
        $this->data['o'] = $o;
        $this->load->view('videoadmin/affadmin', $this->data);  
    }

    function view_details(){

        $p          = strtolower($this->input->post('p'));
        $user = (array) $this->user;
        $this->check_permission(); 
         $id   = $this->uri->segment(3);

        $count_affiliate_request = $this->count_user_affiliate(0);
        $o['count_affiliate_request'] = number_format($count_affiliate_request,0);

        $count_affiliate_approved = $this->count_user_affiliate(1);
        $o['count_affiliate_approved'] = number_format($count_affiliate_approved,0);    

        $count_affiliate_rejected = $this->count_user_affiliate(2);
        $o['count_affiliate_rejected'] = number_format($count_affiliate_rejected,0);        

        $sql = "select id,first_name,last_name,company,phone,mobile,country,whatsapp,aff_status,
                    facebook,twitter,google,linkedin,website,aff_added,paypal_email,aff_notes
                from 
                    users where id = '$id' and aff_status = 'approved' LIMIT 1";
        $check_pending = $this->db->query($sql);
        if($check_pending->num_rows() == 0){
            $msg        = "Invalid ID, please try again";
            $msg_type   = 'danger'; 
            $path       = "affiliateadmin/";
            $this->redirect_link($path,$msg,$msg_type); 

        }else{

            $u = $check_pending->row_array();
            $o['gc'] = $u;    
             $o['show_user_approved_table'] = true;
            $o['msg_type'] = $msg_type;
            $o['msg'] = $msg;
            $o['title'] = "Approved Affiliate Request";
            $o['page'] = 'approved';
            $o['user'] = $user;
            $o['baseurl'] = $this->baseurl;    
            $this->data['o'] = $o;
            $this->load->view('videoadmin/affadmin', $this->data);    
        }    
    }
    public function view_summary(){

        $p          = strtolower($this->input->post('p'));
        $user = (array) $this->user;
        $this->check_permission(); 
         $id   = $this->uri->segment(3);

        $count_affiliate_request = $this->count_user_affiliate(0);
        $o['count_affiliate_request'] = number_format($count_affiliate_request,0);

        $count_affiliate_approved = $this->count_user_affiliate(1);
        $o['count_affiliate_approved'] = number_format($count_affiliate_approved,0);    

         $count_affiliate_rejected = $this->count_user_affiliate(2);
        $o['count_affiliate_rejected'] = number_format($count_affiliate_rejected,0);       

        $sql = "select id,first_name,last_name,company,phone,mobile,whatsapp,aff_status,
                    facebook,twitter,google,linkedin,website,aff_added,is_aff,paypal_email
                from 
                    users where id = '$id' and aff_status = 'approved' LIMIT 1";
        $check_pending = $this->db->query($sql);
        if($check_pending->num_rows() == 0){
            $msg        = "Invalid ID, please try again";
            $msg_type   = 'danger'; 
            $path       = "affiliateadmin/";
            $this->redirect_link($path,$msg,$msg_type); 

        }else{

            $u = $check_pending->row_array();

            $sql = "select count(user_id) as count_aff from affiliates where user_id = '$id' LIMIT 1";
            $check_aff = $this->db->query($sql);
            if($check_aff->num_rows() == 0){
                $o['affiliate_count'] = 0;
            }else{
                $get_aff = $check_aff->row_array();
                $o['affiliate_count'] = number_format($get_aff['count_aff'],0);
            }

            /* check all approved */
            $sql = "select aff.user_id_aff,aff.date_added,
            (select CONCAT(first_name, ' ',last_name) from users where id = aff.user_id_aff LIMIT 1) as user_name,
            pp.amt,pp.curr,pp.p_status,pp.date_confirmed
            from 
            affiliates as aff,
            paypal as pp
            where 
                aff.user_id = '$id'
            and
                pp.user_id = aff.user_id_aff";

            $check_com = $this->db->query($sql);
            if($check_com->num_rows() == 0){
                $o['active_count_users'] = 0;
                $msg_type = 'danger'; 
                $msg = "No active affiliate users";
            }else{

                $o['active_count_users'] = $check_com->num_rows();
                $show_ud = array();
                foreach($check_com->result_array() as $cc){
                    $cc['date_confirmed'] = date('m-d-Y', $cc['date_confirmed']);
                    $show_ud[] = $cc;
                }
                $o['show_ud'] = $show_ud;
                $o['show_user_aff_table'] = true;
            }

            $o['affiliate_admin_header'] = true;
            $o['gc'] = $u;    
            $o['show_user_affiliate_table'] = true;
            $o['msg_type'] = $msg_type;
            $o['msg'] = $msg;
            $o['title'] = "Approved Affiliate Request";
            $o['page'] = 'approved';
            $o['user'] = $user;
            $o['baseurl'] = $this->baseurl;    
            $this->data['o'] = $o;
            $this->load->view('videoadmin/affadmin', $this->data);    
        }      
    }

    function payout(){
        
        $p          = strtolower($this->input->post('p'));
        $user = (array) $this->user;
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
                        ) 
                    as 
                        total_transaction 
                from 
                    aff_payout 
                where
                    aff_sent = '1' order by date_transaction desc";

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
        $o['affiliate_admin_header'] = true;
        $o['msg_type'] = $msg_type;
        $o['msg'] = $msg;
        $o['title'] = "Payout";
        $o['page'] = 'payout';
        $o['user'] = $user;
        $o['baseurl'] = $this->baseurl;    
        $this->data['o'] = $o;
        $this->load->view('videoadmin/payout', $this->data);    

    }

    function view_payout_date(){
        $p          = strtolower($this->input->post('p'));
        $user = (array) $this->user;
        $this->check_permission(); 
        $payout_date   = $this->uri->segment(3);

        $sql = "select distinct(receiver_id), ap.date_transaction, 
            (select count(pid) from aff_payout where receiver_id = ap.receiver_id and date_transaction = '$payout_date' LIMIT 1) as total_trans,
            (select SUM(amt) from aff_payout where receiver_id = ap.receiver_id and date_transaction = '$payout_date' LIMIT 1) as total_paid
        from 
            aff_payout as ap
        where 
        aff_sent = '1' 
        and 
            ap.date_transaction = '$payout_date'
        order by pid asc";

        $check_payout = $this->db->query($sql);
        if($check_payout->num_rows() == 0){
            $msg        = "Invalid date, please try again";
            $msg_type   = 'danger'; 
            $path       = "affiliateadmin/payout/";
            $this->redirect_link($path,$msg,$msg_type); 

        }else{
            $total_paid = 0;
            $total_transaction = 0;
            $show_payout = array();
            foreach($check_payout->result_array() as $cp){
                $aff_name = (array) $this->ion_auth->user($cp['receiver_id'])->row();
                $cp['aff_name'] = $aff_name['first_name'] . " " . $aff_name['last_name'];
                $total_paid = $total_paid + intval($cp['total_paid']);
                $total_transaction = $total_transaction + $cp['total_trans'];
                $show_payout[] = $cp;
            }
            $o['show_payout'] = $show_payout;
        }
        $o['affiliate_admin_header'] = true; 
        $o['total_transaction'] = $total_transaction;
        $o['total_paid'] = $total_paid;
        $o['gc'] = $u;    
        $o['show_payout_date_table'] = true;
        $o['msg_type'] = $msg_type;
        $o['msg'] = $msg;
        $o['title'] = "Payout date: $payout_date";
        $o['page'] = 'payout';
        $o['user'] = $user;
        $o['baseurl'] = $this->baseurl;    
        $this->data['o'] = $o;
        $this->load->view('videoadmin/payout', $this->data);   

    }

    function view_payout_details(){
        $p          = strtolower($this->input->post('p'));
        $user = (array) $this->user;
        $this->check_permission(); 
        $receiver_id   = $this->uri->segment(3);
        $payout_date   = $this->uri->segment(4);


        $sql = "select receiver_id,pid,date_transaction,amt,payout_status,payout_id,sender_id
        from 
            aff_payout as ap
        where 
        aff_sent = '1' 
        and 
            ap.date_transaction = '$payout_date'
        and 
            ap.receiver_id = '$receiver_id'
        order by pid asc";

        $check_payout = $this->db->query($sql);
        if($check_payout->num_rows() == 0){
            $msg        = "Invalid date, please try again";
            $msg_type   = 'danger'; 
            $path       = "affiliateadmin/payout/";
            $this->redirect_link($path,$msg,$msg_type); 

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
        }
        $o['affiliate_admin_header'] = true; 
        $o['total_transaction'] = $total_transaction;
        $o['total_paid'] = $total_paid;
        $o['gc'] = $u;    
        $o['show_payout_user_transaction_det'] = true;
        $o['msg_type'] = $msg_type;
        $o['msg'] = $msg;
        $o['title'] = "Payout details for $receiver_name - $payout_date";
        $o['page'] = 'payout';
        $o['user'] = $user;
        $o['baseurl'] = $this->baseurl;    
        $this->data['o'] = $o;
        $this->load->view('videoadmin/payout', $this->data);   

    }

    function paypaltransaction_details(){
        $transaction_id      = $this->input->post('transaction_id');
        $user   = (array) $this->user;
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
       // echo "<pre>";
       // print_r($paypal_obj);
       // echo "</pre>";
        $this->data['pp'] = $paypal_obj;
        $o['baseurl'] = $this->baseurl;    
        
        $this->data['o'] = $o;
        $this->load->view('videoadmin/div/transaction_details', $this->data);  

    }

    function chargeback(){
        $user   = (array) $this->user;
        $this->check_permission(); 

        $sql = "Select * from  (select pl.date_added,pl.id,pl.reason_code,pl.parent_txn_id,pl.payment_status, 
                    (select user_id from paypal where return_id = pl.parent_txn_id LIMIT 1) as user_request_id,
                    (select CONCAT(first_name,' ',last_name) as name from users where id = user_request_id LIMIT 1) as name
                    from 
                        paypal_logs as pl 
                    where 
                        pl.is_chargeback = '1' 
                    order by pl.id desc
) as temp_table GROUP BY parent_txn_id";
        $check_chargback = $this->db->query($sql);
        if($check_chargback->num_rows() == 0){
            $msg = "No Chargeback found.";
            $msg_type = 'warning';
        }else{

            $show_chargeback = array();
            foreach($check_chargback->result_array() as $ccb){
                $show_chargeback[] = $ccb;

            }
            $o['show_chargeback'] = $show_chargeback;  
            $o['show_chargeback_table'] = true;
        }

        $o['affiliate_admin_header'] = true;
        $o['msg_type'] = $msg_type;
        $o['msg'] = $msg;
        $o['title'] = "Chargeback";
        $o['page'] = 'chargeback';
        $o['user'] = $user;
        $o['baseurl'] = $this->baseurl;    
        $this->data['o'] = $o;
        $this->load->view('videoadmin/affadmin', $this->data); 
        
    }

    function chargebackdetails(){
        $user   = (array) $this->user;
        $this->check_permission(); 
        $parent_txn_id   = $this->uri->segment(3);

        $sql = "select pl.all_response,pl.payment_date,pl.date_added,pl.id,pl.reason_code,pl.parent_txn_id,pl.payment_status, 
                    (select user_id from paypal where return_id = pl.parent_txn_id LIMIT 1) as user_request_id,
                    (select CONCAT(first_name,' ',last_name) as name from users where id = user_request_id LIMIT 1) as name
                    from 
                        paypal_logs as pl 
                    where 
                        pl.is_chargeback = '1' 
                    and
                        pl.parent_txn_id = '$parent_txn_id'
                    order by pl.id asc";
        $check_chargback = $this->db->query($sql);
        if($check_chargback->num_rows() == 0){
            $msg = "Invalid ID, please try again.";
            $msg_type = 'warning';
        }else{

            $show_chargeback = array();
            foreach($check_chargback->result_array() as $ccb){
                $all_resp = array();
                $explode_resp = explode("&",$ccb['all_response']);
                foreach($explode_resp as $er){
                    $rep_str = str_replace("=", ": ", $er);
                    $all_resp[] = urldecode($rep_str);
                }
                $ccb['all_resp'] = $all_resp;
                $show_chargeback[] = $ccb;
            }
            $o['show_chargeback'] = $show_chargeback;  
            $o['show_chargeback_detail_table'] = true;
        }

        $o['affiliate_admin_header'] = true;
        $o['msg_type'] = $msg_type;
        $o['msg'] = $msg;
        $o['title'] = "Chargeback";
        $o['page'] = 'chargeback';
        $o['user'] = $user;
        $o['baseurl'] = $this->baseurl;    
        $this->data['o'] = $o;
        $this->load->view('videoadmin/affadmin', $this->data);         
    }
    function redirect_link($path,$msg,$msg_type){

        $this->session->set_flashdata('msg', $msg);
        $this->session->set_flashdata('msg_type', $msg_type);            
        redirect($this->baseurl . "$path", 'refresh');        
    }
    function count_user_affiliate($type){
        $user = $this->user;

        if($type == 0){
            $sql = "select count(id) as count_aff from users where aff_status = 'pending' and is_aff_tos = '1' LIMIT 1";
        }elseif($type == 1){
           $sql = "select count(id) as count_aff from users where aff_status = 'approved' and is_aff != '' and is_aff_tos = '1' LIMIT 1"; 
        }else{
            $sql = "select count(id) as count_aff from users where aff_status = 'rejected' LIMIT 1"; 
        }
        $count_dbaff = $this->db->query($sql);
        if($count_dbaff->num_rows() == 0){
            return 0;
        }else{
            $check_c = $count_dbaff->row_array();
            return $check_c['count_aff'];
        }

    }
    function check_permission(){
        if (!$this->ion_auth->in_group($this->group_name) or !$this->ion_auth->logged_in() )
        {
            return show_error('Permission denied, you are not allowed to view this page or try to login again');
        }  
    }

    function cleanup($word)
    {
        $word = trim($word);
        $word = strip_tags($word, " <STRONG> <EM> <U> <BR> <n> \n ");
        $word = addslashes($word);
        $word = str_replace(array("|","~>","'")," ",$word); 
        return $word;
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