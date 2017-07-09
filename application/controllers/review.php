<?php defined('BASEPATH') OR exit('No direct script access allowed');
error_reporting(0);
class Review extends MX_Controller {

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

    function index(){
        return show_error('Error occured, please try again');
    }

    function affiliate(){
        $user = $this->ion_auth->user()->row();
        
        $this->data['title'] = "Affiliate - Sign Up";
        $this->data['user'] = $user;

        if (!$this->ion_auth->logged_in() )
        {
            return show_error('Permission denied, Please login.');
        }  

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
            'data-toggle'   => 'popover',
            'data-placement'=> 'top',
            'data-trigger'  => 'focus',
            'data-content'  => 'http:// or https://www.yourdomainname.com',
            'value' => $this->form_validation->set_value('website'),
        );
        $this->data['twitter'] = array(
            'name'  => 'twitter',
            'id'    => 'twitter',
            'type'  => 'text',
            'class' => 'validify_required validify_twitter',
            'data-toggle'   => 'popover',
            'data-placement'=> 'top',
            'data-trigger'  => 'focus',
            'data-content'  => 'http:// or https://twitter.com/youridhere',
            'value' => $this->form_validation->set_value('twitter'),
        );
        $this->data['fb'] = array(
            'name'  => 'fb',
            'id'    => 'fb',
            'type'  => 'text',
            'class' => 'validify_required validify_facebook',
            'data-toggle'   => 'popover',
            'data-placement'=> 'top',
            'data-trigger'  => 'focus',
            'data-content'  => 'http:// or https://www.facebook.com/youridhere',
            'value' => $this->form_validation->set_value('fb'),
        );
        $this->data['ln'] = array(
            'name'  => 'ln',
            'id'    => 'ln',
            'type'  => 'text',
            'class' => 'validify_required',
            'data-toggle'   => 'popover',
            'data-placement'=> 'top',
            'data-trigger'  => 'focus',
            'data-content'  => 'Your LinkedIn Profile URL',
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
       // $this->_render_page('support', $this->data);
        $this->load->view('review/affiliate', $this->data);
    }

    function main(){
        $user = $this->ion_auth->user()->row();

        if (!$this->ion_auth->logged_in() )
        {
            return show_error('Permission denied, Please login.');
        }  

        $o['username'] = ucwords($user->first_name);
        $o['contact_table']     = true;
        $o['baseurl'] = $this->baseurl;    
        $this->data['o'] = $o;
       // $this->_render_page('support', $this->data);
        $this->load->view('review/main', $this->data);
    }

    function upload_video(){
        $user = $this->ion_auth->user()->row();

        if (!$this->ion_auth->logged_in() )
        {
            return show_error('Permission denied, Please login.');
        }  

        $o['username'] = ucwords($user->username);
        $o['contact_table']     = true;
        $o['baseurl'] = $this->baseurl;    
        $this->data['o'] = $o;
       // $this->_render_page('support', $this->data);
        $this->load->view('review/upload_video', $this->data);  
    }

    function send_video(){
        

        $user = $this->ion_auth->user()->row();
        $email  = $user->email;
        $name   = $user->username;
                
        $orig_file_name     = $_FILES['afile']['name'];

        //form validation
        $errors = array();
        $fields = array();

        if($orig_file_name == "") {
            $errors[] = "No file found";
            $fields[] = "file_upload";
        }

        if(!$errors) {

            $orig_file_name     = $_FILES['afile']['name'];
            $config['upload_path'] = './assets/attachments';
            $config['allowed_types'] = 'jpeg|jpg|doc|docx|xls|docx|png|mp4|avi';
            $config['max_size'] = '1024000'; // 50MB
            $config['encrypt_name'] = TRUE;
            $this->load->library('upload', $config);

            if ($this->upload->do_upload('afile'))
            {
                $has_attachment = 1;
            }     

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
            $subject = "Video review submitted by $name";

            $this->email->from("$email", "$name");
            $this->email->to('australiawow@gmail.com'); 
            //$this->email->to('info@topappstoday.com'); 
            $this->email->subject("$subject");
            $this->email->message("$subject");  

            $data1 = array('upload_data' => $this->upload->data('afile'));
            $attachments_path1      = $data1['upload_data']['full_path'];
            $this->email->attach($attachments_path1);

            if ($this->email->send())
            {
                $response['msgStatus'] = "ok";
                $response['message'] = "Thank you for submitting!";
  
            }else {
                $response['msgStatus'] = "error";
                $response['message'] = "An error occured while trying to send your message. Please try again later.";
            }

            $response['msgStatus'] = "ok";
            $response['message'] = "Thank you for submitting!";
        }else{
            $response['msgStatus'] = "error";
            $response['errors'] = $errors;
            $response['errorFields'] = $fields;
        }
        
        header('Content-type: application/json');
        echo json_encode($response);       
    }

