<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Logs_model extends CI_Model {
	
	public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->model('Chatroom_model', 'chat'); 

        $this->user  = $this->ion_auth->user()->row();
    }
    
    public function insert_logs($log_type, $log_desc) {

        $user = $this->user;

        if($user->id){
          $data = array(
             'user_id' => $user->id ,
             'log_type' => $this->chat->cleanup($log_type),
             'log_desc' => $this->chat->cleanup($log_desc),
             'date_added' => time()
          );
          $this->db->insert('users_logs', $data); 
        }
    }
}