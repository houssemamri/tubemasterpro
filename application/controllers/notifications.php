 <?php defined('BASEPATH') OR exit('No direct script access allowed');

class Notifications extends MX_Controller {
   
    public $data;

    function __construct() {

        parent::__construct();
        $this->load->library('authentication', NULL, 'ion_auth');
        $this->load->library('form_validation');
        $this->load->helper('url','form');
        $this->load->library('template');
        $this->load->database();
        $this->load->model('Admin_model', 'admin'); 

        $this->form_validation->set_error_delimiters($this->config->item('error_start_delimiter', 'ion_auth'), $this->config->item('error_end_delimiter', 'ion_auth'));

        $this->lang->load('auth');
        $this->load->helper('language');
        $this->load->helper('captcha');

        $this->baseurl = $this->config->config['base_url']; 

        $this->load->library('email');

    }

    function index(){
        die("Welcome to Tubemasterpro.com");
    }

    function send_notifications(){
die();
                $this->db->trans_start();
                $config['protocol'] = 'smtp';
                $config['smtp_host'] = 'localhost';
                $config['smtp_port'] = '25';
                $config['charset'] = 'iso-8859-1';
                $config['wordwrap'] = TRUE;
                $config['mailtype'] = 'html';

                $config['smtp_host'] = 'box342.bluehost.com';
                $config['smtp_user'] = 'nathan@nathanhague.com';
                $config['smtp_pass'] = '$Wolfman1';
                $config['smtp_port'] = '26';
                $this->email->initialize($config);

                $date_today         = date('Y-m-d', time());
                $date_yesterday     = date('Y-m-d', strtotime('-1 days'));
                $date_twodays_ago   = date('Y-m-d', strtotime('-2 days'));
                $date_threedays_ago   = date('Y-m-d', strtotime('-3 days'));
                $date_fourdays_ago   = date('Y-m-d', strtotime('-5 days'));


        $sql = "select not_id from notification where notif_date = '$date_today' LIMIT 1";
        $check_notif_date = $this->db->query($sql);
        if($check_notif_date->num_rows() == 0){
     
                //check trial users who haven't create a campaign
                 $sql = "select 
                    u.first_name,
                    u.last_name,
                    u.id,
                    u.is_subscribe,
                    u.created_on,
                    u.email,
                    FROM_UNIXTIME(u.created_on, '%Y-%m-%d') as date_created
                from 
                    users as u,
                    users_groups as ug
                where
                    u.id = ug.user_id
                AND
                    u.id not in (select user_id from paypal)
                AND
                    u.id not in (select user_id from campaign_list where user_id = u.id )
                AND 
                     ug.group_id = '2'
                AND
                    u.active = '1'
                AND
                     ug.group_id not in ('1','5')           
                AND
                 FROM_UNIXTIME(u.created_on, '%Y-%m-%d') > '$date_fourdays_ago' 
                order 
                    by u.created_on asc";

                $check_threedays = $this->db->query($sql);
                if($check_threedays->num_rows() > 0){

                    foreach($check_threedays->result_array() as $ct){


                        //check 3 days ago
                        if($ct['date_created'] == $date_threedays_ago){
                             $this->email->from('support@tubemasterpro.com', 'TubeMasterPro');
                            //$this->email->to("{$ct['email']}");  
                             $this->email->to("australiawow@gmail.com");   

                            $this->email->subject("TubeMasterPro Notification");
                            $this->email->message("Hi {$ct['first_name']}, <br>  <br> you haven't create a campaign for 3 days now. <br><br> Regards, <br> TubeMasterPro Team");  
                            $this->email->send();
                        }
                    
                    }


                }

                //check trial users who haven't create a list
                $sql = "select 
                    u.first_name,
                    u.last_name,
                    u.id,
                    u.is_subscribe,
                    u.created_on,
                    u.email,
                    FROM_UNIXTIME(u.created_on, '%Y-%m-%d') as date_created
                from 
                    users as u,
                    users_groups as ug
                where
                    u.id = ug.user_id
                AND
                   u.id not in (select user_id from paypal)
                AND
                    u.id not in (select user_id from targets where user_id = u.id )
                AND 
                     ug.group_id = '2'
                AND
                    u.active = '1'
                AND
                     ug.group_id not in ('1','5')           
                AND
                 FROM_UNIXTIME(u.created_on, '%Y-%m-%d') > '$date_fourdays_ago' 
                order 
                    by u.created_on asc";

                $check_no_list = $this->db->query($sql);
                if($check_no_list->num_rows() > 0){

                    foreach($check_no_list->result_array() as $ct){

                        //check 3 days ago
                        if($ct['date_created'] == $date_threedays_ago){
                             $this->email->from('support@tubemasterpro.com', 'TubeMasterPro');
                            //$this->email->to("{$ct['email']}");  
                             $this->email->to("renefandida@gmail.com");   

                            $this->email->subject("TubeMasterPro Notification");
                            $this->email->message("Hi {$ct['first_name']}, <br>  <br> you haven't created a list for 3 days now. <br><br> Regards, <br> TubeMasterPro Team");  
                            $this->email->send();
                        }

                        if($ct['date_created'] == $date_twodays_ago){
                             $this->email->from('support@tubemasterpro.com', 'TubeMasterPro');
                            //$this->email->to("{$ct['email']}");  
                             $this->email->to("renefandida@gmail.com");   

                            $this->email->subject("TubeMasterPro Notification");
                            $this->email->message("Hi {$ct['first_name']}, <br>  <br> you haven't created a list for 2 days now. <br><br> Regards, <br> TubeMasterPro Team");  
                            $this->email->send();
                        }
                    
                    }


                }

            //if they haven’t searched for anything in first 24 hours of becoming trial user = send email
            $sql = "select 
                    u.first_name,
                    u.last_name,
                    u.id,
                    u.is_subscribe,
                    u.created_on,
                    u.email,
                    FROM_UNIXTIME(u.created_on, '%Y-%m-%d') as date_created
                    from 
                        users as u,
                        users_groups as ug
                    where
                        u.id = ug.user_id
                    AND
                       u.id not in (select user_id from paypal)
                    AND 
                         ug.group_id = '2'
                    AND
                        u.active = '1'
                    AND
                         ug.group_id not in ('1','5')           
                    AND
                     FROM_UNIXTIME(u.created_on, '%Y-%m-%d') > '$date_fourdays_ago' 
                    order 
                        by u.created_on asc";

                    $check_no_search_list = $this->db->query($sql);
                    if($check_no_search_list->num_rows() > 0){

                    foreach($check_no_search_list->result_array() as $ct){

                        //check 3 days ago

                        if($ct['date_created'] == $date_yesterday){

                            //check if user has no search message
                            $sql = "select log_type from users_logs where user_id = '{$ct['id']}' and log_type = 'video_search' LIMIT 1";
                            $check_search = $this->db->query($sql);
                            if($check_search->num_rows() == 0){
                             $this->email->from('renefandida@tubemasterpro.com', 'TubeMasterPro');
                            
                            //$this->email->to("{$ct['email']}");  
                             $this->email->to("renefandida@gmail.com");   

                            $this->email->subject("TubeMasterPro Notification");
                            $this->email->message("Hi {$ct['first_name']}, <br>  <br> you haven't search for almost a day. <br><br> Regards, <br> TubeMasterPro Team");  
                            $this->email->send();
                            }
                        }

                    
                    }


                }


                //if they haven’t exported to adwords in 3 days of becoming trial user = send email                
                $sql = "select 
                    u.first_name,
                    u.last_name,
                    u.id,
                    u.is_subscribe,
                    u.created_on,
                    u.email,
                    FROM_UNIXTIME(u.created_on, '%Y-%m-%d') as date_created
                from 
                    users as u,
                    users_groups as ug
                where
                    u.id = ug.user_id
                AND
                   u.id not in (select user_id from paypal)
                AND
                    u.id not in (select user_id from users_logs where log_type = 'export_campaign')
                AND 
                     ug.group_id = '2'
                AND
                    u.active = '1'
                AND
                     ug.group_id not in ('1','5')           
                AND
                 FROM_UNIXTIME(u.created_on, '%Y-%m-%d') > '$date_fourdays_ago' 
                order 
                    by u.created_on asc";

                $check_no_expor_list = $this->db->query($sql);
                if($check_no_expor_list->num_rows() > 0){

                    foreach($check_no_expor_list->result_array() as $ct){

                        //check 3 days ago
                        if($ct['date_created'] == $date_threedays_ago){

                             $this->email->from('support@tubemasterpro.com', 'TubeMasterPro');
                            //$this->email->to("{$ct['email']}");  
                             $this->email->to("renefandida@gmail.com");   

                            $this->email->subject("TubeMasterPro Notification");
                            $this->email->message("Hi {$ct['first_name']}, <br>  <br> you haven't exported to adwords for 3 days now. <br><br> Regards, <br> TubeMasterPro Team");  
                            $this->email->send();
                        }
                    
                    }
                } 

            //if they HAVE created 10 lists
            $sql = "select 
                    u.first_name,
                    u.last_name,
                    u.id,
                    u.is_subscribe,
                    u.created_on,
                    u.email,
                    FROM_UNIXTIME(u.created_on, '%Y-%m-%d') as date_created,
                    (select count(id) from targets where user_id = u.id LIMIT 1) as count_targets
                from 
                    users as u
                where
                    (select count(id) from targets where user_id = u.id LIMIT 1) = '10'
                and
                    u.id not in (select user_id from notification_notified where notif_type = 'created_10_list') LIMIT 1";          

                $check_no_expor_list = $this->db->query($sql);
                if($check_no_expor_list->num_rows() > 0){

                    foreach($check_no_expor_list->result_array() as $ct){
                            $data = array(
                               'user_id'                => $ct['id'],
                               'date_added'             => time(),
                               'notif_type'              => 'created_10_list'
                            );
                          //  $this->db->insert('notification_notified', $data);

                        //check 3 days ago
                        if($ct['date_created'] == $date_threedays_ago){

                             $this->email->from('support@tubemasterpro.com', 'TubeMasterPro');
                            //$this->email->to("{$ct['email']}");  
                             $this->email->to("renefandida@gmail.com");   

                            $this->email->subject("TubeMasterPro Notification");
                            $this->email->message("Hi {$ct['first_name']}, <br>  <br> Congratulations, you created 10 lists. <br><br> Regards, <br> TubeMasterPro Team");  
                            $this->email->send();
                        }
                    
                    }
                } 

            //if they HAVE exported to adwords = send email
            $sql = "select 
                    distinct(ul.user_id),
                    u.first_name,
                    u.last_name,
                    u.id,
                    u.is_subscribe,
                    (select count(log_id) from users_logs where user_id = u.id and log_type = 'export_campaign' LIMIT 1) as count_campaign
                from 
                    users_logs as ul,
                    users as u

                where
                    ul.user_id = u.id
                and
                    ul.log_type = 'export_campaign'
                and
                 u.id not in (select user_id from notification_notified where notif_type = 'add_first_campaign')
                and
                (select count(log_id) from users_logs where user_id = u.id and log_type = 'export_campaign' LIMIT 1) = '1' LIMIT 1";    

                $check_first_campaign = $this->db->query($sql);
                if($check_first_campaign->num_rows() > 0){

                    foreach($check_first_campaign->result_array() as $ct){
                            $data = array(
                               'user_id'                => $ct['id'],
                               'date_added'             => time(),
                               'notif_type'              => 'add_first_campaign'
                            );
                           // $this->db->insert('notification_notified', $data);
            
                        //check 3 days ago
                        if($ct['date_created'] == $date_threedays_ago){

                             $this->email->from('support@tubemasterpro.com', 'TubeMasterPro');
                            //$this->email->to("{$ct['email']}");  
                             $this->email->to("renefandida@gmail.com");   

                            $this->email->subject("TubeMasterPro Notification");
                            $this->email->message("Hi {$ct['first_name']}, <br>  <br> Congratulations, you created your first campaign lists. <br><br> Regards, <br> TubeMasterPro Team");  
                            $this->email->send();
                        }
                    
                    }
                } 
            $data = array('notif_date'  => $date_today);
           // $this->db->insert('notification', $data);
		   echo "Success";
            $this->db->trans_complete();
        }else{
	        echo "Exist";
        }

    }

  
}
?>