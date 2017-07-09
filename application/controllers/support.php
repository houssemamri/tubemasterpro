<?php defined('BASEPATH') OR exit('No direct script access allowed');
error_reporting(0);
class Support extends MX_Controller {

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

        
    }

function index()
    {
        $this->load->helper('captcha');
       

        $p          = strtolower($this->input->post('p'));
        
         /* check if user is logged in */
        $user = $this->ion_auth->user()->row();

        if($user->id != ""){
           $user_is_logged_in = 1;
           $o['user_is_logged_in'] = $user_is_logged_in;
           $o['name'] = $user->first_name . " " . $user->last_name;
           $o['email'] = $user->email;
        }else{
            $user_is_logged_in = 0;
            $o['user_is_logged_in'] = $user_is_logged_in;
        }
       
        /*
        if($user_is_logged_in == 0){
            $create_random_words = random_string('alnum', 8);
            $vals = array(
            'word'   => $create_random_words,
            'img_path'   => 'assets/images/captcha/',
            'img_url'    => $this->baseurl . '/assets/images/captcha/',
            'font_path'  => '/assets/fonts/icomoon.ttf',
            'img_width'  => 200,
            'img_height' => 80,
            'expiration' => 7200
            );

            $cap = create_captcha($vals);

            $data = array(
            'captcha_time'  => $cap['time'],
            'ip_address'    => $this->input->ip_address(),
            'word'   => $cap['word']
            );
        
            $query = $this->db->insert_string('captcha', $data);
            $this->db->query($query);

            $cap = create_captcha($vals);
            $o['captcha_image'] = $cap['image'];
            $o['show_captcha_image'] = true;
        }
        */
        
        $p['msg'] = $msg;
        $o['css']               = "default.css";
        $o['contact_table']     = true;
        $o['baseurl'] = $this->baseurl;    
        $this->data['o'] = $o;
       // $this->_render_page('support', $this->data);
        $this->load->view('support', $this->data);

    }
    

    function send()
    {
            $this->load->library('email');
            $name       = $this->cleanup($this->input->post('name'));
            $email      = $this->cleanup($this->input->post('email'));  
            $web        = $this->cleanup($this->input->post('web'));
            $message    = $this->cleanup($this->input->post('comments'));
            $cc         = $this->cleanup($this->input->post('cc'));
            $subject    = $this->cleanup($this->input->post('subject'));
            $captcha    = $this->cleanup($this->input->post('captcha'));
            
             /* check if user is logged in */
            $user = $this->ion_auth->user()->row();
            
            if($user->id != ""){
                $user_is_logged_in = 1;
                $email  = $user->email;
                $name   = $user->firstname;

            }else{
                $user_is_logged_in = 0;
                $o['user_is_logged_in'] = $user_is_logged_in;
            }


            // First, delete old captchas
            /*
            $expiration = time()-7200; // Two hour limit
            $this->db->query("DELETE FROM captcha WHERE captcha_time < ".$expiration);  
            // Then see if a captcha exists:
            $sql = "SELECT COUNT(*) AS count FROM captcha WHERE word = ? AND ip_address = ? AND captcha_time > ?";
            $binds = array($captcha, $this->input->ip_address(), $expiration);
            $query = $this->db->query($sql, $binds);
            $row = $query->row();
            if ($row->count == 0)
            {
                $captcha_check = "not_found";
            }
                
            */
                //form validation
                $errors = array();
                $fields = array();
                if(!$name) {
                    $errors[] = "Please enter your name.";
                    $fields[] = "name";
                }
                /*
                if($user_is_logged_in == 0){

                    if($captcha_check == "not_found") {
                     $errors[] = "You must submit the word that appears in the image";
                     $fields[] = "security";
                    }
                }
                */
                $email_pattern = "/^[a-zA-Z0-9][a-zA-Z0-9\.-_]+\@([a-zA-Z0-9_-]+\.)+[a-zA-Z]+$/";
                if(!$email) {
                    $errors[] = "Please enter your e-mail address.";
                    $fields[] = "email";
                } else if(!preg_match($email_pattern, $email)) {
                    $errors[] = "The e-mail address you provided is invalid.";
                    $fields[] = "email";    
                }
                /*
                if(!$subject) {
                    $errors[] = "Please choose the subject of your message.";
                    $fields[] = "subject";
                }
                */
                if(!$message) {
                    $errors[] = "Please enter your message.";
                    $fields[] = "message";
                }


                if (!isset($ip_address))
                {
                    if (isset($_SERVER['REMOTE_ADDR'])) 
                    $ip_address=$_SERVER['REMOTE_ADDR'];
                }

                //preparing mail
                if(!$errors) {

                    $orig_file_name     = $_FILES['afile']['name'];
                    $config['upload_path'] = './assets/attachments';
                    $config['allowed_types'] = 'jpeg|jpg|doc|docx|xls|docx|png|pdf|pot|ppt|pptx|dot|dotx|ods';
                    $config['max_size'] = '10240'; // 50MB
                    $config['encrypt_name'] = TRUE;
                    $this->load->library('upload', $config);

                    if ($this->upload->do_upload('afile'))
                    {
                        $has_attachment = 1;
                    }
                    if ($this->upload->do_upload('afile2'))
                    {
                        $has_attachment_2 = 1;
                    }
                    if ($this->upload->do_upload('afile3'))
                    {
                        $has_attachment_3 = 1;
                    }                                        
                    
                    //taking info about date, IP and user agent
                    $timestamp = date("Y-m-d H:i:s");
                    $ip   = $this->input->ip_address();
                    $host = gethostbyaddr($ip); 
                    $user_agent = $_SERVER["HTTP_USER_AGENT"];

                    $headers = "MIME-Version: 1.0\n";
                    $headers .= "Content-type: text/html; charset=utf-8\n";
                    $headers .= "Content-Transfer-Encoding: quoted-printable\n";
                    $headers .= "From: $email\n";

                    $content = 
                    '<p>'.$message.'</p> <br>'.
                    '<strong>Send by:</strong> '.$name.'<br>'.
                    '<strong>E-mail:</strong> '.$email.'<br><hr>'.
                    '<strong>Time:</strong> '.$timestamp.'<br>'.
                    '<strong>IP:</strong> '.$host.'<br>'.
                    '<strong>User agent:</strong> '.$user_agent;                   

                    /*;
                    if($has_attachment == 1){

                        $message = $message . "<br>" . "Attachments: <br> name: {$data2['upload_data']['client_name']} <br> Full path: {$data2['upload_data']['full_path']}";
                    }
                    */
                    $this->load->library('email');

					$config['protocol'] = 'smtp';
                    $config['smtp_host'] = 'localhost';
                    $config['smtp_port'] = '25';
                    $config['charset'] = 'iso-8859-1';
                    $config['wordwrap'] = TRUE;
					//$config['mailtype'] = 'html';
                     
                   
					// $config['protocol']  = 'smtp';
					// $config['smtp_host'] = 'box342.bluehost.com';
					// $config['smtp_port'] = '26';
					// $config['smtp_user'] = 'nathan@nathanhague.com';
					// $config['smtp_pass'] = '$Wolfman1';
					// $config['mailtype']  = 'html';
					// $config['charset']   = 'iso-8859-1';
					// $config['wordwrap']  = TRUE;

                    $this->email->initialize($config);

                        $subject = "Support request from $name";

                        $this->email->from("$email", "$name");
                        $this->email->to('support@tubemasterpro.com');
                        //$this->email->to('edwin_n_b@yahoo.com');
                       	$this->email->subject("$subject");
                        $this->email->message("$content");  

                    if($has_attachment == 1){
                        $data1 = array('upload_data' => $this->upload->data('afile'));
                        $attachments_path1      = $data1['upload_data']['full_path'];
                        $this->email->attach($attachments_path1);
                    }
                    if($has_attachment_2 == 1){
                        $data2 = array('upload_data2' => $this->upload->data('afile2'));
                        $attachments_path2     = $data2['upload_data2']['full_path'];
                        $this->email->attach($attachments_path2);
                    }
                    if($has_attachment_3 == 1){
                        $data3 = array('upload_data3' => $this->upload->data('afile3'));
                        $attachments_path3     = $data3['upload_data3']['full_path'];
                        $this->email->attach($attachments_path3);
                    }  

                    if ($this->email->send())
                    {
                        $date_added = date("Y-m-d H:i:s",time());
                        $data = array('name' => $name,'email' => $email,'subject' => $subject,'content' => $message, 'date_added' => $date_added);
                        $this->db->simple_query("SET NAMES 'utf-8'");  
                        $this->db->set($data);
                        $this->db->insert('contact'); 

                        $response['msgStatus'] = "ok";
                        $response['message'] = "We'll smash through your question within a few hours usually!";
          
                    }else {
                        $response['msgStatus'] = "error";
                        $response['message'] = "An error occured while trying to send your message. Please try again later.";
                    }
                } else {
                    $response['msgStatus'] = "error";
                    $response['errors'] = $errors;
                    $response['errorFields'] = $fields;
                }

                header('Content-type: application/json');
                echo json_encode($response);


    }

	function test_email(){
                /* SEND EMAIL TO USER */
                    $this->load->library('email');
                    $config['protocol'] = 'smtp';
                    $config['smtp_host'] = 'localhost';
                    // $config['smtp_user'] = $this->smtp_user;
                    // $config['smtp_pass'] = $this->smtp_pass;
                    // $config['smtp_port'] = '26';

                    $config['smtp_port'] = '25';
                    $config['charset'] = 'iso-8859-1';
                    $config['wordwrap'] = TRUE;
                    $config['mailtype'] = 'html';
 $upload_status = 'rejected';
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
                    $this->email->reply_to('australiawow@gmail.com', 'Your Name');
                    $this->email->to("renefandida@gmail.com"); 
                    //$this->email->to('info@topappstoday.com'); 
                    $this->email->subject("$subject");
                    $this->email->message("$content");  
                    if($this->email->send()){
	                    echo "success";
                    }else{
	                    echo "failed";
                    }
	}
    function getIp()
    {if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])){
        $ip_address=$_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    

    return $ip_address;
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