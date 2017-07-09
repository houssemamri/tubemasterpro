<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Signup extends MY_Controller {

    public $data;

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

        $this->template->set_title('Signup');
        $this->template->add_css('bfh/css/bootstrap-formhelpers.min.css');
        $this->template->add_js('bfh/bootstrap-formhelpers.min.js');
        $this->template->add_js('jquery.validify.js');
        $this->template->add_js('modules/warroom.js');
    }

    //redirect if needed, otherwise display the user list
    function index()
    {
        $tables = $this->config->item('tables','ion_auth');
                //validate form input
        $this->form_validation->set_rules('first_name', $this->lang->line('create_user_validation_fname_label'), 'required|xss_clean');
        $this->form_validation->set_rules('last_name', $this->lang->line('create_user_validation_lname_label'), 'required|xss_clean');
        $this->form_validation->set_rules('email', $this->lang->line('create_user_validation_email_label'), 'required|valid_email|is_unique['.$tables['users'].'.email]');
        //$this->form_validation->set_rules('phone', $this->lang->line('create_user_validation_phone_label'), 'required|xss_clean');
        //$this->form_validation->set_rules('company', $this->lang->line('create_user_validation_company_label'), 'required|xss_clean');
        $this->form_validation->set_rules('password', $this->lang->line('create_user_validation_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|max_length[' . $this->config->item('max_password_length', 'ion_auth') . ']|matches[password_confirm]');
        $this->form_validation->set_rules('password_confirm', $this->lang->line('create_user_validation_password_confirm_label'), 'required');

        $affiliate_name = $_COOKIE['aff_affiliate'];

        $sql = "select user_pixel from users where is_aff = '$affiliate_name' LIMIT 1";
        $check_pixel = $this->db->query($sql);
        if($check_pixel->num_rows() > 0){
           $show_pix = $check_pixel->row_array();
            $is_affiliate_signup = 1;
        }
        
        if ($this->form_validation->run() == true)
        {
            $username = strtolower($this->input->post('first_name')) . ' ' . strtolower($this->input->post('last_name'));
            $email    = strtolower($this->input->post('email'));
            $password = $this->input->post('password');

            if($is_affiliate_signup != 1){
                $is_affiliate_signup = 0;
            }
            $additional_data = array(
                'first_name' => $this->input->post('first_name'),
                'last_name'  => $this->input->post('last_name'),
                'su_is_aff'  => $is_affiliate_signup,
            );
            $group = array('3');
        }
        if ($this->form_validation->run() == true && $this->ion_auth->register($username, $password, $email, $additional_data, $group))
        {
            //insert affiliate if has and clear cookie...
            /* check affiliate */
           

            if($affiliate_name != ""){
                $get_user_aff_id    = $this->get_aff_cookie($affiliate_name);
                $sql = "select id from users where email = '$email' LIMIT 1";

                $check_new_user = $this->db->query($sql);
                if($check_new_user->num_rows() > 0){
                    $new_u = $check_new_user->row_array();
 
                    if($get_user_aff_id != ""){

                        $data = array(
                           'user_id_aff' => $new_u['id'],
                           'user_id' => $get_user_aff_id ,
                           'date_added' => time()
                        );
                        $this->db->insert('affiliates', $data);
                    } 
                    //setcookie("aff_affiliate", "", time() - 3600); 
                }
                
                 
            }            
               
            //check to see if we are creating the user
            //redirect them back to the admin page

            //echo $this->ion_auth->messages();die();
            $this->session->set_flashdata('message', '<p>Activation Email Sent</p>');
            redirect("auth/login", 'refresh');
        }
        else
        {

            //display the create user form
            //set the flash data error message if there is one
            $this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message') ) );

                    $this->data['first_name'] = array(
                        'name'  => 'first_name',
                        'id'    => 'first_name',
                        'type'  => 'text',
                        'class' => 'validify_required validify_alpha',
                        'value' => $this->form_validation->set_value('first_name'),
                    );
                    $this->data['last_name'] = array(
                        'name'  => 'last_name',
                        'id'    => 'last_name',
                        'type'  => 'text',
                        'class' => 'validify_required validify_alpha',
                        'value' => $this->form_validation->set_value('last_name'),
                    );
                    $this->data['email'] = array(
                        'name'  => 'email',
                        'id'    => 'email',
                        'type'  => 'text',
                        'class' => 'validify_required validify_email_ajax',
                        'value' => $this->form_validation->set_value('email'),
                    );
                    $this->data['password'] = array(
                        'name'  => 'password',
                        'id'    => 'password',
                        'type'  => 'password',
                        'class' => 'validify_required validify_password',
                        'value' => $this->form_validation->set_value('password'),
                    );
                    $this->data['password_confirm'] = array(
                        'name'  => 'password_confirm',
                        'id'    => 'password_confirm',
                        'type'  => 'password',
                        'class' => 'validify_required validify_password_confirm',
                        'value' => $this->form_validation->set_value('password_confirm'),
                    );
            //$this->load->view('signup/create_user', $this->data);
            $this->_render_page('signup/create_user', $this->data);


        }
    }

    function aff(){
         $affiliate_id   = $this->uri->segment(3);

         /* check affiliate id */
         $sql = "select id from users where is_aff = '$affiliate_id' LIMIT 1";
         $check_aff = $this->db->query($sql);
         if($check_aff->num_rows() == 0){
           redirect('signup', 'refresh');
         }else{
            //check cookie exist
            $cookie_name = "aff_affiliate";
            $cookie_value = "$affiliate_id";
            setcookie($cookie_name, $cookie_value, time() + (86400 * 7), "/"); // 86400 = 1 day // 2592000 1 day

            redirect('aff-' . $affiliate_id, 'refresh');
            
         }
    }

    function get_aff_cookie($affiliate_id)
    {
         /* check affiliate id */
         $sql = "select id from users where is_aff = '$affiliate_id' LIMIT 1";
         $check_aff = $this->db->query($sql);
         if($check_aff->num_rows() == 0){
           return "";
         }else{
            $show_aff_id = $check_aff->row_array();
            return $show_aff_id['id'];      
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