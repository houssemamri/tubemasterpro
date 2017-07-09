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
	
	function get_campaign_upload () {
		$this->load->model('target_model');
		$uploaded = $this->target_model->get_campaign_uploaded(20);
		echo '<pre>';
		print_r(unserialize($uploaded->data));
		echo '</pre>';
	}
	
	function multiple_delete_campaign () {
		$ids = $this->input->post('ids');
		$this->load->model('target_model');
		$return = $this->target_model->multiple_delete_campaign($ids);
		echo $return;exit();
	}
	
	function get_campaign_info () {
		$cid  = $this->input->post('cid');
        $user = $this->ion_auth->user()->row();
        
		$this->load->model('target_model');
		$campaign = $this->target_model->get_campaign($cid,$user->id);
		
		$html  = '<div class="row">';
		$html .= '<div class="col-md-12">';
		
		//- Name
		$html .= '<div class="row bg-info">';
		$html .= '<div class="col-sm-4 text-right">';
		$html .= '<strong>Campaign Name: </strong>';
		$html .= '</div>';
		$html .= '<div class="col-sm-8">';
		$html .= '<span>'.$campaign->name.'</span>';
		$html .= '</div>';
		$html .= '</div>';
		//- Video Ads
		$html .= '<div class="row">';
		$html .= '<div class="col-sm-4 text-right">';
		$html .= '<strong>Video Ads: </strong>';
		$html .= '</div>';
		$html .= '<div class="col-sm-8">';
		$html .= '<ol>';
		$videoads = explode(',', $campaign->video_ads);
    	foreach ($videoads as $value) {
        	$html .= '<li><a class="video_link" href="https://www.youtube.com/watch?v='.$value.'">https://www.youtube.com/watch?v='.$value.'</a></li>';
        }
		$html .= '</ol>';
		$html .= '</div>';
		$html .= '</div>';
		//- Display URL
		$html .= '<div class="row bg-info">';
		$html .= '<div class="col-sm-4 text-right">';
		$html .= '<strong>Display URL: </strong>';
		$html .= '</div>';
		$html .= '<div class="col-sm-8">';
		$html .= '<span>'.$campaign->display_url.'</span>';
		$html .= '</div>';
		$html .= '</div>';
		//- Destination URL
		$html .= '<div class="row">';
		$html .= '<div class="col-sm-4 text-right">';
		$html .= '<strong>Destination URL: </strong>';
		$html .= '</div>';
		$html .= '<div class="col-sm-8">';
		$html .= '<span>'.$campaign->destination_url.'</span>';
		$html .= '</div>';
		$html .= '</div>';
		//- Target Group
		$html .= '<div class="row bg-info">';
		$html .= '<div class="col-sm-4 text-right">';
		$html .= '<strong>Target Group: </strong>';
		$html .= '</div>';
		$html .= '<div class="col-sm-8">';
		$html .= '<ol>';
    	$targets  = explode(',', $campaign->target_lists);
    	foreach ($targets as $tid) {
    		$target = $this->target_model->get($tid);
        	$videos = unserialize($target->data);
			$html .= '<li>'.$target->name.' ('.count($videos).')</li>';
        }
		$html .= '</ol>';
		$html .= '</div>';
		$html .= '</div>';
		//- Daily Budget
		$html .= '<div class="row">';
		$html .= '<div class="col-sm-4 text-right">';
		$html .= '<strong>Daily Budget: </strong>';
		$html .= '</div>';
		$html .= '<div class="col-sm-8">';
		$html .= '<span>'.$campaign->daily_budget.'</span>';
		$html .= '</div>';
		$html .= '</div>';
		//- Max CPV
		$html .= '<div class="row bg-info">';
		$html .= '<div class="col-sm-4 text-right">';
		$html .= '<strong>Max CPV: </strong>';
		$html .= '</div>';
		$html .= '<div class="col-sm-8">';
		$html .= '<span>$ '.$campaign->max_cpv.'</span>';
		$html .= '</div>';
		$html .= '</div>';
		//- Start Date
		$html .= '<div class="row">';
		$html .= '<div class="col-sm-4 text-right">';
		$html .= '<strong>Start Date: </strong>';
		$html .= '</div>';
		$html .= '<div class="col-sm-8">';
		$html .= '<span>'.date('F d, Y',strtotime($campaign->start_date)).'</span>';
		$html .= '</div>';
		$html .= '</div>';
		//- End Date
		$html .= '<div class="row bg-info">';
		$html .= '<div class="col-sm-4 text-right">';
		$html .= '<strong>End Date: </strong>';
		$html .= '</div>';
		$html .= '<div class="col-sm-8">';
		if ( $campaign->end_date == '#N/A' ) {
			$html .= '<span>'.$campaign->end_date.'</span>';
		}
		else {
			$html .= '<span>'.date('F d, Y',strtotime($campaign->end_date)).'</span>';
		}
		$html .= '</div>';
		$html .= '</div>';
		//- Delivery Method
		$html .= '<div class="row">';
		$html .= '<div class="col-sm-4 text-right">';
		$html .= '<strong>Delivery Method: </strong>';
		$html .= '</div>';
		$html .= '<div class="col-sm-8">';
		$html .= '<span>'.$campaign->delivery_method.'</span>';
		$html .= '</div>';
		$html .= '</div>';
		//- Mobile Bid Modifier
		$html .= '<div class="row bg-info">';
		$html .= '<div class="col-sm-4 text-right">';
		$html .= '<strong>Mobile Bid Modifier: </strong>';
		$html .= '</div>';
		$html .= '<div class="col-sm-8">';
		if ( $campaign->mbm_sign == 0 ) {
			$html .= '<span>'.$campaign->mbm_value.'%</span>';
		}
		else if ( $campaign->mbm_sign == '-' ) {
			$html .= '<span>Decrease By '.$campaign->mbm_value.'%</span>';
		}
		else {
			$html .= '<span>Increase By'.$campaign->mbm_value.'%</span>';
		}
		$html .= '</div>';
		$html .= '</div>';
		//- Language
		$html .= '<div class="row">';
		$html .= '<div class="col-sm-4 text-right">';
		$html .= '<strong>Language: </strong>';
		$html .= '</div>';
		$html .= '<div class="col-sm-8">';
		$html .= '<span style="word-wrap: break-word;">'.$campaign->language.'</span>';
		/*
$html .= '<ol>';
    	$language  = explode(',', $campaign->language);
    	foreach ($language as $lang) {
			$html .= '<li>'.$lang.'</li>';
        }
		$html .= '</ol>';
*/
		$html .= '</div>';
		$html .= '</div>';
		//- Location
		$html .= '<div class="row bg-info">';
		$html .= '<div class="col-sm-4 text-right">';
		$html .= '<strong>Location: </strong>';
		$html .= '</div>';
		$html .= '<div class="col-sm-8">';
		$html .= '<span style="word-wrap: break-word;">'.$campaign->countries.'</span>';
		/*
$html .= '<ol>';
    	$countries  = explode(',', $campaign->countries);
    	foreach ($countries as $country) {
			$html .= '<li>'.$country.'</li>';
        }
		$html .= '</ol>';
*/
		$html .= '</div>';
		$html .= '</div>';
		//- Age
		$html .= '<div class="row">';
		$html .= '<div class="col-sm-4 text-right">';
		$html .= '<strong>Age: </strong>';
		$html .= '</div>';
		$html .= '<div class="col-sm-8">';
		$html .= '<span style="word-wrap: break-word;">'.$campaign->age.'</span>';
		/*
$html .= '<ol>';
    	$ages  = explode(',', $campaign->age);
    	foreach ($ages as $age) {
			$html .= '<li>'.$age.'</li>';
        }
		$html .= '</ol>';
*/
		$html .= '</div>';
		$html .= '</div>';
		//- Gender
		$html .= '<div class="row bg-info">';
		$html .= '<div class="col-sm-4 text-right">';
		$html .= '<strong>Gender: </strong>';
		$html .= '</div>';
		$html .= '<div class="col-sm-8">';
		$html .= '<span style="word-wrap: break-word;">'.$campaign->gender.'</span>';
		/*
$html .= '<ol>';
    	$genders  = explode(',', $campaign->gender);
    	foreach ($genders as $gender) {
			$html .= '<li>'.$gender.'</li>';
        }
		$html .= '</ol>';
*/
		$html .= '</div>';
		$html .= '</div>';
		
		
        
		$html .= '</div>';
		$html .= '</div>';
		
		echo $html;die();
	}


