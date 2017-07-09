<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Auth_ajax extends Ajax_Controller {

    /**
     * Launch dialog that contains a specific Ion auth page content
     *
     * This function is only be used for a quick example
     * about displaying page content (without master page) inside a dialog.
     * It also helps to keep the Ion auth's controller structure unchanged
     * so you can freely modify it yourself.
     */
    function ion_auth_dialog($page)
    {
        if (in_array($page, array(
            'login',
            'change_password',
            'forgot_password'
        )))
        {
            $this->response->dialog(array(
                'body' => Modules::run('auth/' . $page)
            ));

            $this->response->script('$("#signup_form").validify();');
        }
        $this->response->send();
    }

    function forgot_password() {
        $this->load->database();
        $this->load->library('authentication', NULL, 'ion_auth');
        $this->lang->load('auth');
        // get identity from username or email
        if ( $this->config->item('identity', 'ion_auth') == 'username' ){
            $identity = $this->ion_auth->where('username', strtolower($this->input->get_post('email')))->users()->row();
        }
        else
        {
            $identity = $this->ion_auth->where('email', strtolower($this->input->get_post('email')))->users()->row();
        }
        // echo "<pre>";
        // print_r($identity);
        // echo "</pre>";die();

        if(empty($identity)) {
            $this->ion_auth->set_message('forgot_password_email_not_found');
            //$this->session->set_flashdata('message', $this->ion_auth->messages());
            $this->response->script('$("#infoMessage").html("'.$this->ion_auth->messages().'");$("#infoMessage").addClass("alert alert-info")');
        }

        //run the forgotten password method to email an activation code to the user
        $forgotten = $this->ion_auth->forgotten_password($identity->{$this->config->item('identity', 'ion_auth')});
        //$this->response->script('$("#infoMessage").append("forgotter == '.$forgotten.'");$("#infoMessage").addClass("alert alert-info")');
        if ($forgotten)
        {
            //if there were no errors
            //$this->session->set_flashdata('message', $this->ion_auth->messages());
            
            $this->response->script('$("#show_success").modal("show");');
        }
        else
        {
            //$this->session->set_flashdata('message', $this->ion_auth->errors());
            $errors = $this->ion_auth->errors();
            
            $this->response->script('$("#infoMessage").html("'.$errors.'");$("#infoMessage").addClass("alert alert-info")');
        }
        $this->response->send();
    }
}

/* End of file auth_ajax.php */
/* Location: ./application/modules/auth/controllers/auth_ajax.php */