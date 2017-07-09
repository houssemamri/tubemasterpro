<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Campaign_ajax extends Ajax_Controller {

	function __construct () {
		parent::__construct();
        $this->load->library('authentication', NULL, 'ion_auth');
        // $this->load->model('target_model');
        // $user = $this->ion_auth->user()->row();
        
        // echo $this->target_model->create();
        // die();
	}
	
	function insert_page(){
	    $video_url = $this->input->post('url');
	    $user = $this->ion_auth->user()->row();
		$video_data=explode("=", $video_url);
	    $data = array(
	       'video_ad' => $video_data[1],
	       'user_id' => $user->id
	    );
	 
		$this->db->insert('campaign', $data);    
		$ads ="<option value='".$video_data[1]."' >https://www.youtube.com/watch?v=".$video_data[1]."</option>";
		echo $ads;
		die();
	}
	
	
	function get_video_ads(){
		$user = $this->ion_auth->user()->row();
		$ads = "";
		$sql = "select * from campaign where user_id = '$user->id'";
		$shw_r = $this->db->query($sql);
		if($shw_r->num_rows() == 0){
			$ads="No Video Ads Available";
		}else{
			// $show = array();
			foreach($shw_r->result_array() as $sr){
				$ads.="<option value='".$sr['video_ad']."' >https://www.youtube.com/watch?v=".$sr['video_ad']."</option>";
			}
		}
	
		echo $ads;
	
	}
	
	function remove_page(){
		$user = $this->ion_auth->user()->row();
		$vid_id = "";
		$vid_id = $this->input->post('vid_id');
		
		$sql = "delete from campaign where video_ad = '$vid_id' and user_id = '$user->id'";
		$shw_r = $this->db->query($sql);
		echo "success";
	
	}
	
	
	function export_settings(){
		// $filename = secure_get('f')."_".date("Y-m-d_His").".csv";
	
		//get values
	
		$filename = 'test.csv';
		header('Content-type: text/csv');
		header('Content-Disposition: attachment; filename="'.$filename.'"');
	
	
			// while($data = mysql_fetch_assoc($rs)){
			// 	$userid = $data['user_id'];
			// 	$email = getEmailByUID($data['user_id']);
			// 	$name = getFullNameByUID($data['user_id']);
			// 	$dadded = $data['date_added'];
			// 	$data_arr[] = array($userid, $name, $email, $dadded);
			// }
		/*
			$data_header = array("NAME", "EMAIL");
	
			$data_array = array(
				$data_header,
				$data_arr
			);
		*/
		
			$data_arr = array(
			 array('data11', 'data12', 'data13'),
			 array('data21', 'data22', 'data23'),
			 array('data31', 'data32', 'data23')
			);
		
			
			$file = fopen("php://output","w");
			$data_header = array("Campaign","Campaign Daily Budget","Languages","Location ID","Location","Networks","Ad Group","Max CPC","Flexible Reach","Display Network Custom Bid Type","Keyword","Type","Bid adjustment","Headline","Description Line 1","Description Line 2","Sitelink text","Display URL","Destination URL","Platform targeting","Device Preference","Ad Schedule");
			fputcsv($file, $data_header);
			
			foreach ($data_arr as $line){
			  	fputcsv($file, $line);
			}
			
			fclose($file);
	
	
	
		die();
	}
	
}