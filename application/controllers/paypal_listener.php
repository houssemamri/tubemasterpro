<?php defined('BASEPATH') OR exit('No direct script access allowed');
error_reporting(0);
class Paypal_listener extends MX_Controller {

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

        $this->baseurl = $this->config->config['base_url']; 

        $this->smtp_host = "box342.bluehost.com";
        $this->smtp_user = "nathan@nathanhague.com";
        $this->smtp_pass = "$Wolfman1";  

    }

    function index(){
        // CONFIG: Enable debug mode. This means we'll log requests into 'ipn.log' in the same directory.
        // Especially useful if you encounter network errors or other intermittent problems with IPN (validation).
        // Set this to 0 once you go live or don't require logging.
        define("DEBUG", 0);
        // Set to 0 once you're ready to go live
        define("USE_SANDBOX", 0);
        define("LOG_FILE", "./ipn.log");
        // Read POST data
        // reading posted data directly from $_POST causes serialization
        // issues with array data in POST. Reading raw POST data from input stream instead.
        $raw_post_data = file_get_contents('php://input');

        $raw_post_array = explode('&', $raw_post_data);
        $myPost = array();
        foreach ($raw_post_array as $keyval) {
            $keyval = explode ('=', $keyval);
            if (count($keyval) == 2)
                $myPost[$keyval[0]] = urldecode($keyval[1]);
        }
        // read the post from PayPal system and add 'cmd'
        $req = 'cmd=_notify-validate';
        if(function_exists('get_magic_quotes_gpc')) {
            $get_magic_quotes_exists = true;
        }
        foreach ($myPost as $key => $value) {
            if($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1) {
                $value = urlencode(stripslashes($value));
            } else {
                $value = urlencode($value);
            }
            $req .= "&$key=$value";
        }
        // Post IPN data back to PayPal to validate the IPN data is genuine
        // Without this step anyone can fake IPN data
        if(USE_SANDBOX == true) {
            $paypal_url = "https://www.sandbox.paypal.com/cgi-bin/webscr";
        } else {
            $paypal_url = "https://www.paypal.com/cgi-bin/webscr";
        }
        $ch = curl_init($paypal_url);
        if ($ch == FALSE) {
            return FALSE;
        }
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
        if(DEBUG == true) {
            curl_setopt($ch, CURLOPT_HEADER, 1);
            curl_setopt($ch, CURLINFO_HEADER_OUT, 1);
        }
        // CONFIG: Optional proxy configuration
        //curl_setopt($ch, CURLOPT_PROXY, $proxy);
        //curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 1);
        // Set TCP timeout to 30 seconds
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));
        // CONFIG: Please download 'cacert.pem' from "http://curl.haxx.se/docs/caextract.html" and set the directory path
        // of the certificate as shown below. Ensure the file is readable by the webserver.
        // This is mandatory for some environments.
        //$cert = __DIR__ . "./cacert.pem";
        //curl_setopt($ch, CURLOPT_CAINFO, $cert);
        $res = curl_exec($ch);
        if (curl_errno($ch) != 0) // cURL error
            {
            if(DEBUG == true) { 
                error_log(date('[Y-m-d H:i e] '). "Can't connect to PayPal to validate IPN message: " . curl_error($ch) . PHP_EOL, 3, LOG_FILE);
            }
            curl_close($ch);
            exit;
        } else {
                // Log the entire HTTP response if debug is switched on.
                if(DEBUG == true) {
                    error_log(date('[Y-m-d H:i e] '). "HTTP request of validation request:". curl_getinfo($ch, CURLINFO_HEADER_OUT) ." for IPN payload: $req" . PHP_EOL, 3, LOG_FILE);
                    error_log(date('[Y-m-d H:i e] '). "HTTP response of validation request: $res" . PHP_EOL, 3, LOG_FILE);
                }
                curl_close($ch);
        }
        // Inspect IPN validation result and act accordingly
        // Split response headers and payload, a better way for strcmp
        $tokens = explode("\r\n\r\n", trim($res));
        $res = trim(end($tokens));

        if (strcmp ($res, "VERIFIED") == 0) {

            // check whether the payment_status is Completed
            // check that txn_id has not been previously processed
            // check that receiver_email is your PayPal email
            // check that payment_amount/payment_currency are correct
            // process payment and mark item as paid.
            // assign posted variables to local variables
            //$item_name = $_POST['item_name'];
            //$item_number = $_POST['item_number'];
            //$payment_status = $_POST['payment_status'];
            //$payment_amount = $_POST['mc_gross'];
            //$payment_currency = $_POST['mc_currency'];
            //$txn_id = $_POST['txn_id'];
            //$receiver_email = $_POST['receiver_email'];
            //$payer_email = $_POST['payer_email'];
            /*

cmd=_notify-validate&payment_cycle=Monthly&txn_type=recurring_payment_profile_created&last_name=Frain&next_payment_date=03%3A00%3A00+Mar+19%2C+2015+PDT&residence_country=GB&initial_payment_amount=0.00&currency_code=USD&time_created=03%3A01%3A28+Feb+19%2C+2015+PST&verify_sign=AoNMxRuHl1DxNQ0QSnnmyYwc6XS2AiRjo1C9TFi5u6PIL4ABC2yJr5vH&period_type=+Regular&payer_status=verified&tax=0.00&payer_email=ovcfinancial%40gmail.com&first_name=Andrew&receiver_email=nathan.hague%40australiawow.com.au&payer_id=T9NR9AYS7J8UY&product_type=1&payer_business_name=Online+Video+Consultants+Limited&shipping=0.00&amount_per_cycle=147.00&profile_status=Active&charset=windows-1252&notify_version=3.8&amount=147.00&outstanding_balance=0.00&recurring_payment_id=I-7BY43F5F14H6&product_name=One+calendar+month+subscription+to+TubeTargetPro+system.+Thanks+heaps%21&ipn_track_id=de62158260b02
cmd=_notify-validate&payment_cycle=Monthly&txn_type=recurring_payment_profile_created&last_name=brodrick&next_payment_date=02%3A00%3A00+Feb+21%2C+2015+PST&residence_country=NZ&initial_payment_amount=0.00&currency_code=USD&time_created=16%3A04%3A48+Feb+19%2C+2015+PST&verify_sign=ANPF9zgvkmiyCLDiObVpddNBXiE4AzLRRda1FwQ9VhnalSpNZZ0tbOO.&period_type=+Regular&payer_status=unverified&tax=0.00&payer_email=brodrick.ed%40gmail.com&first_name=edward&receiver_email=nathan.hague%40australiawow.com.au&payer_id=SQ368B4M8J4RU&product_type=1&shipping=0.00&amount_per_cycle=247.00&profile_status=Active&charset=windows-1252&notify_version=3.8&amount=247.00&outstanding_balance=0.00&recurring_payment_id=I-6ALB6SHPHSRW&product_name=One+calendar+month+subscription+to+TubeTargetPro+system.+Thanks+heaps%21&ipn_track_id=1415a04b2b6d4

cmd=_notify-validate&residence_country=US&invoice=abc1234&address_city=San+Jose&first_name=John&payer_id=TESTBUYERID01&shipping=3.04&mc_fee=0.44&txn_id=590827732&receiver_email=seller%40paypalsandbox.com&quantity=1&custom=xyz123&reason_code=chargeback&payment_date=05%3A09%3A52+11+Feb+2015+PST&address_country_code=US&address_zip=95131&tax=2.02&item_name=something&address_name=John+Smith&last_name=Smith&receipt_ID=3012-5109-3782-6103&receiver_id=seller%40paypalsandbox.com&item_number=AK-1234&verify_sign=ACUZAIHjz6aWDvPeI6hFIkW2zYpuATNezGaTghbw7zr.BRcUsvKz5zkF&address_country=United+States&payment_status=Reversed&address_status=confirmed&business=seller%40paypalsandbox.com&payer_email=buyer%40paypalsandbox.com&notify_version=2.1&txn_type=web_accept&test_ipn=1&payer_status=verified&mc_currency=USD&mc_gross=12.34&address_state=CA&mc_gross1=12.34&parent_txn_id=SOMEPRIORTXNID002&payment_type=instant&address_street=123%2C+any+street

 for IPN payload: cmd=_notify-validate&payment_cycle=Monthly&txn_type=recurring_payment_profile_created&last_name=Test&next_payment_date=02%3A00%3A00+Feb+12%2C+2015+PST&residence_country=US&initial_payment_amount=0.00&currency_code=USD&time_created=06%3A30%3A24+Feb+11%2C+2015+PST&verify_sign=A5KVngv77D9DG5iNBSkjtM40.lS-AN6ntdb0.SO6ue5zq8N2o.lXbTBS&period_type=+Regular&payer_status=verified&test_ipn=1&tax=0.00&payer_email=awtest%40gmail.com&first_name=AuWow&receiver_email=renefandida-facilitator%40gmail.com&payer_id=6Y8RDWNDB9WCS&product_type=1&shipping=0.00&amount_per_cycle=147.00&profile_status=Active&charset=windows-1252&notify_version=3.8&amount=147.00&outstanding_balance=0.00&recurring_payment_id=I-W1VH6GTJ55RY&product_name=One+calendar+month+subscription+to+TubeTargetPro+system.+Thanks+heaps%21&ipn_track_id=35cc3066dba4a
            */
            $txn_type = $_POST['txn_type'];
            $reason_code = $_POST['reason_code'];
            if($reason_code == "chargeback"){
                $is_chargeback = 1;
            }else{
                $is_chargeback = 0;
            }
            
            $this->db->trans_start();

                $data = array(

                   'txn_id'         => $_POST['txn_id'],
                   'payment_cycle'  => $_POST['payment_cycle'],
                   'payer_id'       => $_POST['payer_id'],
                   'payer_name'     => $_POST['first_name'] . " " . $_POST['last_name'],
                   'invoice_id'     => $_POST['invoice'],
                   'reason_code'    => $_POST['reason_code'],
                   'receipt_id'     => $_POST['receipt_ID'],
                   'receiver_id'    => $_POST['receiver_id'],
                   'payment_status' => $_POST['payment_status'],
                   'txn_type'       => $_POST['txn_type'],
                   'parent_txn_id'  => $_POST['parent_txn_id'],
                   'payment_date'   => $_POST['payment_date'],
                   'mc_currency'    => $_POST['mc_currency'],
                   'mc_fee'         => $_POST['mc_fee'],
                   'mc_gross'       => $_POST['mc_gross'],
                   'date_added'     => date('Y-m-d',time()),
                   'is_chargeback'  => $is_chargeback,
                   'all_response'   => $req,
                   'ipn_track_id'   => $_POST['ipn_track_id'],
                   'recurring_payment_id' => $_POST['recurring_payment_id']
                );
                $this->db->insert('paypal_logs', $data); 
                $latest_insert_id = $this->db->insert_id();
                /* if chargeback and completed*/

                if($is_chargeback == 1){
                    /* check for reversal of payment for affiliate member */
                    $sql = "select pp.user_id, (select user_id from affiliates where user_id_aff = pp.user_id LIMIT 1) as my_master_id
                            from 
                                paypal as pp 
                            where 
                                pp.return_id = '" . $_POST['parent_txn_id'] ."' LIMIT 1";
                    $check_is_aff = $this->db->query($sql);
                    if($check_is_aff->num_rows() > 0){
                        $aff_d = $check_is_aff->row_array();
                        /* check if there is a comission paid for user's master */
                        $sql = "select pid,amt from aff_payout 
                                where 
                                    sender_id = '{$aff_d['user_id']}'
                                and
                                    receiver_id = '{$aff_d['my_master_id']}' 
                                and
                                    payout_status = 'SUCCESS' 
                                LIMIT 1";

                        $check_payout = $this->db->query($sql);
                        if($check_payout->num_rows() == 0){
                            $commision_paid = 0;
                            $payout_id = 0;
                            $amt = 0;
                        }else{
                            $show_om = $check_payout->row_array();
                            $commision_paid = 1;
                            $payout_id = $show_om['pid'];
                            $amt = $show_om['amt'];
                        }


                        /* INSERT TO CHARGEBACk*/
                        if($_POST['payment_status'] == 'Reversed'){
                         $data2 = array(
                           'aff_user_id'            => $aff_d['user_id'],
                           'master_aff_user_id'     => $aff_d['my_master_id'],
                           'parent_txn_id'          => $_POST['parent_txn_id'],
                           'pp_log_id'              => $latest_insert_id,
                           'is_returned'            => 0,
                           'returned_amt'           => $amt,
                           'date_added'             => time(),
                           'commission_paid'        => $commision_paid,
                           'payout_id'              => $payout_id
                        );
                         $this->db->insert('aff_chargeback', $data2);
                       }
                    }
                }else{
                    if($_POST['txn_type'] == "recurring_payment"){
                        //$_POST['recurring_payment_id'] recurring payment = return_id from paypal table
                        $sql = "select id,p_status from paypal 
                                    where 
                                return_id = '". $_POST['recurring_payment_id'] ."'
                                and
                                p_status = '1' and p_status = 'PENDING' LIMIT 1";
                        $check_rp = $this->db->query($sql);
                        if($check_rp->num_rows() > 0){
                            $get_crp = $check_rp->row_array();
                            $data = array(
                               'p_status' => 'ACTIVE',
                        );
                            $this->db->where('id', $get_crp['id']);
                            $this->db->update('paypal', $data);
                        }

                    }
                }
            $this->db->trans_complete();
            if(DEBUG == true) {
                error_log(date('[Y-m-d H:i e] '). "Verified IPN: $req Trasaction type -  " . print_r($data2). PHP_EOL, 3, LOG_FILE);
            }
        } else if (strcmp ($res, "INVALID") == 0) {
            // log for manual investigation
            // Add business logic here which deals with invalid IPN messages
            if(DEBUG == true) {
                error_log(date('[Y-m-d H:i e] '). "Invalid IPN: $req" . PHP_EOL, 3, LOG_FILE);
            }
        }
    }
}
?>