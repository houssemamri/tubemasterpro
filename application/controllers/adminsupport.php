<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Adminsupport extends MX_Controller {

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
         $this->load->model('Chatroom_model', 'chat'); 

        $this->form_validation->set_error_delimiters($this->config->item('error_start_delimiter', 'ion_auth'), $this->config->item('error_end_delimiter', 'ion_auth'));

        $this->lang->load('auth');
        $this->load->helper('language');
        $this->load->helper('captcha');
        
        $this->baseurl = $this->config->config['base_url']; 
        $this->group_name = array('video_admin','admin');
        $this->user  = $this->ion_auth->user()->row();



        $this->smtp_host = "box342.bluehost.com";
        $this->smtp_user = "nathan@nathanhague.com";
        $this->smtp_pass = "$Wolfman1";  
    }

    function index(){
        $user = (array) $this->user;
        $this->check_permission();

        $o['support_admin_header'] = true;
        $o['msg_type'] = $msg_type;
        $o['msg'] = $msg;
        $o['title'] = "Support Admin";
        $o['page'] = 'support';
        $o['user'] = $user;
        $o['baseurl'] = $this->baseurl;    
        $this->data['o'] = $o;
        $this->load->view('videoadmin/adminsupport', $this->data);  
    }

    function check_permission(){
        if (!$this->ion_auth->in_group($this->group_name) or !$this->ion_auth->logged_in() )
        {
            return show_error('Permission denied, you are not allowed to view this page or try to login again');
        }  
    }

    function cleanup($word)
    {
        $word = trim($word);
        $word = strip_tags($word, " <STRONG> <EM> <U> <BR> <n> \n ");
        $word = addslashes($word);
        $word = str_replace(array("|","~>","'")," ",$word); 
        return $word;
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