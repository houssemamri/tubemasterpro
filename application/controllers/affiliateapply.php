<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Affiliateapply extends MY_Controller {

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

        $this->template->set_title('Affiliate Signup');
        $this->template->add_css('bfh/css/bootstrap-formhelpers.min.css');
        $this->template->add_css('intl-tel-input/intlTelInput.css');
        $this->template->add_js('bfh/bootstrap-formhelpers.min.js');
        $this->template->add_js('intl-tel-input/intlTelInput.min.js');
        $this->template->add_js('jquery.validify.js');
        $this->template->add_js('jquery.countdown.min.js');
        $this->template->add_js('modules/affiliate.js');    
        $this->template->add_js('affiliate_apply.js');    
    }

    
    function index(){

                //display the create user form
                    //set the flash data error message if there is one
                    $this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
                    
                    $this->data['firstname'] = array(
                        'name'  => 'firstname',
                        'id'    => 'firstname',
                        'type'  => 'text',
                        'class' => 'validify_required validify_alpha',
                        'value' => $this->form_validation->set_value('firstname'),
                    );
                    $this->data['lastname'] = array(
                        'name'  => 'lastname',
                        'id'    => 'lastname',
                        'type'  => 'text',
                        'class' => 'validify_required validify_alpha',
                        'value' => $this->form_validation->set_value('lastname'),
                    );

                    $this->data['email'] = array(
                        'name'  => 'email',
                        'id'    => 'email',
                        'type'  => 'text',
                        'class' => 'validify_required validify_email',
                        'placeholder' => '',
                        'value' => $this->form_validation->set_value('email'),
                    );  

                    $this->data['paypal_email'] = array(
                        'name'  => 'paypal_email',
                        'id'    => 'paypal_email',
                        'type'  => 'text',
                        'class' => 'validify_required validify_email',
                        'placeholder' => 'Paypal Email',
                        'value' => $this->form_validation->set_value('email'),
                    );
                    $this->data['watsapp_id'] = array(
                        'name'  => 'whatsApp',
                        'id'    => 'whatsApp',
                        'type'  => 'text',
                        'class' => 'validify_numeric',
                        'value' => $this->form_validation->set_value('whatsApp'),
                    );  

                    $this->data['skype_id'] = array(
                        'name'  => 'skype_id',
                        'id'    => 'skype_id',
                        'type'  => 'text',
                        'class' => 'validify_required validify_alphanumeric',
                        'value' => $this->form_validation->set_value('skype_id'),
                    );
                    $this->data['mobile'] = array(
                        'name'  => 'mobile',
                        'id'    => 'mobile',
                        'type'  => 'text',
                        'class' => 'validify_required',
                        'value' => $this->form_validation->set_value('mobile'),
                    );
                                                                       
                    $this->data['email'] = array(
                        'name'  => 'email',
                        'id'    => 'email',
                        'type'  => 'text',
                        'class' => 'validify_required validify_email',
                        'value' => $this->form_validation->set_value('email'),
                    );
                    $this->data['website'] = array(
                        'name'  => 'website',
                        'id'    => 'website',
                        'type'  => 'text',
                        'class' => 'validify_required validify_website_complete',
                        'data-toggle'   => 'popover',
                        'data-placement'=> 'top',
                        'data-trigger'  => 'focus',
                        'data-content'  => 'http:// or https://www.yourdomainname.com',
                        'value' => $this->form_validation->set_value('website'),
                    );

                    $this->data['promote_note'] = array(
                        'name'  => 'promote_note',
                        'id'    => 'promote_note',
                        'type'  => 'text',
                        'class' => 'validify_required',
                        'value' => $this->form_validation->set_value('promote_note'),
                    );  
                    
                    $this->data['company'] = array(
                        'name'  => 'company',
                        'id'    => 'company',
                        'type'  => 'text',
                        'class' => 'validify_required validify_alpha',
                        'value' => $this->form_validation->set_value('company'),
                    );

                    $this->data['twitter'] = array(
                        'name'  => 'twitter',
                        'id'    => 'twitter',
                        'type'  => 'text',
                        'class' => 'validify_required validify_twitter',
                        'data-toggle'   => 'popover',
                        'data-placement'=> 'top',
                        'data-trigger'  => 'focus',
                        'data-content'  => 'http:// or https://www.twitter.com/youridhere',
                        'value' => $this->form_validation->set_value('twitter'),
                    );
                    $this->data['fb'] = array(
                        'name'  => 'fb',
                        'id'    => 'fb',
                        'type'  => 'text',
                        'class' => 'validify_required validify_facebook',
                        'data-toggle'   => 'popover',
                        'data-placement'=> 'top',
                        'data-trigger'  => 'focus',
                        'data-content'  => 'http:// or https://www.facebook.com/youridhere',
                        'value' => $this->form_validation->set_value('fb'),
                    );
                    $this->data['ln'] = array(
                        'name'  => 'ln',
                        'id'    => 'ln',
                        'type'  => 'text',
                        'class' => 'validify_required',
                        'data-toggle'   => 'popover',
                        'data-placement'=> 'top',
                        'data-trigger'  => 'focus',
                        'data-content'  => 'Your LinkedIn Profile URL',
                        'value' => $this->form_validation->set_value('ln'),
                    );

            $this->_render_page('affiliateapply', $this->data);
    }

    function signup(){

        $firstname      = $this->input->post('firstname');
        $lastname       = $this->input->post('lastname');
        $email          = $this->input->post('email');
        $country        = $this->input->post('country');
        $mobile         = $this->input->post('mobile');
        $website        = $this->input->post('website');
        $skype_id       = $this->input->post('skype_id');
        $promote_note   = str_replace("\n", "<br>", $this->input->post('promote_note'));

         $paypal_email       = $this->input->post('paypal_email');
         $company           = $this->input->post('company');
         $whatsApp          = $this->input->post('whatsApp');
         $twitter           = $this->input->post('twitter');
         $fb                = $this->input->post('fb');
         $ln                = $this->input->post('ln');

        $this->load->library('email');
        $config['protocol'] = 'smtp';
        $config['smtp_host'] = 'localhost';
        $config['smtp_port'] = '25';
        $config['charset'] = 'iso-8859-1';
        $config['wordwrap'] = TRUE;
        $config['mailtype'] = 'html';
        $this->email->initialize($config);        

        $message = "Name: $firstname $lastname <br> 
        Email: $email <br>
        Paypal email: $paypal_email <br>
        Country: $country <br>
        Mobile: $mobile <br>
        Website: $website <br>
        Skype ID: $skype_id <br> <br>
        Social links: <br><br>
        Twitter: $twitter <br>
        Facebook: $fb <br>
        LinkedIn: $ln <br><br>
        Note: <br>
        $promote_note";
        $this->email->from('support@tubemasterpro.com', 'TubeMasterPro');
        //$this->email->reply_to('australiawow@gmail.com', 'TubeMasterPro');
        $this->email->to("australiawow@gmail.com"); 
        //$this->email->to("renefandida@gmail.com"); 
        $this->email->subject("Affiliate Application");
        $this->email->message("$message");  
        if($this->email->send()){
            echo "success";
        }else{
            echo "failed";
        }

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