    function upload_pictures(){
        $user = $this->ion_auth->user()->row();

        $o['username'] = ucwords($user->username);
        $o['contact_table']     = true;
        $o['baseurl'] = $this->baseurl;    
        $this->data['o'] = $o;
       // $this->_render_page('support', $this->data);
        $this->load->view('review/upload_pictures', $this->data);    
    }

    function send_photo(){
        

        $user = $this->ion_auth->user()->row();
        $email  = $user->email;
        $name   = $user->username;
                
        $orig_file_name     = $_FILES['afile']['name'];

        //form validation
        $errors = array();
        $fields = array();

        if($orig_file_name == "") {
            $errors[] = "No file found";
            $fields[] = "file_upload";
        }

        if(!$errors) {

            $orig_file_name     = $_FILES['afile']['name'];
            $config['upload_path'] = './assets/attachments';
            $config['allowed_types'] = 'jpeg|jpg|doc|docx|xls|docx|png|pdf|pot|ppt|pptx|dot|dotx|ods';
            $config['max_size'] = '1024000'; // 50MB
            $config['encrypt_name'] = TRUE;
            $this->load->library('upload', $config);

            if ($this->upload->do_upload('afile'))
            {
                $has_attachment = 1;
            }     
            
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
            $subject = "Image review submitted by $name";

			$this->email->from('support@tubemasterpro.com', 'TubeTargetReview');
            //$this->email->from("$email", "$name");
            $this->email->to('australiawow@gmail.com'); 
            $this->email->to('info@topappstoday.com'); 
            $this->email->subject("$subject");
            $this->email->message("$subject");  

            $data1 = array('upload_data' => $this->upload->data('afile'));
            $attachments_path1      = $data1['upload_data']['full_path'];
            $this->email->attach($attachments_path1);

            if ($this->email->send())
            {
                $response['msgStatus'] = "ok";
                $response['message'] = "Thank you for submitting!";
  
            }else {
                $response['msgStatus'] = "error";
               // $response['message'] = $this->email->print_debugger();
                $response['message'] = "An error occured while trying to send your message. Please try again later.";
            }
        }else{
            $response['msgStatus'] = "error";
            $response['errors'] = $errors;
            $response['errorFields'] = $fields;
        }
        
        header('Content-type: application/json');
        echo json_encode($response); 
    }