function upload_file(){
	/*
$importpath		= $config['upload_path']."/e8a7b7643306c2e5d09f794216f35f5b.csv";//$config['upload_path']."/1d5b3e1f296d667d4858bee84d013111.csv";
	$text_url_path	= "http://www.tubemasterpro.dev";
	$importdata		= $this->csvtotxt($importpath,$text_url_path);
	$xmlphp = (array)simplexml_load_string($importdata)->FormattedDataStructure->sJSON;
	$json = "[".$xmlphp[0]."]";
	//$json1 = json_encode($xmlphp[0]);
	$json = json_decode($json,true);
	//echo json_last_error(); // 4 (JSON_ERROR_SYNTAX)
	//echo json_last_error_msg(); // unexpected character 
	//echo '<pre>';
	//print_r($json);
	//echo '</pre>';exit();
	$adsData = array();
	$adsCnt	 = 0;
	
	foreach( $json as $campaign ) {
		//echo $key . ' == ' . $val;
		if ( $campaign['Input ads here'] ) {
			foreach ( $campaign['Input ads here']['data'] as $key => $ad ) {
				$adsData['ads'][$key]['id']		= $ad['Video id'];
				$adsData['ads'][$key]['views']	= $ad['Views (Ignored)'];
				$adsData['ads'][$key]['clicks'] = $ad['Clicks (Ignored)'];
			}
		}
		
		if ( $campaign['Input targeting groups here'] ) {
			foreach ( $campaign['Input targeting groups here']['data'] as $key => $tgroup ) {
				$adsData['tgroups'][$key]['id']	    = explode(')',end(explode('(',$tgroup['Targeting group'])))[0];
				$adsData['tgroups'][$key]['views']	= $tgroup['Views (Ignored)'];
				$adsData['tgroups'][$key]['clicks'] = $tgroup['Clicks (Ignored)'];
			}
		}
		
		if ( $campaign['Input targets here'] ) {
			$targetCnt = 0;
			foreach ( $campaign['Input targets here']['data'] as $key => $target ) {
				if ( $target['Type'] == 'YouTube video' ) {
					$adsData['targets'][$targetCnt]['tgroup']	= explode(')',end(explode('(',$target['Targeting group'])))[0];
					$adsData['targets'][$targetCnt]['id']	    = explode(')',end(explode('(youtube.com/video/',$target['Target'])))[0];
					$adsData['targets'][$targetCnt]['views']	= $target['Views (Ignored)'];
					$targetCnt++;
				}
			}
		}
	}
	
	echo '<pre>';
	print_r($adsData);
	echo '</pre>';
	$cid = $this->input->post('cid');
	if ( isset($cid) && !empty($cid) ) {
        $this->load->database();
        $this->load->model('target_model');
		$uploaded = $this->target_model->get_campaign_uploaded($cid);
		
		if ( $uploaded ) {
			//- Compare here
		}
		else {
			$cup_data = array(
				'cid' 			=> $cid,
				'data'			=> serialize($adsData),
				'date_uploaded' => time()
			);
			$uploaded = $this->target_model->save_campaign_uploaded($cup_data);
			
		}
	}
	//echo json_encode($uploaded);
	exit();
*/
	

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
						$text_url_path 			= "http://www.nathanhague.com/tubetargetpro/";
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
							if ( $campaign['Input campaigns here'] ) {
								if ( count($campaign['Input campaigns here']['data']) > 1 ) {
									$return['diff'] = false;
									$return['msg']  = 'Invalid AdWords Campaign CSV. Please upload the correct Campaign CSV file.';
									$json = json_encode(array(
				                   		'error' => 0,
								   		'error_msg'  => "success",
								   		'parse_data' => $return
				                    ));
					                echo $json;
									exit();
									break;
								}
							}
							
							//echo $key . ' == ' . $val;
							if ( $campaign['Input ads here'] ) {
								foreach ( $campaign['Input ads here']['data'] as $key => $ad ) {
									$adsData['ads'][$key]['id']		= $ad['Video id'];
									$adsData['ads'][$key]['views']	= $ad['Views (Ignored)'];
									$adsData['ads'][$key]['clicks'] = $ad['Clicks (Ignored)'];
								}
							}
							
							if ( $campaign['Input targeting groups here'] ) {
								foreach ( $campaign['Input targeting groups here']['data'] as $key => $tgroup ) {
									$adsData['tgroups'][$key]['id']	    = explode(')',end(explode('(',$tgroup['Targeting group'])))[0];
									$adsData['tgroups'][$key]['views']	= $tgroup['Views (Ignored)'];
									$adsData['tgroups'][$key]['clicks'] = $tgroup['Clicks (Ignored)'];
								}
							}
							
							if ( $campaign['Input targets here'] ) {
								$targetCnt = 0;
								foreach ( $campaign['Input targets here']['data'] as $key => $target ) {
									if ( $target['Type'] == 'YouTube video' ) {
										$adsData['targets'][$targetCnt]['tgroup']	= explode(')',end(explode('(',$target['Targeting group'])))[0];
										$adsData['targets'][$targetCnt]['id']	    = explode(')',end(explode('(youtube.com/video/',$target['Target'])))[0];
										$adsData['targets'][$targetCnt]['views']	= $target['Views (Ignored)'];
										$targetCnt++;
									}
								}
							}
						}
						
				        $this->load->model('target_model');
						$cid 	 = $this->input->post('cid');
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
							$return['msg']  = 'Data Saved';
							$return['cid']	= $cid;
							$return['name']	= $c_name;
							$return['overview'] = $this->target_model->get_overview($cid);
						}
						else {
							$return['diff'] = false;
							$return['msg']  = 'Campaign does not match! Please upload the correct campaign CSV file.';
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
	        	$return['diff'] = false;
				$return['msg']  = 'Please Upload file from Adwords Export.';
	        	
	        	$json = json_encode(array(
	                  'error' => 1,
	                  'error_msg'  => "error",
					  'parse_data' => $return
	            ));
	            echo $json;exit();
	        }
        }
        else {
	        
			echo json_encode(array('id'=>'waaaa', 'files' =>'FILES!!'));
			exit();
        }

    }
    
    function general_upload_file(){

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
						$text_url_path 			= "http://www.nathanhague.com/tubetargetpro/";
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
						
				        $this->load->model('target_model');
						$user 	 = $this->ion_auth->user()->row();
						$c_name  = trim(explode('(',$cjson[0]['Input campaigns here']['data'][0]['Campaign'])[0]);
						$orig_c  = $this->target_model->get_campaign_by_name($c_name,$user->id);
						
						if ( $orig_c ) {
							foreach( $cjson as $campaign ) {
								if ( $campaign['Input campaigns here'] ) {
									if ( count($campaign['Input campaigns here']['data']) > 1 ) {
										$return['diff'] = false;
										$return['msg']  = 'Invalid AdWords Campaign CSV. Please upload the correct Campaign CSV file.';
										$json = json_encode(array(
					                   		'error' => 0,
									   		'error_msg'  => "success",
									   		'parse_data' => $return
					                    ));
						                echo $json;
										exit();
										break;
									}
								}
								
								//echo $key . ' == ' . $val;
								if ( $campaign['Input ads here'] ) {
									foreach ( $campaign['Input ads here']['data'] as $key => $ad ) {
										$adsData['ads'][$key]['id']		= $ad['Video id'];
										$adsData['ads'][$key]['views']	= $ad['Views (Ignored)'];
										$adsData['ads'][$key]['clicks'] = $ad['Clicks (Ignored)'];
									}
								}
								
								if ( $campaign['Input targeting groups here'] ) {
									foreach ( $campaign['Input targeting groups here']['data'] as $key => $tgroup ) {
										$adsData['tgroups'][$key]['id']	    = explode(')',end(explode('(',$tgroup['Targeting group'])))[0];
										$adsData['tgroups'][$key]['views']	= $tgroup['Views (Ignored)'];
										$adsData['tgroups'][$key]['clicks'] = $tgroup['Clicks (Ignored)'];
									}
								}
								
								if ( $campaign['Input targets here'] ) {
									$targetCnt = 0;
									foreach ( $campaign['Input targets here']['data'] as $key => $target ) {
										if ( $target['Type'] == 'YouTube video' ) {
											$adsData['targets'][$targetCnt]['tgroup']	= explode(')',end(explode('(',$target['Targeting group'])))[0];
											$adsData['targets'][$targetCnt]['id']	    = explode(')',end(explode('(youtube.com/video/',$target['Target'])))[0];
											$adsData['targets'][$targetCnt]['views']	= $target['Views (Ignored)'];
											$targetCnt++;
										}
									}
								}
							}
							$cup_data = array(
								'cid' 			=> $orig_c->id,
								'data'			=> serialize($adsData),
								'date_uploaded' => time()
							);
							
							$this->target_model->save_campaign_uploaded(null,$cup_data);
							$return['diff'] = false;
							$return['msg']  = 'Data Saved';
							$return['cid']	= $orig_c->id;
							$return['name']	= $c_name;
							$return['overview'] = $this->target_model->get_overview($cid);
						}
						else {
							$return['diff'] = false;
							$return['msg']  = 'Campaign does not match! Please upload the correct campaign CSV file.';
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
	        	$return['diff'] = false;
				$return['msg']  = 'Please Upload file from Adwords Export.';
	        	
	        	$json = json_encode(array(
	                  'error' => 1,
	                  'error_msg'  => "error",
					  'parse_data' => $return
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
		/*
header('Content-Type: text/html; charset=UTF-8');
    	mb_regex_encoding('UTF-8');
		mb_internal_encoding('UTF-8');
		setlocale(LC_ALL, 'ja_JP.EUC-JP'); 
*/
        $content = file_get_contents($fn);
		/*
$return_value = array();
		$my_file= fopen($fn, "r");
        $row = 0;
	    while (($data = fgetcsv($my_file, 1000, ",")) !== FALSE) {
	        $num = count($data);
	         for ($c=0; $c < $num; $c++) {
	            #csv file is written in euc-jp so convert to utf-8 here.
	            $return_value[$row][$c] = iconv('UTF-8', 'EUC-JP', $data[$c]);//mb_convert_encoding($data[$c], "UTF-8", "sjis-win");
	         }
	         $row++;
	    }  
	    return $return_value;
*/
        // return mb_convert_encoding($content, 'UTF-8', 'ISO-2022-JP-MS');
        return mb_convert_encoding($content, 'UTF-8', mb_detect_encoding($content, 'UTF-8, ISO-8859-1', true));
        // return mb_convert_encoding($content, 'UTF-8', mb_detect_encoding($content, 'UTF-8, ISO-8859-1, JIS, eucjp-win, sjis-win, EUC-JP', true)); 
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

        $data = str_replace('ÿþ','',$data);
        //$write_file = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x80-\x9F]/u','',$data);
        $write_file = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x80-\x9F\r]/u','',$data);
        // $write_file = utf8_encode($write_file);
        
        /*
return $write_file;
		$opts = array(
		  'http'=>array(
		    'method'=>"GET",
		    'header'=>"Content-Type: text/html; charset=UTF-8"
		  )
		);
		
		$context = stream_context_create($opts);
*/
        $text_path = "assets/csvtotxt/$create_name";
        $myfile = fopen("$text_path", "w") or die("Unable to open file!");
        fwrite($myfile, $write_file);
        fclose($myfile);

        $field['data']['argText'] = "$url_path/$text_path";
       // $field['data']['argText'] = "http://www.nathanhague.com/tubetargetpro/assets/csvtotxt/Lsmc8r7Ua8.txt";
        $response   =  (array) $client->__soapCall("pcActions_Parse_AdWordsImport", array($field['data']));
        $result     = $response['pcActions_Parse_AdWordsImportResult'];
        //$exp_one    = explode("<sJSON>",$result);
        //$exp_two    = explode("</sJSON>",$exp_one[1]);
        
        
        //delete text file
        //unlink($text_path);
        
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
	
	function get_video_details () {
		$return = array();
		$ids = $this->input->post('videoIds');
		if ( isset($ids) && !empty($ids) ) {
			$this->load->library('google');
			
	        $client = $this->google;
			$client->setDeveloperKey($this->generate_ytkey());
			
			// Define an object that will be used to make all API requests. 
	        $this->load->library('youtube', $client);
	        $youtube = $this->youtube;
	        
	        $id_groups 	= array_chunk($ids, 50);
			$videos    	= array();
			$vCount		= 0;
	        
	        foreach ( $id_groups as $id_group ) {
		        $videoIds = join(',', $id_group);
				$videosResponse = $youtube->videos->listVideos('id, snippet, statistics', array(
					'id' => $videoIds,
				));
				
				foreach ( $videosResponse['items'] as $i => $videoResponse ) {
					$videos[$vCount]['id'] 			= $videoResponse['id'];
					$videos[$vCount]['snippet'] 		= $videoResponse['snippet'];
					$videos[$vCount]['thumbnails'] 	= $videoResponse['snippet']['thumbnails']['default'];
					$videos[$vCount]['statistics'] 	= $videoResponse['statistics'];
					$vCount++;
				}
	        }
	        
			$return['ids'] 	  = $id_groups;
			$return['videos'] = $videos;//['items'][0];['snippet']['title'];
		}
		else {
			$return['videos'] = false;
		}
		//print_r((array)$videos);
		//echo $videos;
		echo json_encode($return,true);
		die();
    }
    
    function get_targets () {
		$return  = array();
		$targets = array();
		$ids = $this->input->post('target_list');
		if ( isset($ids) && !empty($ids) ) {
	        $this->load->database();
	        $this->load->model('target_model');
	        
	        foreach ( $ids as $id ) {
	        	$target = $this->target_model->get( $id );
                $target->data = unserialize($target->data);
		        $targets[] = $target;
	        }
			$return['targets'] = $targets;
		}
		else {
			$return['targets'] = false;
		}
		echo json_encode($return,true);
		die();
    }
}	