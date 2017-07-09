 <?php defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends MX_Controller {
   
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
        $this->user  = $this->ion_auth->user()->row();

    }
    
    function index () {
    	$this->admin->check_permission();
 		$user = (array) $this->user;
        $sess_msg       = $this->session->flashdata('msg');
        $sess_msg_type  = $this->session->flashdata('msg_type');
        if($sess_msg){
            $msg = $sess_msg;
            $msg_type = $sess_msg_type;
        }

        //count total paid
        $sql = "select u.is_subscribe,u.id,u.first_name, u.last_name,u.email from users as u,paypal as p where u.id=p.user_id
                and p.p_status in ('ACTIVE','PENDING') and p.ppstatus = '1'";
        $total_paid_users = $this->db->query($sql);
        $o['total_paid_users'] = number_format($total_paid_users->num_rows,0);

        //check cancelled users
        $sql = "select u.is_subscribe,u.id,u.first_name, u.last_name,u.email,p.p_status
                from 
                    users as u,
                    paypal as p,
                    users_groups as g
                where 
                    u.id=p.user_id
                and
                    g.user_id = u.id
                and 
                    p.p_status not in ('ACTIVE','PENDING','CREATED')
                and
                    g.group_id not in ('1','5')";
            $total_cancelled_users = $this->db->query($sql);
            $o['total_cancelled_users'] = number_format($total_cancelled_users->num_rows,0);

        //check total demo users
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
                    ug.group_id not in ('1','5')";

        $qtotal_demo_users = $this->db->query($sql);
        if($qtotal_demo_users->num_rows() == 0){
            $o['total_demo_users'] = 0;
             $o['total_expired_demo'] = 0;
        }
        else
        {
            $total_demo_users = 0;
            $total_expired_demo = 0;
            foreach($qtotal_demo_users->result_array() as $cdu)
            {
                    $end_date = strtotime(date('Y/m/d H:i:s',strtotime('+7 days', $cdu['first_login'])));
                    $time_now = time();
                    $total_end_time = $end_date - $time_now;
                    if($total_end_time > 0){
                       $total_demo_users = $total_demo_users + 1;
                    }
                    if($total_end_time < 0){
                       $total_expired_demo = $total_expired_demo + 1;
                    }
            }
            
            if($total_demo_users == 0){
                $o['total_demo_users'] = 0;
            }else{
                $o['total_demo_users'] = number_format($total_demo_users,0);
            }          
            if($total_expired_demo == 0){
                $o['total_expired_demo'] = 0;
            }else{
                $o['total_expired_demo'] = number_format($total_expired_demo,0);
            }
        }          
        
        
        $o['support_admin_header'] = true;
        $o['msg_type'] = $msg_type;
        $o['msg'] = $msg;
        $o['title'] = "Admin";
        $o['page'] = 'support';
        $o['user'] = $user;
        $o['baseurl'] = $this->baseurl;    
        $this->data['o'] = $o;
        $this->load->view('videoadmin/admin', $this->data);  
    }

    function check_stats(){
    	$this->admin->check_permission();

    	//check all users
    	$sql = "select count(u.id) as total_users
				from 
					users as u,
					users_groups as ug
				where 
					u.id = ug.user_id
				and
					u.active = '1'
				and
					ug.group_id = '2' LIMIT 1";
		$all_users = $this->db->query($sql);
		if($all_users->num_rows() == 0){
			$check_all_users = 0;
		}else{
			$cau = $all_users->row_array();
			$check_all_users = $cau['total_users'];
		}

		//check total paid users
		$sql = "select count(id) as count_active from paypal where ppstatus = '1' and p_status in ('ACTIVE','PENDING') LIMIT 1";
		$check_paid = $this->db->query($sql);
		if($check_paid->num_rows() == 0){
			$total_paid_users = 0;
		}else{
			$tcp = $check_paid->row_array();
			$total_paid_users = $tcp['count_active'];
		}
		$total_conv_rate = (($total_paid_users / $check_all_users) * 100);

		// check signup date
		$date_today = date("Y-m-d",time());
		$date_yesterday = date("Y-m-d",mktime(0,0,0,date("m"),date("d")-1,date("Y")));
		$sql = "select  count(id) as today_signup from users where from_unixtime(created_on, '%Y-%m-%d') = '$date_today' LIMIT 1";
		$check_ts = $this->db->query($sql);
		if($check_ts->num_rows() == 0){
			$signup_today_count = 0;
		}else{
			$tts = $check_ts->row_array();
			$signup_today_count = $tts['today_signup'];
		}
		//check signup yesterday
		$sql = "select  count(id) as yest_signup from users where from_unixtime(created_on, '%Y-%m-%d') = '$date_yesterday' LIMIT 1";
		$check_sy = $this->db->query($sql);
		if($check_sy->num_rows() == 0){
			$signup_yesterday_count = 0;
		}else{
			$csy = $check_sy->row_array();
			$signup_yesterday_count = $csy['yest_signup'];
		}
		$o['signup_today_count'] = $signup_today_count;		
		$o['signup_yesterday_count'] = $signup_yesterday_count;
		$o['date_today'] = date("F d, Y", strtotime($date_today));
		$o['total_conv_rate'] = $total_conv_rate;
		$o['total_paid_users'] = $total_paid_users;
        $o['check_all_users_count'] = $check_all_users;
        $o['baseurl'] = $this->baseurl;

        $o['show_list_stat_report'] = true;
        $this->data['o'] = $o;       

        $this->_render_page('videoadmin/stat/total_users', $this->data);   
    }

    function check_user_name(){
    	$this->admin->check_permission();
    	$show_type = $this->input->post('show_type');

    	if($show_type == "paid"){
    		$sql = "select u.id as user_id,u.is_subscribe,u.id,u.first_name, u.last_name,
                    u.email,p.plan_id,p.return_id, p.id as paypal_id, 
                    u.aff_status, u.is_aff, u.aff_notes 
                    from users as u,paypal as p where u.id=p.user_id
                    and p.p_status in ('ACTIVE','PENDING') and p.ppstatus = '1'";
			$check_users = $this->db->query($sql);
			if($check_users->num_rows() > 0){
			 	
                $count_subscirbe = 0;
                $count_unsubscribe = 0;
                $show_paid_users = array();
                foreach($check_users->result_array() as $cu){

                    $url = $this->baseurl . "paypal.php?p=check_billing_agreement&id=".$cu['return_id'];
                    $subscription_status = strtoupper(file_get_contents( $url ));
                    $paypal_obj = json_decode($subscription_status,true);

                    if($paypal_obj['STATE'] == "ACTIVE" or $paypal_obj['STATE'] == "PENDING"){
                        if($cu['is_subscribe'] == 1){
                            $count_subscirbe = $count_subscirbe + 1;  
                        }else{
                            $count_unsubscribe = $count_unsubscribe + 1;  
                        }
                        $show_paid_users[] = $cu;
                    }else{
                       
                        $data = array(
                           'ppstatus' => '2',
                           'date_cancelled' => time(),
                           'p_status'      => $paypal_obj['STATE']
                        );
                        $this->db->where('id', $cu['paypal_id']);
                        $this->db->update('paypal', $data);

                        /* if user has affiliate, disable it!*/
                        if($cu['is_aff'] != '' and $cu['aff_status'] == 'approved'){
                            $data = array(
                               'aff_notes' => "admin - Subscription status: {$paypal_obj['STATE']} <br>" . $cu['aff_notes'] ,
                               'aff_status'      => 'rejected'
                            );
                            $this->db->where('id', $cu['user_id']);
                            $this->db->update('users', $data);

                            //update user affiliate status 0
                            $sql = $this->db->query("update affiliates set aff_status = '0' where user_id = '{$cu['user_id']}'");
                        }
                    }
                }
                $o['count_subscirbe']    = $count_subscirbe;
                $o['count_unsubscribe']     = $count_unsubscribe;
			 	$o['user_text'] = "Paid Users";
			 	$o['show_paid_users'] = $show_paid_users;
				$o['show_paid_users_name'] = true;
			}
    	}

        if($show_type == "expired_users"){
            $sql = "select u.is_subscribe,u.id,u.first_name, u.last_name,u.email,p.p_status
                from 
                    users as u,
                    paypal as p,
                    users_groups as g
                where 
                    u.id=p.user_id
                and
                    g.user_id = u.id
                and 
                    p.p_status not in ('ACTIVE','PENDING','CREATED')
                and
                    g.group_id not in ('1','5')";
            $check_users = $this->db->query($sql);
            if($check_users->num_rows() > 0){
                
                $count_subscirbe = 0;
                $count_unsubscribe = 0;
                $show_paid_users = array();
                foreach($check_users->result_array() as $cu){
                    if($cu['is_subscribe'] == 1){
                        $count_subscirbe = $count_subscirbe + 1;  
                    }else{
                        $count_unsubscribe = $count_unsubscribe + 1;  
                    }
                    $show_paid_users[] = $cu;
                }

                $o['count_subscirbe']    = $count_subscirbe;
                $o['count_unsubscribe']     = $count_unsubscribe;
                $o['user_text'] = "Ex-paid Users";
                $o['show_paid_users'] = $show_paid_users;
                $o['show_paid_users_name'] = true;
            }
        }

        if($show_type == "demo"){
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
                    ug.group_id not in ('1','5')";

            $check_demo_users = $this->db->query($sql);
            if($check_demo_users->num_rows() > 0){
                $get_demo = array();
                $count_subscirbe = 0;
                $count_unsubscribe = 0;
                $show_paid_users = array();
                foreach($check_demo_users->result_array() as $cdu){
                    $end_date = strtotime(date('Y/m/d H:i:s',strtotime('+7 days', $cdu['first_login'])));

                    $time_now = time();
                    $total_end_time = $end_date - $time_now;
                    //check if already paid
                    if($total_end_time > 0){
                        $sql = "select id from paypal where user_id = '{$cdu['id']}' 
                                and 
                        p_status in ('ACTIVE','PENDING') and ppstatus = '1' LIMIT 1";
                        $check_ifpaid = $this->db->query($sql);
                        if($check_ifpaid->num_rows() == 0){
                            if($cdu['is_subscribe'] == 1){
                                $count_subscirbe = $count_subscirbe + 1;  
                            }else{
                                $count_unsubscribe = $count_unsubscribe + 1;  
                            }
                            $get_demo[] = $cdu;
                        }
                    }
                    
                }
                $o['count_subscirbe']    = $count_subscirbe;
                $o['count_unsubscribe']     = $count_unsubscribe;
                $o['user_text'] = "Active Demo users";
                $o['show_paid_users'] = $get_demo;
                $o['show_paid_users_name'] = true;
            }  
        }      
        if($show_type == "demo_expired"){
                
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
                    ug.group_id not in ('1','5')";

            $check_demo_users = $this->db->query($sql);
            if($check_demo_users->num_rows() > 0){
                $get_demo = array();
                $count_subscirbe = 0;
                $count_unsubscribe = 0;
                $show_paid_users = array();
                foreach($check_demo_users->result_array() as $cdu){
                    $end_date = strtotime(date('Y/m/d H:i:s',strtotime('+7 days', $cdu['first_login'])));

                    $time_now = time();
                    $total_end_time = $end_date - $time_now;    
                    //check if already paid
                    if($total_end_time < 0){
                        $sql = "select id from paypal where user_id = '{$cdu['id']}' 
                                and 
                        p_status in ('ACTIVE','PENDING') and ppstatus = '1' LIMIT 1";
                        $check_ifpaid = $this->db->query($sql);
                        if($check_ifpaid->num_rows() == 0){
                            if($cdu['is_subscribe'] == 1){
                                $count_subscirbe = $count_subscirbe + 1;  
                            }else{
                                $count_unsubscribe = $count_unsubscribe + 1;  
                            }
                            $get_demo[] = $cdu;
                        }
                    }
                    
                }
                $o['count_subscirbe']    = $count_subscirbe;
                $o['count_unsubscribe']     = $count_unsubscribe;
                $o['user_text'] = "Expired demo users";
                $o['show_paid_users'] = $get_demo;
                $o['show_paid_users_name'] = true;
            }
        }

        $o['show_type'] = $show_type;
        $o['baseurl'] = $this->baseurl;
       
        $this->data['o'] = $o;       

        $this->_render_page('videoadmin/stat/total_users', $this->data);   
    }

    function search_users(){
        $this->admin->check_permission();
        $o['show_search_table'] = true;
        $o['baseurl'] = $this->baseurl;
        $this->data['o'] = $o;
        $this->_render_page('videoadmin/stat/search_user', $this->data);       
    }

    function search_result(){
         $this->admin->check_permission();
         $search_val = trim($this->input->post('search_val'));

        $sql = "select id,first_name,last_name,email from users where first_name like 
                '%$search_val%' or last_name like '%$search_val%' or email like '%$search_val%'
                 order by last_name,first_name asc limit 20";


        $search_user = $this->db->query($sql);
        if($search_user->num_rows() == 0){
            echo "no_result_found";
        }else{
            $show_res = array();
            foreach($search_user->result_array() as $su){
                $show_res[] = $su;
            }
            $o['show_res'] = $show_res;
            $o['show_search_result_table'] = true;
            $o['baseurl'] = $this->baseurl;
            $this->data['o'] = $o;
            $this->_render_page('videoadmin/stat/search_user', $this->data);              
        }
    }

    function check_adword_export_user(){
         $this->admin->check_permission();
         $show_type = trim($this->input->post('show_type'));   

        if($show_type == "done") {
                    $sql = "select 
    distinct(ul.user_id),
    u.first_name,
    u.last_name,
    u.id,
    u.is_subscribe
from 
    users_logs as ul,
    users as u

where
    ul.user_id = u.id
and
    ul.log_type = 'export_campaign'
and
    ul.user_id in (select user_id from paypal where p_status in ('PENDING','CREATED','ACTIVE'))
order by ul.log_id asc ";
                    $export_title = "Paid users already use export campaign";
        }else{
            $sql = "select 
                u.first_name,
                u.last_name,
                u.id,
                u.is_subscribe
            from 
                users as u
            where
                u.id in (select user_id from paypal where p_status in ('PENDING','CREATED','ACTIVE'))
            AND
                u.id not in (select user_id from users_logs where log_type = 'export_campaign')";  

                 $export_title = "Paid users not yet use export campaign";
         }
        $show_exp = $this->db->query($sql);

        if($show_exp->num_rows() == 0){
            echo '<div class="alert alert-warning" role="alert">No data found...</div>';
        }else{
            $show_exp_result = array();
            $count_subscirbe = $count_unsubscribe = 0;

            foreach($show_exp->result_array() as $se){

                if($se['is_subscribe'] == 1){
                    $count_subscirbe = $count_subscirbe + 1;  
                 }else{
                    $count_unsubscribe = $count_unsubscribe + 1;  
                }

                $show_exp_result[] = $se;
            }

            $o['count_subscirbe'] = $count_subscirbe;
            $o['count_unsubscribe'] = $count_unsubscribe;
            $o['show_type'] = $show_type;
            $o['export_title'] = $export_title;
            $o['show_res'] = $show_exp_result;
            $o['show_search_result_table'] = true;
            $o['baseurl'] = $this->baseurl;
            $this->data['o'] = $o;
            $this->_render_page('videoadmin/stat/export_user', $this->data);  

        }
    }

    function view_user_log(){
        $this->load->helper('download');
        $this->admin->check_permission();  
        $id   = $this->uri->segment(3);
        $is_download   = $this->uri->segment(4);
        // check users data
        $sql = "select first_name,last_name,created_on,first_login,last_login from users where id = '$id' LIMIT 1";
        $check_user = $this->db->query($sql);
        if($check_user->num_rows() == 0){
            echo "Error: Invalid user, please try again";
        }else{
            $gu = $check_user->row_array();
            $gu['name']         = ucfirst($gu['first_name']) . " " . ucfirst($gu['last_name']);
            $gu['created_on']   = date("M d, Y h:i a", $gu['created_on']);
            $gu['first_login']  = date("M d, Y h:i a", $gu['first_login']);
            $gu['last_login']   = date("M d, Y h:i a", $gu['last_login']);
            $name = $gu['name'];

            /* count login */
            $sql = "select 
(select count(log_type) from users_logs 
where 
user_id = '$id' and log_type = 'login' LIMIT 1) as count_login,
(select count(log_type) from users_logs 
where 
user_id = '$id' and log_type = 'keyword_search' LIMIT 1) as count_keyword_search,
(select count(log_type) from users_logs 
where 
user_id = '$id' and log_type = 'channel_search' LIMIT 1) as count_channel_search,
(select count(log_type) from users_logs 
where 
user_id = '$id' and log_type = 'extract_videos' LIMIT 1) as count_extract_videos,
(select count(log_type) from users_logs 
where 
user_id = '$id' and log_type = 'video_search' LIMIT 1) as count_video_search,
(select count(log_type) from users_logs   
where 
user_id = '$id' and log_type = 'export_campaign' LIMIT 1) as count_export_campaign
from 
users_logs where user_id = '$id' LIMIT 1";
             
            $check_count_logs = $this->db->query($sql);
            if($check_count_logs->num_rows() > 0){
                $gu['ccl'] = $check_count_logs->row_array();
            }
   
            $sql = "select log_type,log_desc,date_added from users_logs where user_id = '$id' order by log_id desc";
            $check_logs = $this->db->query($sql);
            if($check_logs->num_rows() > 0){
                $show_logs = array();
                foreach($check_logs->result_array() as $cl){
                    //$cl['date_added'] = timespan($cl['date_added'], time());
                    $cl['date_added'] = date("Y-m-d h:i a", $cl['date_added']);
                    $show_logs[] = $cl;
                }
                $o['show_logs'] = $show_logs;
                $o['show_logs_table'] = true;

            }
        }
            $o['gu'] = $gu;

            if($is_download == 1){
                header('Content-type: text');
                header('Content-Disposition: attachment; filename="' . $name . '-logs.txt"');
                header("Content-Transfer-Encoding: binary"); 
                header('Pragma: no-cache'); 
                header('Expires: 0');
                $o['download_log_table'] = true;
            }
            else{
                $o['show_log_table'] = true; 
            }
            //header("Content-Type:text/plain");
            $o['is_download'] = $is_download;
            
            $o['baseurl'] = $this->baseurl;
            $this->data['o'] = $o;   
            
            $this->load->view('videoadmin/stat/user_logs', $this->data); 
    }


    function sub_unsubscribe_users(){
        $update_type = $this->input->post('update_type');
        $checked = $this->input->post('checked');

         foreach($checked as $ch){
            if($update_type == "subscribe"){
                $data = array('is_subscribe' => 1);
            }else{
                $data = array('is_subscribe' => 0);
            }
                $this->db->where('id', $ch);
                $this->db->update('users', $data);   
         }
    }

    function email_users(){
       $subject = $this->input->post('subject');
       $message = str_replace("\n", "<br>", $this->input->post('message'));
       $checked = $this->input->post('checked');

            $this->load->library('email');
            /*
            $config['protocol'] = 'smtp';
            $config['smtp_host'] = 'localhost';
            $config['smtp_port'] = '25';
            $config['charset'] = 'iso-8859-1';
            $config['wordwrap'] = TRUE;
            $config['mailtype'] = 'html';
*/
                    $config['protocol'] = 'smtp';
                    $config['smtp_host'] = 'box342.bluehost.com';
                    $config['smtp_user'] = 'nathan@nathanhague.com';
                    $config['smtp_pass'] = '$Wolfman1';
                    $config['smtp_port'] = '26';
                    $config['charset'] = 'iso-8859-1';
                    $config['wordwrap'] = TRUE;
                     $config['mailtype'] = 'html';

            $this->email->initialize($config);

			$count = 0;
            foreach($checked as $ch){

                if($count == 0){
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
	                    //$this->email->to("{$get_email['email']}");  
                        $this->email->to("australiawow@gmail.com");  
	                    $this->email->subject("$subject");
	                    $this->email->message("$msg");  
	                    //$this->email->send();
	                    
	                }
                 }
                 $count++;
            }
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