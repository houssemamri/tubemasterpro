<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
ini_set('auto_detect_line_endings', true);
ini_set('memory_limit','-1');
ini_set('max_execution_time', '3600');

class Optimizer_ajax extends Ajax_Controller {

	function __construct () {
		parent::__construct();
        $this->load->library('authentication', NULL, 'ion_auth');

        
        // echo $this->target_model->create();
        // die();

	}


function upload_file(){
		
        if (!empty($_FILES)) {

            $orig_file_name     = $_FILES['campaign_file']['name'];
            $config['upload_path'] = './assets/uploads/optimizer';
            //$config['allowed_types'] = 'jpeg|jpg|doc|docx|xls|docx|png|pdf|pot|ppt|pptx|dot|dotx|ods|avi|mp4|mov';
            $config['allowed_types'] = 'txt|csv|xls';
            $config['max_size'] = '1024000'; // 50MB
            $config['encrypt_name'] = TRUE;
            $this->load->library('upload', $config);
            $filetype   = $_FILES['campaign_file']['type']; 
            $fileparts = explode("_",$orig_file_name);     
               
			if ($fileparts[0]=="campaign" && $fileparts[1] == "full"){
				
	            if ( ! $this->upload->do_upload('campaign_file'))
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
						$importpath				= $config['upload_path']."/".$new_filename;
						$text_url_path 			= "http://www.tubemasterpro.com";
						//$text_url_path			= "http://10.62.0.110";
						$importdata				= $this->csvtotxt($importpath,$text_url_path);
						
						//delete uploaded file
						unlink($importpath);
							
	                   $json = json_encode(array(
	                   'error' => 0,
	                  'error_msg'  => "success",
	                  'parse_data' => $importdata
	                    ));
	                echo $json;
	                	
	            }
	        }else{
	        	$json = json_encode(array(
	                  'error' => 1,
	                  'error_msg'  => 'Please Upload file from Adwords Export:'
	                    ));
	                echo $json;
	        }
        }   

    }
    
	
	function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
    
    function file_get_contents_utf8($fn) { 
            $content = file_get_contents($fn); 
            return mb_convert_encoding($content, 'UTF-8', mb_detect_encoding($content, 'UTF-8, ISO-8859-1', true)); 
    } 


    function csvtotxt($path,$url_path){
 
        //$url = 'http://203.201.129.15/AWSERVICE_WP_WEB/awws/AWService_WP.awws?wsdl';
        //$url ='http://203.201.129.15/TTP_WEB/awws/TTP.awws?wsdl';
        $url = 'http://203.201.129.15/TUBEMASTERPRO_WEB/awws/TTP.awws?wsdl';
        $client = new SoapClient($url);
        $field['data'] = array();

        $create_name = $this->generateRandomString() . ".txt";
        //get csv file content
        $data = $this->file_get_contents_utf8("$path");

        $write_file = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x80-\x9F]/u','',$data);
        $text_path = "assets/csvtotxt/$create_name";
        $myfile = fopen("$text_path", "w") or die("Unable to open file!");
        fwrite($myfile, $write_file);
        fclose($myfile);     

        $field['data']['argText'] = "$url_path/$text_path";
        $response   =  (array) $client->__soapCall("pcActions_Parse_AdWordsImport", array($field['data']));
        $result     = $response['pcActions_Parse_AdWordsImportResult'];
        $exp_one    = explode("<sJSON>",$result);
        $exp_two    = explode("</sJSON>",$exp_one[1]);
        
        
        //delete text file
        unlink($text_path);
        
        //return  data
		return $exp_two[0];
        //return $field['data']['argText'];

    }

    function get_ImportData () {

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
    
    function get_target_list()
    {
        $this->load->database();
        
        $this->load->model('target_model');
        $user = $this->ion_auth->user()->row();
        
    	
        $targets = $this->target_model->get_all( $user->id );

	        foreach ($targets as $key => &$value) {
	            $value->data = unserialize($value->data);
			}
      
        
        if($targets){
			$json = json_encode(array(
               'error' => 0,
              'error_msg'  => "success",
              'target_data' => $targets
                ));
            echo $json;
        
	    }else{
		    $json = json_encode(array(
                  'error' => 1,
                  'error_msg'  => "No Targets Available"
                    ));
            echo $json;
	    }
        
	}
}	