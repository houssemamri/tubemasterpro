<?php defined('BASEPATH') OR exit('No direct script access allowed');
error_reporting(0);
class Cronjobs extends MX_Controller {


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

        $this->smtp_host = "box342.bluehost.com";
        $this->smtp_user = "nathan@nathanhague.com";
        $this->smtp_pass = "$Wolfman1";  
        $this->api_key = md5("TuBeTaRgEtPro2015!?"); //f55de9fe13aa4cda7ec8f74bfd647466

        $this->comission_amt = "67.00";

    }

    function index(){
		return show_error('Error. Please try again'); 
    }

    function affiliate_payout(){
        require 'paypal/sample/bootstrap.php';


        $api_key = $this->uri->segment(3);
        
        if($api_key == $this->api_key){ 
            $aff_date_check = date('Y-m-d',time());
            //$aff_date_check = "2015-02-16";
            $this->db->trans_start();

            /* check all affiliate users */
            /*
            $sql = "select p.id as paypal_id,u.id,u.paypal_email,p.return_id
            from 
                users as u,
                paypal as p
            where
                u.aff_status = 'approved'
            and
                p.p_status = 'ACTIVE'
            and
                u.id = p.user_id";
				*/
			$sql = "select p.id as paypal_id,u.id,u.paypal_email from users as u, paypal as p 
where u.aff_status = 'approved' and u.id = p.user_id";	
            $check_users = $this->db->query($sql);
            if($check_users->num_rows() > 0){
                $total_transaction_found = 0;
                foreach($check_users->result_array() as $cu){
                    $show_res = array();
                    $total_amt = 0;        
                    $cu['receiver_id'] = $cu['id'];
                    $cu['receiver_paypal_email'] = $cu['paypal_email'];

                    /* check master if he/she will be paid for today*/
                    $sql = "select p.id,p.user_id from 
                                paypal as p,
                                affiliates as aff
                            where 
                                p.aff_date_check = '$aff_date_check' 
                            and 
                                p.p_status = 'ACTIVE' 
                            and 
                                p.user_id != '{$cu['id']}'
                            and
                                aff.aff_status = '1'
                            and
                                aff.user_id = '{$cu['id']}'
                            and
                                aff.user_id_aff = p.user_id";

                    $check_aff = $this->db->query($sql);
                    if($check_aff->num_rows() > 0){
                        $total_transaction_found = $check_aff->num_rows();
                        $chargeback_count = 0;
                        foreach($check_aff->result_array() as $show_rec){

                            //check payout if already ceated
                            $sql = "select pid,aff_sent,sender_id from aff_payout 
                                    where 
                                        sender_trans_id = '{$cu['paypal_id']}' 
                                    and
                                        sender_id = '{$show_rec['user_id']}'
                                    and
                                        receiver_id = '{$cu['receiver_id']}'
                                    and
                                        date_transaction = '$aff_date_check'
                                    LIMIT 1";

                            $check_trans = $this->db->query($sql);
                            if($check_trans->num_rows() == 0)
                            {
     
                                $data = array(
                                'sender_trans_id'   => $cu['paypal_id'],
                                'sender_id'         => $show_rec['user_id'],
                                'receiver_id'       => $cu['receiver_id'],
                                'date_transaction'  => $aff_date_check,
                                'amt'               => $this->comission_amt
                                );

                                $this->db->insert('aff_payout', $data);
                                $cu['aff_payout_id'] = $this->db->insert_id();

                                $cu['sender_id'] = $show_rec['user_id'];
                                $show_res[] = $cu;
                            }else{
                                $get_aff = $check_trans->row_array();

                                if($get_aff['aff_sent'] == 0){
                                    $cu['sender_id']        = $get_aff['sender_id'];
                                    $cu['aff_payout_id']    = $get_aff['pid'];
                                    $show_res[] = $cu; 
                                }

                            }
                            $total_amt = $total_amt + $this->comission_amt;
                        }

                       
                        /* SEND TO PAYPAL */

                        /* Check if there is a charge back for this user */
                        $sql = "select aff_user_id,master_aff_user_id,cb_id
                                from 
                                    aff_chargeback 
                                where 
                                    master_aff_user_id = '{$cu['id']}'
                                and
                                    is_returned = '0'";

                        $check_charge = $this->db->query($sql);
                        if($check_charge->num_rows() == 0){
                            $chargeback_count = 0;
                        }else{
                            $chargeback_count = $check_charge->num_rows();
                            $has_chargeback = 1;
                        }

                      
                        //check if transaction total transaction is greater than chargeback
                        if($chargeback_count <=  $total_transaction_found){
                            //check how many can be transffered and how many charge back
                            $total_transactions =  $total_transaction_found - $chargeback_count;
                            //echo "<br><br>out of $total_transaction_found, only $total_transactions will be credited and $chargeback_count chargeback <br><br>";
                            $pp_chback_count = 1;


                            $senderItem = "";
                            $senderBatchHeader = "";
                            $output = $obj = $confirm_obj = "";
                            $payouts = "";
                            foreach($show_res as $pa){

                                if($pp_chback_count <= $total_transactions)
                                {
                                    //START PAYPAL TRANSACTION AND UPDATE
                                    $payouts = new \PayPal\Api\Payout();
                                    $senderBatchHeader = new \PayPal\Api\PayoutSenderBatchHeader();
                                    // #### Batch Header Instance
                                    $senderBatchHeader->setSenderBatchId(uniqid())
                                    ->setEmailSubject("TubeTargetPro Payout!");
                                    // #### Sender Item
                                    // Please note that if you are using single payout with sync mode, you can only pass one Item in the request
                                    $senderItem = new \PayPal\Api\PayoutItem();
                                    $senderItem->setRecipientType('Email')
                                        ->setNote('TubeTargetPro Affiliate Sale')
                                        ->setReceiver(''. $pa['receiver_paypal_email'] .'')
                                        ->setAmount(new \PayPal\Api\Currency('{
                                                            "value":"' . $this->comission_amt . '",
                                                            "currency":"USD"
                                                        }'));

                                    $payouts->setSenderBatchHeader($senderBatchHeader)
                                        ->addItem($senderItem);


                                    // For Sample Purposes Only.
                                    $request = clone $payouts;

                                    // ### Create Payout
                                    try {
                                        $output = $payouts->createSynchronous($apiContext);
                                        $obj = $output->toJSON();
                                        $confirm_obj = (array) json_decode($obj);

                                        // update transaction 
                                        $data = array(
                                           'payout_id' => $confirm_obj['batch_header']->payout_batch_id,
                                           'payout_status' => $confirm_obj['batch_header']->batch_status,
                                           'aff_sent' => 1
                                        );     

                                        $this->db->where('pid', $pa['aff_payout_id']);
                                        $this->db->update('aff_payout', $data);
                                    } catch (Exception $ex) {


                                        ResultPrinter::printError("Created Single Synchronous Payout", "Payout", null, $request, $ex);
                                        exit(1);
                                    }

                                    //END PAYPAL TRANSACTION AND UPDATE
                                }else{
                                    //insert transaction Dummy
                                     $data = array(
                                           'payout_id' => strtoupper("D-" . uniqid()),
                                           'payout_status' => "SUCCESS",
                                           'aff_sent' => 1
                                        );     
                                    $this->db->where('pid', $pa['aff_payout_id']);
                                    $this->db->update('aff_payout', $data);

                                    // create dummy paypal 

                                     // Check if there is a charge back for this user *
                                    $sql = "select aff_user_id,master_aff_user_id,cb_id,payout_id
                                            from 
                                                aff_chargeback 
                                            where 
                                                master_aff_user_id = '{$cu['id']}'
                                            and
                                                is_returned = '0' LIMIT 1";
                                    $check_charge = $this->db->query($sql);
                                    if($check_charge->num_rows() > 0){
                                        $cch = $check_charge->row_array();

                                        $data = array(
                                           'is_returned' => 1,
                                           'date_returned' => $aff_date_check
                                        );     
                                        $this->db->where('cb_id', $cch['cb_id']);
                                        $this->db->update('aff_chargeback', $data);

                                        // ADD REVERSE TO TRANSACTION
                                        $data = array(
                                        'sender_trans_id'   => $cch['payout_id'],
                                        'sender_id'         => $cch['aff_user_id'],
                                        'receiver_id'       => $cch['master_aff_user_id'],
                                        'date_transaction'  => $aff_date_check,
                                        'amt'               => "-$this->comission_amt",
                                        'payout_id'         => strtoupper("D-" . uniqid()),
                                        'payout_status'     => "CHARGEDBACK",
                                        'aff_sent' => 1
                                        );
                                        $this->db->insert('aff_payout', $data);
                                    }
                                    
                                }
                               
                                $pp_chback_count++;
                            }

                        }
                      // echo "<hr>";
                      //  echo "<pre>";
                      //  echo $sql;
                      //  print_r($show_res);

                    }
                }
            }


            $this->db->trans_complete();
        }else{
         return show_error('Error. Please try again'); 
        } 

    }
    function affiliate_payout_old(){

        require 'paypal/sample/bootstrap.php';
        $payouts = new \PayPal\Api\Payout();

        $api_key = $this->uri->segment(3);
        
        if($api_key == $this->api_key){ 
             //$aff_date_check = date('Y-m-d',time());
            $aff_date_check = "2015-02-16";
            $this->db->trans_start();
            
            //check user schedule that will pay 67USD to affiliate member    
            $sql = "select id,user_id from paypal where aff_date_check = '$aff_date_check'";
            $check_pp = $this->db->query($sql);
            if($check_pp->num_rows() > 0){
               $pp_aff = array();
               foreach($check_pp->result_array() as $cp){
                    //check who affiliated them
                    $sql = "select aff.user_id as receiver_id, u.paypal_email 
                            from 
                                affiliates as aff,
                                paypal as pp,
                                users as u 
                            where 
                                aff.user_id_aff = '{$cp['user_id']}' 
                            and
                                pp.p_status = 'ACTIVE'
                            and
                                pp.ppstatus = '1'
                            and
                                u.paypal_email != ''
                            and
                                u.id = aff.user_id
                            LIMIT 1";
                    $check_user = $this->db->query($sql);
                    if($check_user->num_rows() > 0){
                        $show_rec = $check_user->row_array();
                        $cp['receiver_id'] = $show_rec['receiver_id'];
                        $cp['receiver_paypal_email'] = $show_rec['paypal_email'];
                        //check payout if already ceated
                        $sql = "select pid,aff_sent from aff_payout 
                                where 
                                    sender_trans_id = '{$cp['id']}' 
                                and
                                    sender_id = '{$cp['user_id']}'
                                and
                                    receiver_id = '{$show_rec['receiver_id']}'
                                and
                                    date_transaction = '$aff_date_check'
                                LIMIT 1";

                        $check_trans = $this->db->query($sql);
                        if($check_trans->num_rows() == 0)
                        {
                            
                            $data = array(
                            'sender_trans_id'   => $cp['id'],
                            'sender_id'         => $cp['user_id'],
                            'receiver_id'       => $show_rec['receiver_id'],
                            'date_transaction'  => $aff_date_check,
                            'amt'               => $this->comission_amt
                            );

                            $this->db->insert('aff_payout', $data);
                            $cp['aff_payout_id'] = $this->db->insert_id();
                            $pp_aff[] = $cp;
                        }else{
                            $get_aff = $check_trans->row_array();

                            if($get_aff['aff_sent'] == 0){
                                $cp['aff_payout_id'] = $get_aff['pid'];
                                $pp_aff[] = $cp; 
                            }

                        }

                        
                    }
                   
               }

               /* SEND AFFILIATE TO USERS*/

                foreach($pp_aff as $pa){
                    $senderBatchHeader = new \PayPal\Api\PayoutSenderBatchHeader();
                    // #### Batch Header Instance
                    $senderBatchHeader->setSenderBatchId(uniqid())
                    ->setEmailSubject("TubeTargetPro Payout!");
                    // #### Sender Item
                    // Please note that if you are using single payout with sync mode, you can only pass one Item in the request
                    $senderItem = new \PayPal\Api\PayoutItem();
                    $senderItem->setRecipientType('Email')
                        ->setNote('TubeTargetPro Affiliate Sale')
                        ->setReceiver(''. $pa['receiver_paypal_email'] .'')
                        ->setAmount(new \PayPal\Api\Currency('{
                                            "value":"67",
                                            "currency":"USD"
                                        }'));

                    $payouts->setSenderBatchHeader($senderBatchHeader)
                        ->addItem($senderItem);


                    // For Sample Purposes Only.
                    $request = clone $payouts;

                    // ### Create Payout
                    try {
                        $output = $payouts->createSynchronous($apiContext);
                        $obj = $output->toJSON();
                        $confirm_obj = (array) json_decode($obj);

                        /* update transaction */
                        $data = array(
                           'payout_id' => $confirm_obj['batch_header']->payout_batch_id,
                           'payout_status' => $confirm_obj['batch_header']->batch_status,
                           'aff_sent' => 1
                        );     
                        $this->db->where('pid', $pa['aff_payout_id']);
                        $this->db->update('aff_payout', $data);
                    } catch (Exception $ex) {
                        ResultPrinter::printError("Created Single Synchronous Payout", "Payout", null, $request, $ex);
                        exit(1);
                    }
               }
            }
            $this->db->trans_complete();
        }else{
         return show_error('Error. Please try again'); 
        } 
    }
/*
    function verify_paypal_payment(){

       $api_key = $this->uri->segment(3);
        if($api_key == $this->api_key){
            $next_billing_date = date('Y-m-d',time());

             $this->db->trans_start();
             $sql = "select plan_id,user_id,p_status from paypal where p_status = 'ACTIVE' and next_billing_date = '$next_billing_date_check'";
             echo $sql;
             $this->db->trans_complete();
        }else{  
             return show_error('Error. Please try again');
        }
    }
*/
}