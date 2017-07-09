<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Emaildemo extends MX_Controller {
   
    public $data;

    function __construct() {

        parent::__construct();
        $this->load->library('authentication', NULL, 'ion_auth');
        $this->load->library('form_validation');
       
        $this->load->library('template');
        $this->load->database();
        $this->load->model('Admin_model', 'admin'); 
        $this->load->model('Chatroom_model', 'chat'); 

        $this->form_validation->set_error_delimiters($this->config->item('error_start_delimiter', 'ion_auth'), $this->config->item('error_end_delimiter', 'ion_auth'));

        $this->lang->load('auth');
        $this->load->helper(array('url','form','captcha'));
        $this->baseurl = $this->config->config['base_url']; 
        $this->user  = $this->ion_auth->user()->row();
        $this->admin->check_permission();
    }

    function index () {
           
            $this->email_demo();

    }

    function email_demo(){
            $user = (array) $this->user;
            $sess_msg       = $this->session->flashdata('msg');
            $sess_msg_type  = $this->session->flashdata('msg_type');
            if($sess_msg){
                $msg = $sess_msg;
                $msg_type = $sess_msg_type;
            }

            // select demo users
            /*
            $sql = "select id,email,first_name,last_name 
                from 
                    users 
                where 
                    id not in (select user_id from paypal)
                and
                    active = '1' and is_jvzoo = '0' and is_subscribe = '1'";
            */

            $sql = "select u.first_login,u.is_subscribe,u.first_name,u.last_name,u.email,u.id
                from 
                    users as u,
                    users_groups as ug
                where 
                    u.id = ug.user_id
                and
                    u.id not in (select user_id from paypal)
                and
                    u.active = '1'
                and
                    ug.group_id = '2'
                and
                    ug.group_id not in ('1','5')
                and
                    u.is_subscribe = '1'
                and
                    u.is_jvzoo = '0'";

            $check_users = $this->db->query($sql);
            if($check_users->num_rows() == 0){
                $msg = "No Demo users found";
                $msg_type = 'warning';
            }else{
                $show_users = array();
                foreach($check_users->result_array() as $cu){
                    $show_users[] = $cu;
                }
                $o['show_users'] = $show_users;
            }
            $o['count_total'] = $check_users->num_rows();
            $o['emaildemo_header'] = true;
            $o['msg_type'] = $msg_type;
            $o['msg'] = $msg;
            $o['title'] = "Admin - Email Demo";
            $o['page'] = 'demo_users';
            $o['user'] = $user;
            $o['baseurl'] = $this->baseurl;    
            $this->data['o'] = $o;
            $this->load->view('videoadmin/emaildemo', $this->data);  

    }

    function support_message(){
        $user = (array) $this->user;
        $sess_msg       = $this->session->flashdata('msg');
        $sess_msg_type  = $this->session->flashdata('msg_type');
        if($sess_msg){
            $msg = $sess_msg;
            $msg_type = $sess_msg_type;
        }

        $sql = "select u.first_login,u.is_subscribe,u.first_name,u.last_name,u.email,u.id
            from 
                users as u,
                users_groups as ug
            where 
                u.id = ug.user_id
            and
                u.active = '1'
            and
                ug.group_id = '2'
            and
                ug.group_id not in ('1','5')
            and
                u.is_subscribe = '1'
            and
                u.is_jvzoo = '0'";

        $check_users = $this->db->query($sql);
        if($check_users->num_rows() == 0){
            $msg = "No Demo users found";
            $msg_type = 'warning';
        }else{
            $show_users = array();
            foreach($check_users->result_array() as $cu){
                $show_users[] = $cu;
            }
            $o['show_users'] = $show_users;
        }
        $o['count_total'] = $check_users->num_rows();
        $o['emaildemo_header'] = true;

        $o['msg_type'] = $msg_type;
        $o['msg'] = $msg;
        $o['title'] = "Admin - Message support";
        $o['header_text'] = "Send Support message to users";
        $o['page'] = 'send_support';
        $o['user'] = $user;
        $o['baseurl'] = $this->baseurl;    
        $this->data['o'] = $o;
        $this->load->view('videoadmin/msggroupuser', $this->data);  
    }

    function support_message_group(){
        //public group id

        $public_group_id = "11";
        $sql = "select id,user_id,room_name from chatroom where id = '$public_group_id' and status = '1' LIMIT 1";
        $group = $this->db->query($sql);
        if($group->num_rows() == 0){
            $msg_type = 'error';
            $msg = "No group found, please try again";
        }else{
           $sg = $group->row_array();

              $sql = "select u.first_login,u.is_subscribe,u.first_name,u.last_name,u.email,u.id
                from 
                    users as u,
                    users_groups as ug
                where 
                    u.id = ug.user_id
                and
                    u.active = '1'
                and
                    ug.group_id = '2'
                and
                    ug.group_id not in ('1','5')
                and
                    u.is_subscribe = '1'
                and
                    u.is_jvzoo = '0'
                and 
                    u.id in (select user_id from chatusers where room_id = '$public_group_id')";

            $check_users = $this->db->query($sql);
            if($check_users->num_rows() == 0){
                $msg = "No Demo users found";
                $msg_type = 'warning';
            }else{
                $show_users = array();
                foreach($check_users->result_array() as $cu){
                    $show_users[] = $cu;
                }
                $o['show_users'] = $show_users;
            }
            $o['count_total'] = $check_users->num_rows();
            $o['header_text'] = "Message to group: {$sg['room_name']}";
        }

        $o['emaildemo_header'] = true;

        $o['msg_type'] = $msg_type;
        $o['msg'] = $msg;
        $o['title'] = "Admin - Message support";
        $o['page'] = 'send_support_group';
        $o['user'] = $user;
        $o['baseurl'] = $this->baseurl;    
        $this->data['o'] = $o;
        $this->load->view('videoadmin/msggroupuser', $this->data);          
    }
    function send_email(){

       $subject = $this->input->post('subject');
       $message = str_replace("\n", "<br>", $this->input->post('message'));
       $checked = $this->input->post('checked');

            $this->load->library('email');
            $config['protocol'] = 'smtp';
            $config['smtp_host'] = 'localhost';
            $config['smtp_port'] = '25';
            // $config['smtp_host'] = 'box342.bluehost.com';
            // $config['smtp_user'] = 'nathan@nathanhague.com';
            // $config['smtp_pass'] = '$Wolfman1';
            // $config['smtp_port'] = '26';
            $config['charset'] = 'iso-8859-1';
            $config['wordwrap'] = TRUE;
            $config['mailtype'] = 'html';

            $this->email->initialize($config);

            foreach($checked as $ch){
                $subscribe_token = "";
                $msg = "";
                $sql = "select email,is_subscribe,id,subscribe_token from users where id='$ch' and is_subscribe = '1' LIMIT 1";
                $check_em = $this->db->query($sql);
                if($check_em->num_rows() > 0){
                    $get_email = $check_em->row_array();

                    if($get_email['subscribe_token'] == 0){
                        $subscribe_token = md5(rand(00000,9999) . "_" . $get_email['id'] . "_" . time());

                        $data = array('subscribe_token' => $subscribe_token);
                        $this->db->where('id', $get_email['id']);
                        $this->db->update('users', $data);   
                    }else{
                         $subscribe_token = $get_email['subscribe_token'];
                    }

                    $msg = "$message <br><br> <a href='$this->baseurl". "" ."auth/unsubscribe_email/$subscribe_token'>Unsubscribe to Tubemasterpro.com</a>";
                  
                    
                    $this->email->from('support@tubemasterpro.com', 'TubeMasterPro');
                    $this->email->reply_to('australiawow@gmail.com', 'TubeMasterPro');
                    $this->email->to("{$get_email['email']}"); 
                    $this->email->subject("$subject");
                    $this->email->message("$msg");  
                    $this->email->send();
                    
                }
            }
            echo "__emaildemosplit__success";

    }

    function demo_test_send_email(){
       $subject = $this->input->post('subject');
       $message = str_replace("\n", "<br>", $this->input->post('message'));
       $checked = $this->input->post('checked');

            $this->load->library('email');
            $config['protocol'] = 'smtp';
            $config['smtp_host'] = 'localhost';
            $config['smtp_port'] = '25';
            $config['charset'] = 'iso-8859-1';
            $config['wordwrap'] = TRUE;
            $config['mailtype'] = 'html';

            $message = "$message <br><br> <a href='$this->baseurl". "" ."auth/unsubscribe_email/demo123456'>Unsubscribe to Tubemasterpro.com</a>";

            $this->email->initialize($config);
            $this->email->from('support@tubemasterpro.com', 'TubeMasterPro');
            $this->email->reply_to('australiawow@gmail.com', 'TubeMasterPro');
            //$this->email->to("renefandida@gmail.com"); 
            $this->email->to("australiawow@gmail.com"); 
            $this->email->subject("$subject");
            $this->email->message("$message");  
            $this->email->send();
            echo "__emaildemosplit__success"; 
    }

    //send / create message to user
    function send_support_user(){

        $user = (array) $this->user;

        $message = $this->input->post('message');
        $checked = $this->input->post('checked'); 

       $this->db->trans_start();

        foreach($checked as $ch){
            //check details
            $sql = "select id,first_name,last_name from users where id = '$ch' LIMIT 1";
            $check_users = $this->db->query($sql);
            if($check_users->num_rows() > 0){
                $get_u = $check_users->row_array();

                $sql = "select user_id,id from chatroom where user_id = '$ch' and status = '1' LIMIT 1";
                $check_user_addto_chat = $this->db->query($sql);
                if($check_user_addto_chat->num_rows() == 0){

                    $room_name      = "{$get_u['first_name']} {$get_u['last_name']}";
                    $first_msg      = $message;
                    $compose_signature = md5($room_name . "_" . time());

                        $data   = array('user_id' => $get_u['id'], 
                        'room_name'=> "{$get_u['first_name']} {$get_u['last_name']}", 
                        'status'    => 1,
                        'token'=> $compose_signature,
                        'date_update'=> time(), 
                        'date_created'=> time());

                    
                    $this->db->set($data);
                    $this->db->insert('chatroom');
                    $last_inserted_id = $this->db->insert_id();   
            

                    // first convo message
                    $data2 = array( 'user_id' => $user['id'], 
                                'room_id'=> $last_inserted_id, 
                                'user_name' => $user['name'],
                                'user_img'=> $user['profile_pic'], 
                                'text' => $first_msg,
                                'date_sent'=> time()
                            );
                    $this->db->set($data2);
                    $this->db->insert('chatconvo');  #disable as of the moment
                
                    // add user to chatroom
                    $data3 = array( 'user_id' => $ch, 
                            'room_id'=> $last_inserted_id, 
                            'date_added'=> time(),
                            'is_owner' => 0,
                            'notification' => 1
                        );
                    $this->db->set($data3);
                    $this->db->insert('chatusers'); 

                    // add admin to chatroom 
                    $sql = "select user_id from users_groups where group_id = '1'"; // group_id 1 = admin
                    $check_group_admin = $this->db->query($sql);
                    if($check_group_admin->num_rows() > 0){
                        $group_admin = $check_group_admin->result_array();
                        for($i=0;$i<count($group_admin); $i++)
                        {
                                $sql = "select user_id from chatusers where room_id = '$last_inserted_id' and user_id = '{$group_admin[$i]['user_id']}' LIMIT 1";
                                $check_dup = $this->db->query($sql);
                                if($check_dup->num_rows() == 0){
                                $data = array(  'user_id' => $group_admin[$i]['user_id'], 
                                                'room_id'=> $last_inserted_id,  
                                                'date_added'=> time(),
                                                'is_owner' => 1
                                            );
                                    $this->db->set($data);
                                    $this->db->insert('chatusers');
                                }
                        }  
                    }    

                }else{
                     $check_convo = $check_user_addto_chat->row_array();
                    // first convo message
                    $data2 = array( 'user_id' => $user['id'], 
                                'room_id'=> $check_convo['id'], 
                                'user_name' => "Admin",//$user['name'],
                                'user_img'=> $user['profile_pic'], 
                                'text' => $message,
                                'date_sent'=> time()
                            );
                    $this->db->set($data2);
                    $this->db->insert('chatconvo');  #disable as of the moment
                }
            }

        }
         $this->db->trans_complete();
          echo "__emaildemosplit__success";
        
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

