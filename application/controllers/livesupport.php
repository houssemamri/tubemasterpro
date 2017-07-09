<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Livesupport extends MY_Controller {

    public $data;

    function __construct()
    {
        parent::__construct();
        $this->load->library('authentication', NULL, 'ion_auth');
        $this->load->library('form_validation');
        $this->load->helper(array('url','cookie'));
        $this->load->library('template');
        $this->load->model('Chatroom_model', 'chat'); 
        $this->load->model('Logs_model', 'logs'); 
        $this->load->database();

        $this->form_validation->set_error_delimiters($this->config->item('error_start_delimiter', 'ion_auth'), $this->config->item('error_end_delimiter', 'ion_auth'));

        $this->lang->load('auth');
        $this->load->helper('language');

        $this->template->set_title('Support');
         $this->template->add_css('chat/style.css');
        $this->template->add_css('chat.css');

/*
        $this->template->add_css('bfh/css/bootstrap-formhelpers.min.css');
        $this->template->add_js('bfh/bootstrap-formhelpers.min.js');
        $this->template->add_js('jquery.validify.js');
        $this->template->add_js('modules/warroom.js');
*/
        $this->baseurl = $this->config->config['base_url']; 
        $this->user  = $this->ion_auth->user()->row();
        $this->check_permission();

    }

    function index(){

         $group_chat_id         = "11";
         $affiliate_chat_id     = "16"; //16 = live; 17=local
        //check if user already have a chatbox
        $user = (array) $this->user;



       /* create public chat */

        $sql = "select id,user_id,room_name from chatroom where id = '$group_chat_id' LIMIT 1";

        $check_gc = $this->db->query($sql);
        if($check_gc->num_rows() == 0){
            return show_error('No Group chat found, please try again');
        }else{

            $chatroom = $check_gc->row_array();

            /* check if user is already added */
            $sql = "select room_id,user_id from chatusers where room_id = '$group_chat_id' and user_id = '{$user['id']}' and cstatus = '1' LIMIT 1";
            $check_if_ex = $this->db->query($sql);
            if($check_if_ex->num_rows() == 0){

                  $data = array(  'user_id' => $user['id'], 
                        'room_id'=> $group_chat_id,  
                        'date_added'=> time(),
                        'notification' => 0,
                        'is_owner' => 0
                    );
                    $this->db->set($data);
                    $this->db->insert('chatusers');        
            }
 
        }   

       /* create add if affiliate chat*/    
        $sql = "select id,user_id,room_name from chatroom where id = '$affiliate_chat_id' LIMIT 1";

        $check_gc = $this->db->query($sql);
        if($check_gc->num_rows() == 0){
            return show_error('No Group chat found, please try again');
        }else{

            $chatroom = $check_gc->row_array();

            //check if user is affiliate or not
            $sql = "select id,aff_status from users where aff_status != '' and is_aff != '' and id = '{$user['id']}' LIMIT 1";

            $check_is_aff = $this->db->query($sql);
            if($check_is_aff->row_array() > 0){
                $cas = $check_is_aff->row_array();

                if($cas['aff_status'] == 'approved'){
                    /* check if user is already added */
                    $sql = "select room_id,user_id from chatusers where room_id = '$affiliate_chat_id' and user_id = '{$user['id']}' and cstatus = '1' LIMIT 1";
                    $check_if_ex = $this->db->query($sql);
                    if($check_if_ex->num_rows() == 0){

                          $data = array( 'user_id' => $user['id'], 
                                'room_id'=> $affiliate_chat_id,  
                                'date_added'=> time(),
                                'notification' => 0,
                                'is_owner' => 0
                            );
                            $this->db->set($data);
                            $this->db->insert('chatusers');        
                    }
                }

                if($cas['aff_status'] == 'rejected'){
                    $sql = $this->db->query("delete from chatusers where room_id = '$affiliate_chat_id' and user_id = '{$user['id']}'");
                    $sql = $this->db->query("delete from chatconvo where room_id = '$affiliate_chat_id' and user_id = '{$user['id']}'");
                }

            }

            /* ADD ADMIN FOR BOTH AFFILIATE AND PUBLIC */
            $sql = "select user_id from users_groups where group_id = '1'"; // group_id 1 = admin
            $check_group_admin = $this->db->query($sql);
            if($check_group_admin->num_rows() > 0){
                //$group_admin = $check_group_admin->result_array();

                foreach($check_group_admin->result_array() as $ga)
                {
                    //public chat 
                    $sql = "select user_id from chatusers where 
                            user_id = '{$ga['user_id']}' 
                        and 
                            room_id = '$group_chat_id' and cstatus = '1' LIMIT 1";
                    $check_admin_add = $this->db->query($sql);
                    if($check_admin_add->num_rows() == 0){

                        $data = array(  'user_id' => $ga['user_id'], 
                                        'room_id'=> $group_chat_id,  
                                        'date_added'=> time(),
                                        'notification' => 1,
                                        'is_owner' => 0
                                    );
                        $this->db->set($data);
                        $this->db->insert('chatusers');
                    }
                    //affiliate
                    $sql2 = "select user_id from chatusers where 
                            user_id = '{$ga['user_id']}' 
                        and 
                            room_id = '$affiliate_chat_id' and cstatus = '1' LIMIT 1";

                    $check_admin_add2 = $this->db->query($sql2);
                    if($check_admin_add2->num_rows() == 0){

                        $data2 = array(  'user_id' => $ga['user_id'], 
                                        'room_id'=> $affiliate_chat_id,  
                                        'date_added'=> time(),
                                        'notification' => 1,
                                        'is_owner' => 0
                                    );
                        $this->db->set($data2);
                        $this->db->insert('chatusers');
                    }
                }  
            }   
        }   

        /* CREATE SUPPORT ROOM*/
        $sql = "select user_id,id from chatroom where user_id = '{$user['id']}' and status = '1' LIMIT 1";

        $check_user_addto_chat = $this->db->query($sql);
        if($check_user_addto_chat->num_rows() == 0){

            $room_name      = $this->chat->cleanup($this->input->post('room_name'));
            $first_msg      = $this->chat->cleanup($this->input->post('first_msg'));
            $compose_signature = md5($room_name . "_" . time());

            $this->db->trans_start();
            //2014-12-19T14:30:04+08:00
            
            $data   = array('user_id' => $user['id'], 
                    'room_name'=> "{$user['first_name']} {$user['last_name']}", 
                    'status'    => 1,
                    'token'=> $compose_signature,
                    'date_update'=> time(), 
                    'date_created'=> time());


            $this->db->set($data);
            $this->db->insert('chatroom');
            $last_inserted_id = $this->db->insert_id();

            // first convo message
            //$get_name = $this->admin->check_user_data($user_data['id']);
            //$date_added = date("c",time());
            if($first_msg != ""){
                $data2 = array( 'user_id' => $user['id'], 
                                'room_id'=> $last_inserted_id, 
                                'user_name' => $user_data['name'],
                                'user_img'=> $user['profile_pic'], 
                                'text' => $first_msg,
                                'date_sent'=> time()
                            );
                $this->db->set($data2);
                //$this->db->insert('chatconvo');  #disable as of the moment
            }
            // add owner to chatroom
            $data2 = array( 'user_id' => $user['id'], 
                            'room_id'=> $last_inserted_id, 
                            'date_added'=> time(),
                            'is_owner' => 1
                        );
            $this->db->set($data2);
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
                                        'notification' => 1,
                                        'is_owner' => 0
                                    );
                            $this->db->set($data);
                            $this->db->insert('chatusers');
                        }
                }  
            }

        /* END CREATE CHATBOX*/


            $this->db->trans_complete();


            $msg        = "Welcome to chat support system";
            $msg_type   = 'warning'; 
            $path       = "livesupport/open_chatbox";
            $this->redirect_link($path,$msg,$msg_type); 

        }else{

            redirect('livesupport/open_chatbox', 'refresh');
        }

         

    }

    function open_chatbox(){
        $user = (array) $this->user;

        $sess_msg       = $this->session->flashdata('msg');
        $sess_msg_type  = $this->session->flashdata('msg_type');
        if($sess_msg){
            $msg = $sess_msg;
            $msg_type = $sess_msg_type;
        }
        //check if chatroom already exist
        $sql = "select user_id,id from chatroom where user_id = '{$user['id']}' and status = '1' LIMIT 1";
        $check_user = $this->db->query($sql);
        if($check_user->num_rows() == 0){
             redirect('livesupport', 'refresh');
        }
        else{
           


            $chatroom = $check_user->row_array();
            $o['show_live_support_user_table'] = true;
            $o['cr'] = $chatroom;
            $o['msg_type'] = $msg_type;
            $o['msg'] = $msg;
            $o['page'] = 'support';
            $o['chat_title_head'] = "Live Chat with our support"; 
            $o['user'] = $user;
            $o['baseurl'] = $this->baseurl;    
            $this->data['o'] = $o;
            //$this->load->view('chat/livesupport', $this->data);       
            $this->_render_page('chat/livesupport', $this->data);
            
        }
    }

    function groupchat(){
        $user = (array) $this->user;
        $group_chat_id = "11";
        $sess_msg       = $this->session->flashdata('msg');
        $sess_msg_type  = $this->session->flashdata('msg_type');
        if($sess_msg){
            $msg = $sess_msg;
            $msg_type = $sess_msg_type;
        }  

        //check chatbox if exist
        $sql = "select id,user_id,room_name from chatroom where id = '$group_chat_id' LIMIT 1";
        $check_gc = $this->db->query($sql);
        if($check_gc->num_rows() == 0){
            return show_error('No Group chat found, please try again');
        }else{

            $chatroom = $check_gc->row_array();

            /* check if user is already added */
            $sql = "select room_id,user_id from chatusers where room_id = '$group_chat_id' and user_id = '{$user['id']}' and cstatus = '1' LIMIT 1";
            $check_if_ex = $this->db->query($sql);
            if($check_if_ex->num_rows() == 0){

                  $data = array(  'user_id' => $user['id'], 
                        'room_id'=> $group_chat_id,  
                        'date_added'=> time(),
                        'notification' => 0,
                        'is_owner' => 0
                    );
                    $this->db->set($data);
                    $this->db->insert('chatusers');      
                $msg        = "Welcome to TubeTargetPro Group Chat";
                $msg_type   = 'success'; 
                $path       = "livesupport/groupchat";
              //  $this->redirect_link($path,$msg,$msg_type);   
            }


            $sql = "select user_id from users_groups where group_id = '1'"; // group_id 1 = admin
            $check_group_admin = $this->db->query($sql);
            if($check_group_admin->num_rows() > 0){
                $group_admin = $check_group_admin->result_array();
                for($i=0;$i<count($group_admin); $i++)
                {
                    $sql = "select user_id from chatusers where 
                            user_id = '{$group_admin[$i]['user_id']}' 
                        and 
                            room_id = '$group_chat_id' and cstatus = '1' LIMIT 1";
                    $check_admin_add = $this->db->query($sql);
                    if($check_admin_add->num_rows() == 0){

                        $data = array(  'user_id' => $group_admin[$i]['user_id'], 
                                        'room_id'=> $group_chat_id,  
                                        'date_added'=> time(),
                                        'notification' => 1,
                                        'is_owner' => 0
                                    );
                        $this->db->set($data);
                        $this->db->insert('chatusers');
                    }
                }  
            }
/*
            $o['show_live_support_user_table'] = true;
            $o['cr'] = $chatroom;
            $o['msg_type'] = $msg_type;
            $o['msg'] = $msg;
            $o['page'] = 'support';
            $o['chat_title_head'] = "TubeTargetPro Group Chat"; //Live Chat with our support
            $o['user'] = $user;
            $o['baseurl'] = $this->baseurl;    
            $this->data['o'] = $o;
            //$this->load->view('chat/livesupport', $this->data);       
            $this->_render_page('chat/livesupport', $this->data);
            */
        }
    }


    public function show_chat_content(){

        $user = (array) $this->user;
        $room_id    = $this->input->post('room_id');

        if($this->ion_auth->in_group("admin")){
             $o['is_admin'] = 1;
        }

        //check room id
        $sql = "select cr.id,cr.room_name, cu.chat_id,cr.user_id 
                from 
                    chatroom as cr,
                    chatusers as cu 
                where 
                    cr.id = cu.room_id and cr.status = '1' and cr.id = '$room_id' and cu.user_id = '{$user['id']}'";

        $check_room = $this->db->query($sql);
        if($check_room->num_rows() == 0){
            echo "<div class=\"alert alert-warning\" role=\"alert\">Room not found, please try again!</div>";
        }else{

            $room_name = $check_room->row_array();
            if($room_name['user_id'] == $user['id']){
                $room_name['is_room_owner'] = 1;
            }
            //$sql = "select chat_id,notification from chatusers where room_id = '$room_id' and user_id = '{$user_data['id']}' and cstatus = '1'";

            $sql = "select u.first_name,cu.user_id 
                    from 
                        users as u, chatusers as cu
                    where
                        u.id = cu.user_id
                    and
                        cu.room_id = '$room_id'
                    and
                        cu.user_id != '{$user['id']}'";
            $check_names = $this->db->query($sql);
            if($check_names->num_rows() == 0){
                $room_name['room_name_users'] = "";
            }else{
                $total_users = $check_names->num_rows();
                $ch_name = $check_names->result_array();
                $split_name = explode(" ", $ch_name[0]['first_name']);

                if($total_users == 1){
                        $room_name['room_name_users'] = "You and " . $split_name[0];
                }else{
                    $new_total_users = $total_users - 1;
                    $room_name['room_name_users'] = $split_name[0] . " + " . $new_total_users . " others";
                    
                }
                
            }
            $o['user'] = $user;
            $o['rn'] = $room_name;
            $o['baseurl'] = $this->baseurl;
            $this->data['o'] = $o;        
           // $this->smarty->view( 'chat/room_name.tpl',$data);
            $this->_render_page('chat/room_name', $this->data);
        }
    }


    public function ret_chat(){

        $user_data = (array) $this->user;

        $room_id    = $this->input->post('room_id');
        $this->db->trans_start();

        //check user if admin or not
        /*
        $sql = "select user_id from users_groups where user_id = '{$user_data['user_id']}' and group_id ='1'";
        $check_admin = $this->db->query($sql);
        if($check_admin->num_rows() > 0){
            
        }
        */
        if($this->ion_auth->in_group("admin")){
         $o['is_admin'] = 1;
        }
        //$o['is_trainer'] = 0;

        /* update notifcation */
        //$data = array('notification'=> 0);
        //$this->db->simple_query("SET NAMES 'utf-8'");  
        //$data_where = array('room_id'=> $room_id, 'user_id'=> $user_data['id']);        
       // $this->db->set($data);
       // $this->db->update('chatusers', $data, $data_where);
        $sql = "update chatusers set notification = '0' where room_id = '$room_id' and user_id = '{$user_data['id']}'";
        $update_notif = $this->db->query($sql);

        //check if user is allowed to view this chatroom
        $sql = "select chat_id from chatusers where room_id = '$room_id' and user_id = '{$user_data['id']}' and cstatus = '1' LIMIT 1";

        $check_u = $this->db->query($sql);
        if($check_u->num_rows() == 0){
            echo "not_allow_to_view";
        }else{
            $get_chat_id = $check_u->row_array();
            $o['chat_id'] = $get_chat_id['chat_id'];


            $sql = "select cc.convo_id,cc.room_id,cc.user_id,cc.user_name,cc.text,cc.date_sent,
            cc.file_path,cc.file_name,cc.file_image,cc.is_delivered,cc.reader, 
            (select user_id from chatroom where id = cc.room_id LIMIT 1) as room_owner_id,
            (select profile_pic from users where id = cc.user_id LIMIT 1) as user_img 
            from 
                chatconvo as cc
            where 
                cc.room_id = '$room_id' order by convo_id desc LIMIT 100";

            $check_convo = $this->db->query($sql);
            if($check_convo->num_rows() == 0){
                $o['status'] =  "no_chat_found";
            }else{
                $show_content = array();
                $exp_reader = "";

                foreach($check_convo->result_array() as $cc){

                    // check convo owner
                    if($cc['room_owner_id'] == $user_data['id']){
                        $cc['delete_row_link'] = 1; 
                    }                   

                    if($user_data['id'] == $cc['user_id']){
                        $cc['is_me'] = 1;
                        $cc['delete_row_link'] = 1; 
                    }
                    $cc['text'] = $this->chat->convert_videos($cc['text']);
                    if($cc['file_path'] != '' && $cc['file_name'] != ''){
                        $token_file_name = md5($cc['file_name']);
                        //$cc['text'] = "Download attachment: <a href='$this->baseurl/chatroom/download_attachment/{$cc['convo_id']}/$room_id/$token_file_name' target=blank>{$cc['file_name']}</a>";
                        if($cc['file_image'] == 1){
                            $attached_img = "<a href=\"#\" onclick=\"popup_video('" . $this->baseurl . "assets/uploadfiles/{$cc['file_path']}','image');return false;\"><img src=\"$this->baseurl/assets/uploadfiles/{$cc['file_path']}\" width=\"107\" height=\"75\" /></a> <br/> Download: <a href='" . $this->baseurl . "chatroom/download_attachment/{$cc['convo_id']}/$room_id/$token_file_name' target=blank>{$cc['file_name']}</a>";
                            $cc['text'] = $attached_img;
                        }else{
                            $cc['text'] = "Download attachment: <a href='" . $this->baseurl . "livesupport/download_attachment/{$cc['convo_id']}/$room_id/$token_file_name' target=blank>{$cc['file_name']}</a>";
                        }
                    }
                    $cc['time_human'] = timespan($cc['date_sent'],time());
                    
                    // check readers
                    
                    if($cc['user_id'] != $user_data['id']){

                        if($cc['reader'] == ""){
                            $save_reader = $user_data['id'] . "|";
                            $save_reader_date = date("c",time()) . "|";
                            $sql = "update chatconvo set reader = '$save_reader', date_read = '$save_reader_date'  where convo_id = '{$cc['convo_id']}'";
                            $upnot = $this->db->query($sql);
                        }else{
                            $explode_reader = explode("|",$cc['reader']);
                            if (!in_array($user_data['id'], $explode_reader))
                            {
                                $save_reader = $cc['reader'] . $user_data['id'] . "|";
                                $save_reader_date = date("c",time()) . "|";
                                $sql = "update chatconvo set reader = '$save_reader', date_read = '$save_reader_date' where convo_id = '{$cc['convo_id']}'";
                                $upnot = $this->db->query($sql);    
                            }
                        }

                    }

                    // check number of readers

                    $exp_reader = explode("|",$cc['reader']);
                    $count_reader = count($exp_reader) - 1;


                        $new_count_reader = $count_reader -1;
                        $last_reader_id = $exp_reader[$new_count_reader];

                        $exp_last_name = explode(":",$last_reader_id);
                        

                        $sql = "select first_name from users where id='{$exp_last_name[0]}' LIMIT 1";
                        $seen_name ="";
                        $check_name = $this->db->query($sql);
                        if($check_name->num_rows() == 0){
                            $seen_name = "no name";
                        }else{
                            $sn2 = $check_name->row_array();
                            $explode_name = explode(" ", $sn2['first_name']);
                            $seen_name = "$explode_name[0]";
                        }
                        //echo "{$cc['text']} - {$cc['reader']} = $exp_last_name[0] $sql = {$sn2['name']} = $seen_name<br>";
                        if($new_count_reader < 2){
                            $text_seen = "read by $seen_name";
                        }else{
                            $text_seen = "read by $seen_name + $new_count_reader more";
                        }

                    $cc['text_seen'] = $text_seen;
                    if($cc['reader'] != "" or $save_reader != ""){
                        $cc['is_read'] = 1;
                    }
                    $show_content[] = $cc;

                }
        
                
            }
            $this->db->trans_complete();
            $show_content = array_reverse($show_content);

            $o['show_content'] = $show_content;
            $o['baseurl'] = $this->baseurl;
          //  $this->smarty->assign('o',$o);          
          //  $this->smarty->view( 'chatroom/chat_content.tpl',$data);
            $this->data['o'] = $o;        
           // $this->smarty->view( 'chat/room_name.tpl',$data);
            $this->_render_page('chat/chat_content', $this->data);    

        }
    }

    public function postchat(){

         $user_data = (array) $this->user;

        $room_id    = $this->input->post('room_id');
        $msg        = $this->chat->cleanup_msg($this->input->post('msg'));
        $date_sent  = $this->chat->cleanup($this->input->post('date_sent'));

        $this->db->trans_start();

        $data2 = array( 'user_id' => $user_data['id'], 
                        'room_id'=> $room_id, 
                        'user_name' => $user_data['first_name'],
                        'user_img'=> $user_data['profile_pic'], 
                        'text' => $msg,
                        'date_sent'=> $date_sent
        );
        $this->db->set($data2);
        $this->db->insert('chatconvo'); 
        $last_inserted_id = $this->db->insert_id();     
        
        //update user notifications
        $sql = "select chat_id,notification from chatusers where room_id = '$room_id' and user_id != '{$user_data['id']}' and cstatus = '1'";
        $check_not = $this->db->query($sql);
        if($check_not->num_rows() > 0){

            foreach($check_not->result_array() as $cn){

                $data = array('notification'=> $cn['notification'] + 1);
                $this->db->simple_query("SET NAMES 'utf-8'");  
                $data_where = array('chat_id'=> $cn['chat_id']);        
                $this->db->set($data);
                $this->db->update('chatusers', $data, $data_where);
            }
        }

        $data = array('date_update'=> time());
                $this->db->simple_query("SET NAMES 'utf-8'");  
                $data_where = array('id'=> $room_id);       
                $this->db->set($data);
        $this->db->update('chatroom', $data, $data_where);  
        
        $this->db->trans_complete();

        echo $last_inserted_id;

    }

    #### SHOW POST MESSAGES FROM NODE JS. #############
    public function show_post_msg(){
        
        $user_data = (array) $this->user;

        $room_id    = $this->input->post('room_id');
        $msg_id     = $this->input->post('msg_id');
        $date_sent  = $this->input->post('date_sent');
        $user_chat_id   = $this->input->post('user_chat_id');   
        
        $this->db->trans_start();

        $is_trainer = $user_data['is_trainer'];
        $o['is_trainer'] = $is_trainer;

        if($this->ion_auth->in_group("admin")){
             $o['is_admin'] = 1;
        }


        /* update notifcation */
        $sql = "update chatusers set notification = '0' where chat_id = '$user_chat_id'";
        $upnot = $this->db->query($sql);

        $sql = "select cc.convo_id,cc.room_id,cc.user_id,cc.user_name,cc.text,cc.date_sent,
        cc.file_path,cc.file_name,cc.file_image,cc.is_delivered,cc.reader, 
        (select user_id from chatroom where id = cc.room_id LIMIT 1) as room_owner_id,
        (select profile_pic from users where id = cc.user_id LIMIT 1) as user_img 
        from 
            chatconvo as cc
        where 
            cc.room_id = '$room_id' and cc.convo_id = '$msg_id' LIMIT 1";
        $check_convo = $this->db->query($sql);
        if($check_convo->num_rows() > 0)
        {
            //update chatconvo to delivered
            $cc = $check_convo->row_array();

                    /* check convo owner */
            if($cc['room_owner_id'] == $user_data['id']){
                $cc['delete_row_link'] = 1; 
            }


            if($user_data['id'] == $cc['user_id']){
                $cc['is_me'] = 1;
                $cc['delete_row_link'] = 1; 
            }
            $cc['moment_date_sent'] = $date_sent;
            $cc['text'] = $this->chat->convert_videos($cc['text']);
            if($cc['file_path'] != '' && $cc['file_name'] != ''){
                $token_file_name = md5($cc['file_name']);
                if($cc['file_image'] == 1){
                    $attached_img = "<a href=\"#\" onclick=\"popup_video('" . $this->baseurl . "assets/uploadfiles/{$cc['file_path']}','image');return false;\"><img src=\"$this->baseurl/assets/uploadfiles/{$cc['file_path']}\" width=\"107\" height=\"75\" /></a> <br/> Download: <a href='".$this->baseurl ."livesupport/download_attachment/{$cc['convo_id']}/$room_id/$token_file_name' target=blank>{$cc['file_name']}</a>";
                    $cc['text'] = $attached_img;
                }else{
                    $cc['text'] = "Download attachment: <a href='" . $this->baseurl . "livesupport/download_attachment/{$cc['convo_id']}/$room_id/$token_file_name' target=blank>{$cc['file_name']}</a>";
                }
                
            }
            $cc['time_human'] = timespan($cc['date_sent'],time());
            // check readers
            if($cc['user_id'] != $user_data['id']){
                if($cc['reader'] == ""){
                    $save_reader = $user_data['id'] . "|";
                    $save_reader_date = date("c",time()) . "|";
                    $sql = "update chatconvo set reader = '$save_reader', date_read = '$save_reader_date'  where convo_id = '{$cc['convo_id']}'";
                    $upnot = $this->db->query($sql);
                }else{
                    $explode_reader = explode("|",$cc['reader']);
                    if (!in_array($user_data['id'], $explode_reader))
                    {
                        $save_reader = $cc['reader'] . $user_data['id'] . "|";
                        $save_reader_date = date("c",time()) . "|";
                        $sql = "update chatconvo set reader = '$save_reader', date_read = '$save_reader_date'  where convo_id = '{$cc['convo_id']}'";
                        $upnot = $this->db->query($sql);    
                    }
                }
            }
    
            if($cc['reader'] != "" or $save_reader != ""){
                $cc['is_read'] = 1;
            }

            $o['cc'] = $cc;

            $sql = "update chatconvo set is_delivered = '1' where convo_id = '$msg_id'";
            $upnot = $this->db->query($sql);
            
            $this->db->trans_complete();


            $o['baseurl'] = $this->baseurl;        
            //$this->smarty->view( 'chatroom/chat_post_node.tpl',$data);
            $o['baseurl'] = $this->baseurl;
            $this->data['o'] = $o;        
            $this->_render_page('chat/chat_post_node', $this->data);  
        }
    }

    ############### CHECK LAST MESSAGE READ #############
    public function message_read(){
        $user_data = (array) $this->user;
        $convo_id   = $this->input->post('convo_id');
        $sql = "select reader from chatconvo where convo_id = '$convo_id' LIMIT 1";
        $check_convo = $this->db->query($sql);
        if($check_convo->num_rows() > 0){
            $check_co = $check_convo->row_array();
            if($check_co['reader'] != ""){
                echo "read";
            }
        }
    }

    public function message_read_seen(){
        $user_data = (array) $this->user;
        $convo_id   = $this->input->post('convo_id');
        $sql = "select reader from chatconvo where convo_id = '$convo_id' LIMIT 1";
        $check_convo = $this->db->query($sql);
        if($check_convo->num_rows() > 0){
            $check_co = $check_convo->row_array();
            if($check_co['reader'] != ""){

                /* check number of readers*/
                $exp_reader = explode("|",$check_co['reader']);
                $count_reader = count($exp_reader) - 1;
                    $new_count_reader = $count_reader -1;
                    $last_reader_id = $exp_reader[$new_count_reader];

                    $exp_last_name = explode(":",$last_reader_id);
                    $sql = "select first_name from users where id='{$exp_last_name[0]}' LIMIT 1";
                    $check_name = $this->db->query($sql);
                    if($check_name->num_rows() == 0){
                        $seen_name = "no name";
                    }else{
                        $sn = $check_name->row_array();
                        $explode_name = explode(" ", $sn['first_name']);
                        $seen_name = $explode_name[0];
                    }
                    
                    if($new_count_reader < 2){
                            $text_seen = "<a href=\"#\" onclick=\"popup_seen('$convo_id'); return false;\">read by $seen_name</a>";
                        }else{
                            $text_seen = "<a href=\"#\" onclick=\"popup_seen('$convo_id'); return false;\">read by $seen_name +$new_count_reader more</a>";
                    }

                echo "read|$text_seen";
            }
        }
    }

    ############### SHOW POST UPLOAD FROM NODE.JS
    public function show_post_upload(){
        $room_id    = $this->input->post('room_id');
        $convo_id   = $this->input->post('convo_id');
    }
    #############
    public function upload_chatfile(){
        $user_data = (array) $this->user;
        $room_id    = $this->input->post('room_id');

        if (!empty($_FILES)) {

            $orig_file_name     = $_FILES['afile']['name'];
            $config['upload_path'] = './assets/uploadfiles/';
            $config['allowed_types'] = 'jpeg|jpg|doc|docx|xls|docx|png|pdf|pot|ppt|pptx|dot|dotx|ods';
            $config['max_size'] = '10240'; // 50MB
            $config['encrypt_name'] = TRUE;
            $this->load->library('upload', $config);

            // Validate the file type           
            //$fileParts = md5(time() . "_" . $_FILES['Filedata']['name']);
            //$config['file_name'] = $fileParts;
            $filetype   = $_FILES['afile']['type'];         
            $file_name  = $_FILES['afile']['name'];
            $Filedata   = $_FILES['afile']['tmp_name'];     


            if ( ! $this->upload->do_upload('afile'))
            {
                //$error = array('error' => $this->upload->display_errors());
                //echo strip_tags($error['error']);
                $json = json_encode(array(
                  'error' => 1,
                  'error_msg'  => $this->upload->display_errors()
                    ));
                echo $json;
            }
            else
            {
                    $this->db->trans_start();
                    $data2 = array('upload_data' => $this->upload->data());
                    $new_filename           = $data2['upload_data']['file_name'];
                    $original_filename      = $data2['upload_data']['client_name'];
                    $file_size              = $data2['upload_data']['file_size'];
                    $upload_type            = $data2['upload_data']['file_ext'];
                    $upload_is_image        = $data2['upload_data']['is_image'];

                    //$date_added = date("c",time());
                    $data2 = array( 'user_id' => $user_data['id'], 
                                    'room_id'=> $room_id, 
                                    'user_name' => $user_data['first_name'],
                                    'user_img'=> $user_data['profile_pic'], 
                                    'text' =>   "attachment: $original_filename",
                                    'file_name' => $original_filename,
                                    'file_type' => $upload_type,
                                    'file_path' => $new_filename,
                                    'file_image' => $upload_is_image,
                                    'date_sent'=> time()
                    );
                    $this->db->set($data2);
                    $this->db->insert('chatconvo');     
                    $last_inserted_id = $this->db->insert_id(); 
                    
                    //update user notifications
                    $sql = "select chat_id,notification from chatusers where room_id = '$room_id' and user_id != '{$user_data['id']}' and cstatus = '1'";
                    $check_not = $this->db->query($sql);
                    if($check_not->num_rows() > 0){

                        foreach($check_not->result_array() as $cn){

                            $data = array('notification'=> $cn['notification'] + 1);
                            $this->db->simple_query("SET NAMES 'utf-8'");  
                            $data_where = array('chat_id'=> $cn['chat_id']);        
                            $this->db->set($data);
                            $this->db->update('chatusers', $data, $data_where);
                        }
                    }
                    $this->db->trans_complete();

                $json = json_encode(array(
                  'error' => 0,
                  'error_msg'  => "success",
                  'convo_id' => $last_inserted_id
                    ));
                echo $json;

            }
        }   
    }   

    public function download_attachment(){
         $user_data = (array) $this->user;
        $this->load->helper('download');

        $convo_id   = $this->uri->segment(3);
        $room_id    = $this->uri->segment(4);
        $token_path = $this->uri->segment(5);
        $sql = "select file_path,file_name from chatconvo 
            where 
            room_id = '$room_id' and convo_id = '$convo_id' order by convo_id asc LIMIT 1";

        $check_file = $this->db->query($sql);
        if($check_file->num_rows() == 0){
            echo "Invalid, please try again";
        }else{
            $sf = $check_file->row_array();

            if($token_path == md5($sf['file_name'])){
                $data = file_get_contents("assets/uploadfiles/{$sf['file_path']}");
                $name = $sf['file_name'];
                force_download($name, $data);

            }else{
                echo "Invalid, please try again.";
            }
        }
    }

    public function delete_row_convo(){
        include("includes/check_user.php"); 
        $convo_id   = $this->input->post('convo_id');
        $this->db->where('convo_id', $convo_id);
        $this->db->delete('chatconvo');
        echo 1;         
    }

    public function show_popup_seen(){
        $user_data = (array) $this->user;
        $convo_id   = $this->uri->segment(3);

        $sql = "select convo_id,room_id,user_id,user_name,text,date_sent,file_path,file_name,is_delivered,reader,date_read,
                (select profile_pic from users where id = chatconvo.user_id LIMIT 1) as user_img 
                    from chatconvo 
                where 
                    convo_id = '$convo_id' LIMIT 1";
        $check_seen = $this->db->query($sql);
        if($check_seen->num_rows() == 0){
            echo "invalid id, please try again";
        }else{
            $ss = $check_seen->row_array();
            if($user_data['id'] == $ss['user_id']){
                $ss['text'] = $this->chat->convert_videos($ss['text']);
                if($ss['file_path'] != '' && $ss['file_name'] != ''){
                    $token_file_name = md5($cc['file_name']);
                    $ss['text'] = "Download attachment: <a href='" . $this->baseurl . "chatroom/download_attachment/{$cc['convo_id']}/$room_id/$token_file_name' target=blank>{$cc['file_name']}</a>";
                }
                $ss['time_human'] = timespan($ss['date_sent'],time());

                //explode date_read
                $user_read = explode("|",$ss['reader'], -1);
                $date_read = explode("|",$ss['date_read'], -1);
                
                $show_reader = array();
                for($i=0; $i<count($user_read); $i++){
                    $sql = "select first_name from users where id='{$user_read[$i]}' LIMIT 1";
                    $check_name = $this->db->query($sql);
                    if($check_name->num_rows() == 0){
                        $sr['seen_name'] = "no name";
                        $sr['date_seen'] = "none";
                    }else{
                        $sn = $check_name->row_array();
                        $sr['seen_name'] = $sn['first_name'];
                        $sr['date_seen'] = date("Y-m-d H:i a",strtotime($date_read[$i]));
                    }

                    $show_reader[] = $sr;
                }
                $o['show_reader'] = $show_reader;

                $o['ss'] = $ss;
                $o['baseurl'] = $this->baseurl;
                $this->data['o'] = $o;        
               // $this->_render_page('chat/show_seen', $this->data);   
                $this->load->view('chat/show_seen', $this->data);    
            }else{
                echo "invalid id, please try again";
            }
        }

        
    }

    public function checkchatlist(){
        $user_data = (array) $this->user;

        //check user if admin or not
        /*
        $sql = "select user_id from users_groups where user_id = '{$user_data['user_id']}' and group_id ='1'";
        $check_admin = $this->db->query($sql);
        if($check_admin->num_rows() > 0){
            $o['is_admin'] = 1;
        }
        */
        if($this->ion_auth->in_group("admin")){
             $o['is_admin'] = 1;
        }

        $sql = "select cr.user_id as owner_id,cr.id as room_id,cr.room_name,cu.notification, cu.chat_id 
                    from 
                        chatroom as cr, chatusers as cu 
                where 
                    cu.room_id = cr.id and cu.cstatus = '1' and cu.user_id = '{$user_data['id']}' and cr.status = '1'
                order by cr.date_update desc";
        
        $check_list = $this->db->query($sql);
        if($check_list->num_rows() == 0){
            echo "<div class=\"alert alert-info\" role=\"alert\">No Chatlist, Create now!</div>";
        }else{
            $show_result = array();
            

            $total_conversation = $check_list->num_rows();
            $count_notification = 0;
            foreach($check_list->result_array() as $cl){
            
            $count_notification = $count_notification + $cl['notification'];

                if($cl['owner_id'] == $user_data['id']){
                    $cl['is_owner'] = 1;
                }

            $sql = "select u.profile_pic,u.first_name,cu.user_id 
                    from 
                        users as u, chatusers as cu
                    where
                        u.id = cu.user_id
                    and
                        cu.room_id = '{$cl['room_id']}'
                    and
                        cu.user_id != '{$user_data['id']}'";

            $check_names = $this->db->query($sql);
            if($check_names->num_rows() == 0){
                $cl['room_name'] = "No room name";
            }else{
                $total_users = $check_names->num_rows();
                $ch_name = $check_names->result_array();
                $split_name = explode(" ", $ch_name[0]['name']);


                if($total_users == 1){
                    if($cl['room_name'] == ""){
                        $cl['room_name'] = $split_name[0];
                    }
                    $cl['user_img'] = $ch_name[0]['profile_pic'];
                    
                }else{
                    $new_total_users = $total_users - 1;
                    $cl['user_img'] = "0";
                    if($cl['room_name'] == ""){
                        if($new_total_users == 2){
                            $cl['room_name'] = $split_name[0] . " + " . $new_total_users . " other";
                        }else{
                            $cl['room_name'] = $split_name[0] . " + " . $new_total_users . " others";
                        }
                    }
                }
                
            }
                
            /* check last convo */  
            $sql = "select user_id,SUBSTRING(text, 1, 100) as text,date_sent from chatconvo where room_id = '{$cl['room_id']}' order by convo_id desc LIMIT 1";
            $check_conv = $this->db->query($sql);
            if($check_conv->num_rows() == 0){
                $cl['latest_text'] = "";
            }else{
                $show_lt = $check_conv->row_array();
                $cl['latest_sent'] = timespan($show_lt['date_sent'],time());
                $cl['latest_text'] = $show_lt['text'];
            }
            
                $show_result[] = $cl;
            }
            $o['ud'] = $user_data;
            $o['show_result'] = $show_result;
            $o['baseurl'] = $this->baseurl;
            //$this->smarty->assign('o',$o);          
            //echo $count_notification . "__SPLITRESULT__" . $this->smarty->view('chatroom/room_list.tpl',$data);
            $this->data['o'] = $o;        
            echo $count_notification . "__SPLITRESULT__" . $this->_render_page('chat/room_list', $this->data);   
        }
    }

    /* search message */
    public function search_messages(){
        $user_data = (array) $this->user;
        $search_msg             = $this->input->post('search_msg');

        $sql = "select distinct(cc.room_id)  
                from 
            chatconvo as cc 
            where 
            (text like '%$search_msg%' or user_name like '%$search_msg%')
            and cc.room_id = (select room_id from chatusers where user_id = '{$user_data['id']}' and room_id = cc.room_id LIMIT 1)";
        $check_search = $this->db->query($sql);
        if($check_search->num_rows() == 0){
            echo "no_result_found";
        }else{

            $show_result = array();
            foreach($check_search->result_array() as $sr){
            $sql = "select cr.user_id as owner_id,cr.id as room_id,cr.room_name,cu.notification, cu.chat_id 
                            from 
                                chatroom as cr, chatusers as cu 
                        where 
                            cu.room_id = cr.id and cu.cstatus = '1' and cu.user_id = '{$user_data['id']}' and cr.status = '1'
                        and cr.id = '{$sr['room_id']}'
                        order by cr.date_update desc";
                    
                $check_list = $this->db->query($sql);
                if($check_list->num_rows() > 0)
                {
                    $show_result = array();
                    
                    foreach($check_list->result_array() as $cl)
                    {
                        if($cl['owner_id'] == $user_data['id']){
                            $cl['is_owner'] = 1;
                        }
                        $sql = "select u.first_name,cu.user_id 
                                from 
                                    users as u, chatusers as cu
                                where
                                    u.id = cu.user_id
                                and
                                    cu.room_id = '{$cl['room_id']}'
                                and
                                    cu.user_id != '{$user_data['id']}'";
                        $check_names = $this->db->query($sql);
                        if($check_names->num_rows() == 0){
                            $cl['room_name'] = "No room name";
                        }else{
                            $total_users = $check_names->num_rows();
                            $ch_name = $check_names->result_array();
                            $split_name = explode(" ", $ch_name[0]['name']);

                            if($total_users == 1){
                                if($cl['room_name'] == ""){
                                    $cl['room_name'] = $split_name[0];
                                }
                                $cl['user_img'] = $ch_name[0]['profile_pic'];
                                
                            }else{
                                $new_total_users = $total_users - 1;
                                $cl['user_img'] = "0";
                                if($cl['room_name'] == ""){
                                    if($new_total_users == 2){
                                        $cl['room_name'] = $split_name[0] . " + " . $new_total_users . " other";
                                    }else{
                                        $cl['room_name'] = $split_name[0] . " + " . $new_total_users . " others";
                                    }
                                }
                            }
                            
                        }

                    }
                    $sql = "select user_id,SUBSTRING(text, 1, 100) as text from chatconvo where room_id = '{$cl['room_id']}' order by convo_id desc LIMIT 1";
                    $check_conv = $this->db->query($sql);
                    if($check_conv->num_rows() == 0){
                        $cl['latest_text'] = "";
                    }else{
                        $show_lt = $check_conv->row_array();
                        $cl['latest_text'] = $show_lt['text'];
                    }

                }

                $show_result[] = $cl;
            }
            $o['show_result'] = $show_result;
            $o['ud'] = $user_data;
            $o['baseurl'] = $this->baseurl;
            //$this->smarty->assign('o',$o);          
            //echo $count_notification . "__SPLITRESULT__" . $this->smarty->view('chatroom/room_list.tpl',$data);
            $this->data['o'] = $o;        
            echo $count_notification . "__SPLITRESULT__" . $this->_render_page('chat/room_list', $this->data);     
        }    
    }

    function show_user_info(){
        $user_data = (array) $this->user;
        $user_id    = $this->uri->segment(3);
        $this->load->helper('date');

        $sql = "select u.id,u.first_name,u.last_name,u.is_aff,u.aff_status,u.aff_added,p.plan_id,p.return_id
                from 
                    users as u,
                    paypal as p
                where 
                    u.id = '$user_id'
                and 
                    u.id = p.user_id 
                and
                    p.p_status = 'ACTIVE'
                and 
                    p.ppstatus = '1'
                LIMIT 1";
        $check_user = $this->db->query($sql);
        if($check_user->num_rows() == 0){
            echo '';
        }else{
            $show_r = $check_user->row_array();
            //$url = base_url()."paypal.php?p=check_billing_agreement&id=".$show_r['return_id'];
            $url = "http://localhost/paypal.php?p=check_billing_agreement&id=".$show_r['return_id'];
            $subscription_status = (array) json_decode(file_get_contents( $url ));

            $show_r['state']                = $subscription_status['state'];
            $show_r['start_date']           = date("Y-m-d", strtotime($subscription_status['start_date']));
            

            $show_r['next_billing_date']    = date("Y-m-d", strtotime($subscription_status['agreement-details']->next_billing_date));
            $show_r['cycles_completed']     = $subscription_status['agreement-details']->cycles_completed;
            $show_r['aff_added']            = date("Y-m-d", $show_r['aff_added']);
            $show_r['start_date_human']     = timespan(strtotime($show_r['aff_added']), time());
            

                /* check all approved */
                $sql = "select aff.user_id_aff,aff.date_added,afp.amt as aff_amt,
                (select CONCAT(first_name, ' ',last_name) from users where id = afp.sender_id LIMIT 1) as user_aff,
                pp.amt,pp.curr,pp.p_status,pp.date_confirmed
                from 
                affiliates as aff,
                aff_payout as afp,
                paypal as pp
                where 
                    aff.user_id = '{$show_r['id']}'
                and
                    afp.receiver_id = aff.user_id
                and
                    pp.user_id = aff.user_id_aff
                and
                    pp.p_status = 'ACTIVE'
                and 
                    aff.aff_status = '1'
                GROUP BY user_id_aff
                order by 
                    afp.date_transaction desc";
                $check_com = $this->db->query($sql);
                if($check_com->num_rows() == 0){
                    $show_r['active_count_users'] = 0;
                }else{
                    $show_r['active_count_users'] = $check_com->num_rows();
                }

                $sql = "select count(user_id) as count_aff from affiliates where user_id = '{$show_r['id']}' and aff_status = '1' LIMIT 1";

                $check_aff = $this->db->query($sql);
                if($check_aff->num_rows() == 0){
                    $show_r['affiliate_count'] = 0;
                }else{
                    $get_aff = $check_aff->row_array();
                    $show_r['affiliate_count'] = $get_aff['count_aff'];
                }

                /* check chargeback */
                $sql = "select cb_id from aff_chargeback where master_aff_user_id = '{$show_r['id']}' 
                    and is_returned = '1' LIMIT 1";
                $check_charge_back = $this->db->query($sql);
                if($check_charge_back->num_rows() == 0){
                    $show_r['chargeback'] = 0;
                }else{
                    $show_r['chargeback'] = (($check_charge_back->num_rows() / $show_r['affiliate_count']) * 100) . "%";
                }

                /* check latest logs*/
                $sql = "select log_type,log_desc,date_added from users_logs where user_id = '{$show_r['id']}' order by log_id desc limit 10";
                $check_logs = $this->db->query($sql);
                if($check_logs->num_rows() > 0){
                    $show_logs = array();
                    foreach($check_logs->result_array() as $cl){
                        $cl['date_added'] = timespan($cl['date_added'], time());
                        $show_logs[] = $cl;
                    }
                    $o['show_logs'] = $show_logs;
                    $o['show_logs_table'] = true;
                }
        }
        /*
        echo "<pre>";
        print_r($subscription_status);
        print_r($show_r);
        */
        $o['show_sched_table'] = true;
        $o['support_admin_header'] = true;
        $o['sr'] = $show_r;
        $o['baseurl'] = $this->baseurl;        
        $this->data['o'] = $o;        
       // $this->_render_page('chat/show_user_info', $this->data);  
        $this->load->view('chat/show_user_info', $this->data);            
    }

    public function delete_thread(){
        $user_data = (array) $this->user;
        $room_id    = $this->input->post('room_id');
        $this->db->trans_start();

        $result = $this->db->delete('chatroom', array('id' => $room_id));
        $this->db->delete('chatusers', array('room_id' => $room_id));
        $this->db->delete('chatconvo', array('room_id' => $room_id));

        $this->logs->insert_logs("delete chat", "delete chat thread");

        echo 'success';
        $this->db->trans_complete();
        
    }

    function check_permission(){
        if (!$this->ion_auth->logged_in() )
        {
            //redirect them to the login page
            $this->session->set_flashdata('message', $this->ion_auth->messages());
            redirect('main', 'refresh');
        }  
    }   

    function redirect_link($path,$msg,$msg_type){

        $this->session->set_flashdata('msg', $msg);
        $this->session->set_flashdata('msg_type', $msg_type);            
        redirect($this->baseurl . "$path", 'refresh');        
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