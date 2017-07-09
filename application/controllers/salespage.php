<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Salespage extends MY_Controller {

    public $data;

    function __construct()
    {
        parent::__construct();
        $this->load->library('authentication', NULL, 'ion_auth');
        $this->load->library('form_validation');
        $this->load->helper(array('url','cookie'));
         $this->load->library('template');
         $this->load->helper('string');
        $this->load->database();

        $this->form_validation->set_error_delimiters($this->config->item('error_start_delimiter', 'ion_auth'), $this->config->item('error_end_delimiter', 'ion_auth'));
		 $this->baseurl = $this->config->config['base_url']; 

    }

    function index(){
        $o['showsalespage_table'] = true;
        $o['signup_token'] = $signup_token;
        $o['baseurl'] = $this->baseurl;     
        $data['o'] = $o;
        $this->load->view('salespage', $data); 
    }

    function thankyou(){
        $o['showthankyou_table'] = true;
        $o['signup_token'] = $signup_token;
        $o['baseurl'] = $this->baseurl;     
        $data['o'] = $o;
        $this->load->view('salespage', $data); 
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
?>