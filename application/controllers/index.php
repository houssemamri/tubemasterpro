<?php defined('BASEPATH') OR exit('No direct script access allowed');
session_start();
error_reporting(0);

class Index extends MX_Controller {


    function __construct()
    {
        parent::__construct();
        $this->load->library('authentication', NULL, 'ion_auth');
        $this->load->library('form_validation');
        $this->load->helper(array('url','cookie'));
         $this->load->library('template');

        $this->load->database();

        $this->form_validation->set_error_delimiters($this->config->item('error_start_delimiter', 'ion_auth'), $this->config->item('error_end_delimiter', 'ion_auth'));

        $this->lang->load('auth');
        $this->load->helper('language');

        $this->baseurl = $this->config->config['base_url']; 

    }

    function index(){

    	if ( $this->session->flashdata('message') ) {
            $this->session->set_flashdata('message', $this->session->flashdata('message') );
    	}

        $affiliate_id   = $this->uri->segment(1);
        $signup_token  = md5(time() . "_" . rand(00000,99999));
        $_SESSION['signup_token'] = $signup_token;


            //check if affiliate is valid
            $exp_aff = trim(str_replace("aff-", "", $affiliate_id));

            $affiliate_name = $_COOKIE['aff_affiliate'];

            if($affiliate_name != ""){
                $sql = "select user_pixel from users where is_aff = '$affiliate_name' and aff_status = 'approved' and is_aff_tos = '1' LIMIT 1";
                $check_pixel = $this->db->query($sql);
                if($check_pixel->num_rows() > 0){
                        $show_pix = $check_pixel->row_array();
                        $o['user_pixel'] = $this->pixel2orig($show_pix['user_pixel']);
                        $o['user_pixel_signup'] = true;
                    
                        if($exp_aff == $affiliate_name and $affiliate_name != ""){
                         $o['auto_popup_signup'] = true;
                        }

                }
            }
          
        if($_SESSION['isFirstTime'] == 1){
            $_SESSION['isFirstTime'] = 0;
            $o['isFirstTimeTable'] = true;
        }
                    
        $o['signup_token'] = $signup_token;
        $o['baseurl'] = $this->baseurl;     
        $data['o'] = $o;
        if (!$this->ion_auth->logged_in()) {
	        $this->load->view('index', $data); 
        }
        else {
	        redirect('dashboard', 'refresh');
        }
    }

     function pixel2orig($word){
        $word = str_replace('[js]','<script>', $word);
        $word = str_replace('[/js]','</script>', $word);
        $word = str_replace('[/ns]','<noscript>', $word);
        $word = str_replace('[/ns]','</noscript>', $word);
        $word = stripslashes($word);
        return $word;
    }
    
    function pricing () {
	    $this->load->view('pricing'); 
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