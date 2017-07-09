<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
ini_set('auto_detect_line_endings', true);
ini_set('memory_limit','-1');
ini_set('max_execution_time', '3600');

class Optimizer_ajax extends Ajax_Controller {

	private $yt_dev_key = array(
		"AIzaSyDg1hQx7_9wD0DEbHxkT6bMi7K8kk8uZKs",//-NATE
		"AIzaSyBk4tmWtVhiNL6gYqzGU_tEj85GkStQbK8",//-CHRIS
		"AIzaSyCSNw-ijFgCl-OLcilgEV3PwcAxZ3QLsV0",//-RENE
		"AIzaSyBR-0PCJOPADmueJqL3fK6bn68xITfVNXM",//-MAO
		"AIzaSyAjLi2U439nlc1R4p3fK0F2qIuZXil-6D0",//-EDWIN
	);

	function __construct () {
		parent::__construct();
        $this->load->library('authentication', NULL, 'ion_auth');

        
        // echo $this->target_model->create();
        // die();

	}
	
	function generate_ytkey () {
		$key = array_rand($this->yt_dev_key);
		return $this->yt_dev_key[$key];
	}
	
	function save_changes () {
		$id    = $this->input->post('id');
		$cdata = $this->input->post('ads');
		if ( isset($id) && !empty($id) ) {
			$save_data = array(
				'data'			=> serialize($cdata),
				'date_uploaded' => time()
			);
			
			$this->load->model('target_model');
			$this->target_model->save_campaign_uploaded($id,$save_data);
			
			echo json_encode(array('valid' => true));exit();
		}
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
	                echo $json;exit();
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
						$text_url_path 			= "http://www.tubemasterpro.dev";
						//$text_url_path			= "http://10.62.0.110";
						$importdata				= $this->csvtotxt($importpath,$text_url_path);
						
						//delete uploaded file
						unlink($importpath);
						
						$xmlphp = (array)simplexml_load_string($importdata)->FormattedDataStructure->sJSON;
						$cjson = "[".$xmlphp[0]."]";
						//$json1 = json_encode($xmlphp[0]);
						$cjson = json_decode($cjson,true);
						//echo json_last_error(); // 4 (JSON_ERROR_SYNTAX)
						//echo json_last_error_msg(); // unexpected character 
						
						$adsData = array();
						$adsCnt	 = 0;
						$return  = false;
						
						foreach( $cjson as $campaign ) {
							//echo $key . ' == ' . $val;
							if ( $campaign['Input ads here'] ) {
								$adsData[$adsCnt]['cid']	= trim($campaign['Input ads here']['Campaign']);
								
								foreach ( $campaign['Input ads here']['data'] as $ads ) {
									$adsData[$adsCnt]['vid']	= trim($ads['Video id']);
									$adsData[$adsCnt]['views']	= trim($ads['Views (Ignored)']);
									$adsData[$adsCnt]['clicks'] = trim($ads['Clicks (Ignored)']);
								}
								$adsCnt++;
							}
						}
						
						$cid = $this->input->post('cid');
				        $this->load->model('target_model');
						$uploaded = $this->target_model->get_campaign_uploaded($cid);
						
						if ( $uploaded ) {
							//- Compare here
							//- Check if it's the right Campaign
							$return = array();
							$return['cup_id'] = $uploaded->id;
							$ca_data = unserialize($uploaded->data);
							if ( trim($ca_data[0]['cid']) == trim($adsData[0]['cid']) ) {
								//- Check changes
								$ca_cnt 	= 0;
								$diff_cnt 	= 0;
								foreach ( $ca_data as $ca_ad ) {
									foreach ( $adsData as $adData ) {
										if ( trim($ca_ad['vid']) == trim($adData['vid']) ) {
											if ( $adData['clicks'] != $ca_ad['clicks'] ) {
												$return['campaign']['ads'][$ca_cnt]['diff']   = true;
												$diff_cnt++;
											}
											else {
												$return['campaign']['ads'][$ca_cnt]['diff']   = false;
											}
											
											$return['campaign']['ads'][$ca_cnt]['updata'] = $adData;
											$return['campaign']['ads'][$ca_cnt]['dbdata'] = $ca_ad;
											$ca_cnt++;
										}
									}
								}
								
								if ( $diff_cnt > 0 ) {
									$return['diff'] = true;
									$return['msg']  = 'Diff';
								}
								else {
									$return['diff'] = false;
									$return['msg']  = 'No Changes';
								}
							}
							else {
								$return['diff'] = false;
								$return['msg']  = 'Campaign does not match! Please upload the correct campaign CSV file.';
							}
						}
						else {
							$user 	 = $this->ion_auth->user()->row();
							$c_name  = trim(explode('(',$cjson[0]['Input campaigns here']['data'][0]['Campaign'])[0]);
							$c_orig  = $this->target_model->get_campaign($cid,$user->id);
							
							if ( strtolower($c_name) == strtolower($c_orig->name) ) {
								$cup_data = array(
									'cid' 			=> $cid,
									'data'			=> serialize($adsData),
									'date_uploaded' => time()
								);
								
								$this->target_model->save_campaign_uploaded(null,$cup_data);
								$return['diff'] = false;
								$return['msg']  = 'Statistics Saved';
							}
							else {
								$return['diff'] = false;
								$return['msg']  = 'Campaign does not match! Please upload the correct campaign CSV file.';
							}
							
						}
							
	                    $json = json_encode(array(
	                   		'error' => 0,
					   		'error_msg'  => "success",
					   		'parse_data' => $return
	                    ));
	                echo $json;
					exit();
	                	
	            }
	        }else{
	        	$json = json_encode(array(
	                  'error' => 1,
	                  'error_msg'  => 'Please Upload file from Adwords Export:'
	                    ));
	                echo $json;exit();
	        }
        }
        else {
	        
			echo json_encode(array('id'=>'waaaa', 'files' =>'FILES!!'));
			exit();
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
		return $result;//$exp_two[0];
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