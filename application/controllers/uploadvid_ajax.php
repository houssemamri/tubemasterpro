<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Uploadvid_ajax extends Ajax_Controller {

	function __construct () {
		parent::__construct();
        $this->load->library('authentication', NULL, 'ion_auth');
        // $this->load->model('target_model');
        // $user = $this->ion_auth->user()->row();
        
        // echo $this->target_model->create();
        // die();

	}


function upload_file(){

        if (!empty($_FILES)) {

            $orig_file_name     = $_FILES['afile1']['name'];
            $config['upload_path'] = './assets/uploads/raw';
            //$config['allowed_types'] = 'jpeg|jpg|doc|docx|xls|docx|png|pdf|pot|ppt|pptx|dot|dotx|ods|avi|mp4|mov';
            $config['allowed_types'] = 'mts|avi|mp4|mov';
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

    function update_UploadData () {

        $original_filename = $this->input->post('original_filename');
        $upload_id = $this->input->post('upload_id');
        $new_filename = $this->input->post('new_filename');
        $baseurl = $this->input->post('baseurl');
      

    	$date = now();	
    	$data = array(
                   'orig_filename' => $original_filename,
                   'video_path' => $new_filename,
                   'upload_status' => "1",
                   'date_uploaded' => $date
                );

    	$this->db->where('id', $upload_id);
    	$this->db->update('paypal_exp', $data);

    }

function send_Email () {

    $user = $this->ion_auth->user()->row();
    $email  = $user->email;
    $name   = $user->username;
    $orig_filename = $this->input->post('original_filename');

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
            $subject = "A New Video, $orig_filename was submitted for processing by $name";

            $this->email->from("$email", "$name");
            $this->email->to('australiawow@gmail.com,evka9@hotmail.com'); 
            //$this->email->to('edwin_n_b@yahoo.com, vicenteiii3@yahoo.com'); 
            $this->email->subject("$subject");
            $this->email->message("$subject");

            //$data1 = array('upload_data' => $this->upload->data('afile'));
            //$attachments_path1      = $data1['upload_data']['full_path'];
            //$this->email->attach($attachments_path1);

            if ($this->email->send())
            {
                $response['msgStatus'] = "ok";
                $response['message'] = "Thank you for submitting!";
  
            }else 
            {
                $response['msgStatus'] = "error";
                $response['message'] = "An error occured while trying to send your message. Please try again later.";
            }
            $response['msgStatus'] = "error";
            $response['errors'] = $errors;
            $response['errorFields'] = $fields;

        return $response;
    }
}