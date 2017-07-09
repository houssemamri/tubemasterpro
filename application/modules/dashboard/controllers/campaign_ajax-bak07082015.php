<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Campaign_ajax extends Ajax_Controller {

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
        $this->load->model('Logs_model', 'logs');
        // $this->load->model('target_model');
        // $user = $this->ion_auth->user()->row();
        
        // echo $this->target_model->create();
        // die();
	}
	
	function generate_ytkey () {
		$key = array_rand($this->yt_dev_key);
		return $this->yt_dev_key[$key];
	}
	
	function insert_page(){
	    /*
$video_url = $this->input->post('url');
	    $user = $this->ion_auth->user()->row();
		$video_data=explode("=", $video_url);
		$videoId = explode("&",$video_data[1]);
	    $data = array(
	       'video_ad' => $videoId[0],
	       'user_id' => $user->id
	    );
	 
		$this->db->insert('campaign', $data);    
		$ads ="<option value='".$videoId[0]."' >https://www.youtube.com/watch?v=".$videoId[0]."</option>";
		echo $ads;
		die();
*/
		$video_url = $this->input->post('url');
		$video_id  = $this->input->post('id');
	    $user = $this->ion_auth->user()->row();
	    $data = array(
	       'video_ad' => $video_id,
	       'user_id' => $user->id
	    );
	 
		$this->db->insert('campaign', $data);    
		$ads ="<option value='".$video_id."' >".$video_url."</option>";
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
				$videoId = explode("&",$sr['video_ad']);
				$ads.="<option value='".$videoId[0]."' >https://www.youtube.com/watch?v=".$videoId[0]."</option>";
			}
		}
	
		echo $ads;
	
	}
	
	function get_yt_key () {
		echo $this->generate_ytkey();die();
		//echo 'test';die();
	}
	
	function remove_page(){
		$user = $this->ion_auth->user()->row();
		$vid_id = "";
		$vid_id = $this->input->post('vid_id');
		
		$sql = "delete from campaign where video_ad = '$vid_id' and user_id = '$user->id'";
		$shw_r = $this->db->query($sql);
		echo "success";
	
	}
	
	function save_logs(){
		$filename = $this->input->post('data');
		$this->logs->insert_logs("export_campaign", "Exported campaign ".$filename.".csv for Adwords");
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
	
	//- Campaigns
	function check_campaign () {
		$name = trim($this->input->post('name'));
		$result = false;
		if ( isset($name) && !empty($name) ) {
			$user = $this->ion_auth->user()->row();
			$this->load->model('target_model');
			$result = $this->target_model->check_campaign( $name, $user->id );
		}
		echo $result;
		die();
	}
	
	function save_campaign () {
		$data = $this->input->post('data');
		$result = array();
		$result['valid'] = false;
		if ( isset($data) && !empty($data) ) {
			$result['valid'] = true;
			$user = $this->ion_auth->user()->row();
			$id   = $this->input->post('id');
			$data['user_id'] = $user->id;
			
			$this->load->model('target_model');
			$response = $this->target_model->save_campaign( $id, $data );
			
			if ( isset($id) && !empty($id) ) {
				$result['method'] = 'update';
				$result['id']	  = $response;
			}
			else {
				$result['method'] = 'add';
				$result['id']	  = $response;
			}
		}
		echo json_encode($result);
		die();
	}
	
	function copy_campaign () {
		$id = $this->input->post('id');
		$result = false;
		if ( isset($id) && !empty($id) ) {
			$user = $this->ion_auth->user()->row();
			$this->load->model('target_model');
			$result = json_encode($this->target_model->copy_campaign( $id, $user->id ));
		}
		echo $result;
		die();
	}
	
	function get_campaign () {
		$id = $this->input->post('id');
		$result = false;
		if ( isset($id) && !empty($id) ) {
			$user = $this->ion_auth->user()->row();
			
			$this->load->model('target_model');
			$result = json_encode($this->target_model->get_campaign( $id, $user->id ));
		}
		echo $result;
		die();
	}
	
	function delete_campaign () {
		$id = $this->input->post('id');
		$result = false;
		if ( isset($id) && !empty($id) ) {
			$user = $this->ion_auth->user()->row();
			
			$this->load->model('target_model');
			$result = json_encode($this->target_model->delete_campaign( $id, $user->id ));
		}
		echo $result;
		die();
	}
	
}
