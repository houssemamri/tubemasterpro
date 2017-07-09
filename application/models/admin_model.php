<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin_model extends CI_Model {
	
	public function __construct(){ 

        parent::__construct();
        $this->load->library('authentication', NULL, 'ion_auth');
        $this->load->library('form_validation');
        $this->load->helper('url','form');
        $this->load->database();

        $this->baseurl = $this->config->config['base_url']; 
        $this->group_name = array('video_admin','admin');
        $this->user  = $this->ion_auth->user()->row();
    }
    
    public function check_permission(){
        if (!$this->ion_auth->in_group($this->group_name) or !$this->ion_auth->logged_in() )
        {
            return show_error('Permission denied, you are not allowed to view this page or try to login again');
        }  
    }

    public function cleanup($word)
    {
        $word = trim($word);
        $word = strip_tags($word, " <STRONG> <EM> <U> <BR> <n> \n ");
        $word = addslashes($word);
        $word = str_replace(array("|","~>","'")," ",$word); 
        return $word;
    }  
}