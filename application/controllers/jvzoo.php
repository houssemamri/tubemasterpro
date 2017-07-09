<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Jvzoo extends MY_Controller {

    public $data;

    function __construct()
    {
        parent::__construct();
        $this->load->library('authentication', NULL, 'ion_auth');
        $this->load->library('form_validation');
        $this->load->helper(array('url','cookie'));
         $this->load->library('template');
         $this->load->helper('string');
        $this->load->database();

        $this->form_validation->set_error_delimiters($this->config->item('error_start_delimiter', 'ion_auth'), $this->config->item('error_end_delimiter', 'ion_auth'));

        /*
ccustname=JohnSmith&ccuststate=&ccustcc=&ccusst@niteoweb.com&cproditem=1&cprodtitle=TestProduct&cprodtype=STANDARD&ctransaction=SALE&ctransaffiliate=affiliate@niteoweb.com&ctransamount=1000&ctranspaymentmethod=&ctransvendor=&ctransreceipt=1&cupsellreceipt=&caffitid=&cvendthru=&cverify=1EC4B66A&ctranstime=1350388651" http://www.myvideoads.dev/jvzoo/register_user/
        */

    }

    function jvzipnVerification() {
        $secretKey = "AvCXAScFia6vUpWDQnL5MAGeV";
        $pop = "";
        $ipnFields = array();
        foreach ($_POST AS $key => $value) {
            if ($key == "cverify") {
                continue;
            }
            $ipnFields[] = $key;
        }
        sort($ipnFields);
        foreach ($ipnFields as $field) {
            // if Magic Quotes are enabled $_POST[$field] will need to be
            // un-escaped before being appended to $pop
            $pop = $pop . $_POST[$field] . "|";
        }
        $pop = $pop . $secretKey;
        if ('UTF-8' != mb_detect_encoding($pop))
        {
            $pop = mb_convert_encoding($pop, "UTF-8");
        }
        $calcedVerify = sha1($pop);
        $calcedVerify = strtoupper(substr($calcedVerify,0,8));
        return $calcedVerify == $_POST["cverify"];
    }

    function register_user(){
    
        if(!isset($_POST['ctransaction']))
        die('unathorized access.');
        
        $verification = $this->jvzipnVerification();
        if($verification == 1){
            

            if($_POST['ctransaction'] == 'SALE'){


                $this->db->trans_start();
                //check register user
                $sql = "select email from users where email = '{$_POST['ccustemail']}' LIMIT 1";
                $check_ver = $this->db->query($sql);
                if($check_ver->num_rows() == 0){
                    $additional_data = array(
                        'first_name'    => $_POST['ccustname'],
                        'last_name'     => "",
                        'is_jvzoo'      => 1
                    );
                    $group = array('2');
                    $username   = $_POST['ccustname'];
                    $password   = "12345678";//random_string('alnum', 10);
                    $email      = $_POST['ccustemail'];
                    if($this->ion_auth->register($username, $password, $email, $additional_data, $group)){
                        $sql = "select email,id from users where email = '{$_POST['ccustemail']}' LIMIT 1";
                        $check_em = $this->db->query($sql);
                        if($check_em->num_rows() > 0){
                            //insert jvzoo details
                            $new_u = $check_em->row_array();
                            $data = array(
                               'user_id'                => $new_u['id'],
                               'product_item'           => $_POST['cproditem'],
                               'product_title'          => $_POST['cprodtitle'],
                               'prod_type'              => $_POST['cprodtype'],
                               'transaction'            => $_POST['ctransaction'],
                               'transaffiliate'         => $_POST['ctransaffiliate'],
                               'ctransamount'           => $_POST['ctransamount'],
                               'transpayment_method'    => $_POST['ctranspaymentmethod'],
                               'vendor_trans'           => $_POST['ctransvendor'],
                               'trans_receipt'          => $_POST['ctransreceipt'],
                               'sell_receipt'           => $_POST['cupsellreceipt'],
                               'aff_track_id'           => $_POST['caffitid'],
                               'cvendthru'              => $_POST['cvendthru'],
                               'cverify'                => $_POST['cverify'],
                               'ctranstime'             => $_POST['ctranstime'],
                               'date_added'             => time()
                            );
                            $this->db->insert('jvzoo', $data);

                            $this->load->library('email');
                            $config['protocol'] = 'smtp';
                            $config['smtp_host'] = 'box342.bluehost.com';
                            $config['smtp_user'] = 'nathan@nathanhague.com';
                            $config['smtp_pass'] = '$Wolfman1';
                            $config['smtp_port'] = '26';
                            $config['charset'] = 'iso-8859-1';
                            $config['wordwrap'] = TRUE;
                             $config['mailtype'] = 'html';

                            $this->email->initialize($config);

                           // $this->email->from('support@tubemasterpro.com', 'TubeMasterPro');
                           $this->email->from('support@nathanhague.com', 'TubeMasterPro');
                            //$this->email->from("$email", "$name");
                            $this->email->to($_POST['ccustemail']); 
                            $this->email->subject("Welcome to TubeMasterPro");
                            $this->email->message("Welcome to TubeMasterPro. <br> 
                                Please login to our url: http://www.tubemasterpro.com <br>
                                Email: {$_POST['ccustemail']} <br> 
                                Password: $password <br> <br>
                                Thanks, <br>
                                TubeMasterPro Team");  
                        }
                        echo "success";
                    }
                }else{

                }
                    $this->db->trans_complete();
            }else{
                    //if not sale
                    $sql = "select id,email from users where email = '{$_POST['ccustemail']}' LIMIT 1";

                    $check_ver = $this->db->query($sql);
                    if($check_ver->num_rows() > 0){
                        $u_id = $check_ver->row_array();
                        $sql = "select jvzoo_id,user_id,transaction from jvzoo where user_id = '{$u_id['id']}' LIMIT 1";
                        $check_jz = $this->db->query($sql);

                        
                        if($check_jz->num_rows() > 0){
                        $jz = $check_jz->row_array();
                           
                            $data = array(
                                   'transaction' => $_POST['ctransaction']
                            );
                           $this->db->where('jvzoo_id', $jz['jvzoo_id']);
                           $this->db->update('jvzoo', $data);  
                        }
                       

                    }
                  
            }

        }
    }
}