    function send_text(){

        if (!$this->ion_auth->logged_in() )
        {
            return show_error('Permission denied, Please login.');
        }  

        $message                = $this->cleanup($this->input->post('comments'));
        $new_filename           = $this->cleanup($this->input->post('new_filename'));
        $original_filename      = $this->cleanup($this->input->post('original_filename'));
        
        $user = $this->ion_auth->user()->row();
        $email  = $user->email;
        $name   = $user->username;
        //form validation
        $errors = array();
        $fields = array();


        $attach_photo     = $_FILES['afile']['name'];
        $config['upload_path'] = './assets/attachments';
        $config['allowed_types'] = 'jpeg|jpg|doc|docx|xls|docx|png|pdf|pot|ppt|pptx|dot|dotx|ods';
        $config['max_size'] = '1024000'; // 50MB
        $config['encrypt_name'] = TRUE;
        $this->load->library('upload', $config);

        if ($this->upload->do_upload('afile'))
        {
            $has_attachment = 1;
        } 

        if($message == "") {
            $errors[] = "Message is empty";
            $fields[] = "comments";
        }

        if(strlen($message) < 140){
            $errors[] = "Message review atleast 140 characters";
            $fields[] = "comments"; 
        }
        if($attach_photo == "") {
            $errors[] = "Please attached image";
            $fields[] = "afile";
        }      
        if(!$errors) {
   
            $this->load->library('email');
            $config['protocol'] = 'smtp';
            $config['smtp_host'] = 'localhost';
            $config['smtp_port'] = '25';
            // $config['smtp_host'] = 'box342.bluehost.com';
            // $config['smtp_user'] = 'nathan@nathanhague.com';
            // $config['smtp_pass'] = '$Wolfman1';
            // $config['smtp_port'] = '26';
            $config['charset'] = 'iso-8859-1';
            $config['wordwrap'] = TRUE;
            $config['mailtype'] = 'html';

            $this->email->initialize($config);
            $subject = "Review submitted by $name";
            $message = stripslashes($message);
            if($original_filename != ""){
                $add_msg = "<br> <hr> Video attachment: <br> <strong>Filename: </strong> : $original_filename <br>
                <strong>Video path: </strong> : " . $this->baseurl . "assets/attachments/$new_filename";
            }
            //$this->email->from("$email", "TubeTargetReview");
            $this->email->from('support@tubemasterpro.com', 'TubeTargetReview');
            $this->email->reply_to("$email", "$name");
            $this->email->to('australiawow@gmail.com'); 
            //$this->email->to('info@topappstoday.com'); 
            $this->email->subject("$subject");
            $this->email->message("$message $add_msg");  
        
            $data1 = array('upload_data' => $this->upload->data('afile'));
            $attachments_path1      = $data1['upload_data']['full_path'];
            $this->email->attach($attachments_path1);

            if ($this->email->send())
            {
                $response['msgStatus'] = "ok";
                $response['message'] = "Thank you for submitting!";


                $data = array(
                       'has_review' => 1
                    );
                  $this->db->where('id', $user->id);
                  $this->db->update('users', $data);   

  
            }else {
                $response['msgStatus'] = "error";
                $response['message'] = $this->email->print_debugger();
            }
        }else{
            $response['msgStatus'] = "error";
            $response['errors'] = $errors;
            $response['errorFields'] = $fields;
        }
        
        header('Content-type: application/json');
        echo json_encode($response); die();
    }
    function leave_review(){
        $user = $this->ion_auth->user()->row();

        if (!$this->ion_auth->logged_in() )
        {
            return show_error('Permission denied, Please login.');
        }  

        $o['username'] = ucwords($user->username);
        $o['contact_table']     = true;
        $o['baseurl'] = $this->baseurl;    
        $this->data['o'] = $o;
       // $this->_render_page('support', $this->data);
        $this->load->view('review/leave_review', $this->data);    
    }

    function upload_file(){

        if (!empty($_FILES)) {

            $orig_file_name     = $_FILES['afile1']['name'];
            $config['upload_path'] = './assets/attachments';
            $config['allowed_types'] = 'jpeg|jpg|doc|docx|xls|docx|png|pdf|pot|ppt|pptx|dot|dotx|ods|avi|mp4';
            $config['max_size'] = '1024000'; // 50MB
            $config['encrypt_name'] = TRUE;
            $this->load->library('upload', $config);

            // Validate the file type           
            //$fileParts = md5(time() . "_" . $_FILES['Filedata']['name']);
            //$config['file_name'] = $fileParts;
            $filetype   = $_FILES['afile']['type'];         
            $file_name  = $_FILES['afile']['name'];
            $Filedata   = $_FILES['afile']['tmp_name'];     


            if ( ! $this->upload->do_upload('afile1'))
            {
                //$error = array('error' => $this->upload->display_errors());
                //echo strip_tags($error['error']);
                $json = json_encode(array(
                  'error' => 1,
                  'error_msg'  => $this->upload->display_errors()
                    ));
                echo $json;
            }
            else
            {
                    $data2 = array('upload_data' => $this->upload->data());
                    $new_filename           = $data2['upload_data']['file_name'];
                    $original_filename      = $data2['upload_data']['client_name'];
                    $file_size              = $data2['upload_data']['file_size'];
                    $upload_type            = $data2['upload_data']['file_ext'];
                    $upload_is_image        = $data2['upload_data']['is_image'];



                $json = json_encode(array(
                  'error' => 0,
                  'error_msg'  => "success",
                  'filename' => $new_filename,
                  'orig_filename' => $original_filename
                    ));
                echo $json;

            }
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
}
