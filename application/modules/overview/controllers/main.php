<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Main extends MY_Controller {

	protected $user;


    private $yt_dev_key = array(
        "AIzaSyDg1hQx7_9wD0DEbHxkT6bMi7K8kk8uZKs",//-NATE
        "AIzaSyBk4tmWtVhiNL6gYqzGU_tEj85GkStQbK8",//-CHRIS
        "AIzaSyCSNw-ijFgCl-OLcilgEV3PwcAxZ3QLsV0",//-RENE
        "AIzaSyBR-0PCJOPADmueJqL3fK6bn68xITfVNXM",//-MAO
        "AIzaSyAjLi2U439nlc1R4p3fK0F2qIuZXil-6D0",//-EDWIN
    );

    function __construct()
    {
        parent::__construct();
        $this->load->library('authentication', NULL, 'ion_auth');
        $this->lang->load('auth');
        $this->load->helper('language');
        $this->load->library('template');

        $this->template->set_title('Dashboard');
        //$this->template->add_css('datatables/jquery.dataTables.min.css');
        $this->template->add_css('datatables/dataTables.bootstrap.css');
        $this->template->add_css('modules/sidebar_menu.css');
        $this->template->add_css('../js/font-awesome/css/font-awesome.min.css');
        //$this->template->add_js('stupidtable.js');
        $this->template->add_js('datatables/jquery.dataTables.min.js');
        $this->template->add_js('datatables/dataTables.bootstrap.js');
        $this->template->add_js('zeroclipboard/ZeroClipboard.min.js');
        // $this->template->add_js('table2CSV.js');
        $this->template->add_js('numeral.min.js');
        $this->template->add_js('jqtubeutil.min.js');
        $this->template->add_js('modules/app.js');
        $this->template->add_js('modules/dashboard.js');
        $this->template->add_js('modules/uv_popup.js');
         $this->template->add_js('modules/overview.js?v=' . time());
		$this->user = $this->ion_auth->user()->row();

        $this->load->database();
        $this->load->model('target_model');    
        $this->baseurl = $this->config->config['base_url'];    
		/*
echo ($this->session->userdata('user_session_id'));
		die();
*/
        
        if (!$this->ion_auth->logged_in())
        {
            //redirect them to the login page
            redirect('auth/login', 'refresh');
        }
        else if ( $this->ion_auth->in_group(3) ) //remove this elseif if you want to enable this for non-admins
        {
            if ( $this->user->has_review == 1 ) {
		        redirect('subscription', 'refresh');
            }
        }
        else if ( !$this->ion_auth->is_admin() ) {
        // else if ( !$this->ion_auth->in_group(3) ) {
	        //redirect them to the home page because they must be an administrator to view this
            // return show_error('You must be an administrator to view this page.');
            $this->load->model('members_model');
	        $member = $this->members_model->get_subscription_details($this->user->id);
	
	        if ( $member ) {
                $check_status = array('ACTIVE','PENDING');
	        	if (!in_array(strtoupper($member->p_status), $check_status)) {
		        	redirect('subscription', 'refresh');
	        	}
	        }
	        else {
                //check if user has unlimited login

                //check promocode 
                if($this->user->use_promocode != 0){
                $sql = "select discount_amt,is_onetime, option_desc from promo_code where promo_code_id = '". $this->user->use_promocode."' LIMIT 1";
                $check_promo = $this->db->query($sql);
                    if($check_promo->num_rows() > 0){
                        $cp = $check_promo->row_array();
                        if($cp['discount_amt'] == 100 && $cp['is_onetime'] == 0){

                        }else{
                            redirect('subscription', 'refresh');
                        }
                    }
                } else{
		          redirect('subscription', 'refresh');
                }
	        }
        }

        $this->load->model('members_model');
        $count_user_export = $this->members_model->count_user_export();
        if($count_user_export == 0){
            redirect('dashboard', 'refresh');
        }else{
	        redirect('dashboard', 'refresh'); //disable if adwords is fixed
        }        
        
    }



    function generate_ytkey () {
        $key = array_rand($this->yt_dev_key);
        return $this->yt_dev_key[$key];
    }

    public function index(){

        $this->overview();
    }
    public function overview()
    {
        $is_existing_link = $this->uri->segment(4);
        $is_popup_link  = $this->uri->segment(5);
        $is_popup_cid   = $this->uri->segment(6);
        $is_popup_token = $this->uri->segment(7);

        $this->template->set_title('Optimizer');

        $this->template->add_css('jquery-fileupload/jquery.fileupload.css?v='.time());
        $this->template->add_css('jquery-fileupload/jquery.fileupload-ui.css?v='.time());
        $this->template->add_css('modules/compare.css?v='.time());
        
        $this->template->add_js('jqueryui/jquery-ui.min.js?v='.time());
        $this->template->add_js('jquery-fileupload/jquery.fileupload.js?v='.time());
        $this->template->add_js('jquery-fileupload/jquery.iframe-transport.js?v='.time());
        $this->template->add_js('jquery-fileupload/jquery.fileupload-process.js?v='.time());
        $this->template->add_js('jquery-fileupload/jquery.fileupload-validate.js?v='.time());
        //$this->template->add_js('modules/compare.js?v='.time());

        $active = 'create_overview';
        $parent = 'optimizer';

        if($is_existing_link == "existing"){
            $o['header_title'] = "Existing Optimized Campaigns";
            $o['active_sub'] = "existing";
        }else{
            $o['header_title'] = "Create New Optimized Campaign";
            $o['active_sub'] = "new";
        }
        $targets = $this->target_model->get_all( $this->user->id);
        $campaign_list = $this->target_model->get_campaigns( $this->user->id);
        
        //check campaign added on overview
        /*
        $sql = "select count(id) as count_overview from overview_campaign where
            cid = (select cid from overview where user_id = '{$this->user->id}' and status = '1' LIMIT 1) 
            and status = '1' LIMIT 1";
        */

        $sql = "select count(id) as count_overview from overview_campaign 
                where cid in (select id from campaign_list where user_id = '{$this->user->id}') and status = '1' LIMIT 1";
        $check_ov  = $this->db->query($sql);

        if($check_ov->num_rows() == 0){
            $o['add_campaign_overview_table'] = true;
        }else{
            $get_ov = $check_ov->row_array();

           if($get_ov['count_overview'] < count($campaign_list)){
            
            //check campaign if available or not
            $sql = "select cl.id,cl.name,target_lists,video_ads,
                    CASE 
                        WHEN cl.id in (select cid from overview_campaign where status = '1' and 
                        oid in (select id from overview where user_id = '{$this->user->id}' and status = '1')) THEN '0'
                        ELSE '1' 
                    END AS is_available
                from 
                    campaign_list as cl 
                where 
                    cl.user_id = '{$this->user->id}'
                order by is_available desc,name";

            $check_camp = $this->db->query($sql);
            if($check_camp->row_array() == 0){
                $o['msg'] = "No Campaign Available. Please create a campaign";
                $o['msg_type'] = "danger"; 
            }else{
                $show_camp = array();
                foreach($check_camp->result_array() as $cc){
                    if($cc['is_available'] == 1){
                           $explode_target_list = explode(",",$cc['target_lists']);
                            $explode_video_ads = explode(",",$cc['video_ads']);
                            
                            //check target list
                            $total_videos = 0;
                            foreach($explode_target_list as $etl){
                                $sql = "select data from targets where id = '{$etl}' LIMIT 1";
                                $check_d = $this->db->query($sql);
                                if($check_d->num_rows() == 0){
                                    $total_videos = 0;
                                }else{
                                    $show_d = $check_d->row_array();
                                    $unser_data = unserialize($show_d['data']);
                                    $total_videos = $total_videos + count($unser_data);
                                }
                            }
                    
                            $cc['total_target_list']    = count($explode_target_list);
                            $cc['total_video_ads']      = $total_videos;
                     $show_camp[] = $cc;
                    }
                }

//                echo "<pre>";
 //               print_r($show_camp);
  //              die();
                $o['show_camp'] = $show_camp;
                if($is_existing_link != "existing"){
                    $o['add_campaign_overview_table'] = true;
                }
            }
           }else{
            $o['msg'] = "No YouTube campaigns to optimize! Why don’t you go and create one now?";
            $o['msg_type'] = "success"; 
           }
        }

        //check existing orverview
        $sql = "select id,name,date_added,(select count(id) from overview_campaign where oid = overview.id and status = '1' LIMIT 1) as total_campaign
                from 
                    overview where user_id = '{$this->user->id}' and status = '1' order by name asc";

        $check_exist_ov = $this->db->query($sql);
        if($check_exist_ov->num_rows() > 0){
           $show_opt   = array();
           foreach($check_exist_ov->result_array() as $ceo){
            $ceo['date_added'] = date("Y-m-d",time());
            $show_opt[] = $ceo;
           }
           $o['show_opt'] = $show_opt;
            if($is_existing_link == "existing"){
                $o['show_existing_optimizer'] = true;
            }
        }else{
            if($is_existing_link == "existing"){
                $o['msg'] = "No Existing Optimized Campaigns!";
                $o['msg_type'] = "danger"; 
            }
        }
        

        /* check if popup from mao's upload script*/
        if($is_popup_link == "popup"){
            $sql = "select id from overview 
                        where 
                            user_id = '{$this->user->id}' 
                        and 
                            token_key = '$is_popup_token' 
                        and
                            status = '1'
                        LIMIT 1";
            
            $check_pop = $this->db->query($sql);
            if($check_pop->num_rows() > 0){
                $cp = $check_pop->row_array();
                $o['auto_popup_selected_id'] = $cp['id'];
                $o['auto_popup_selected_optimizer'] = true; 
            }
        }
        /* check if from uploaded */
        /*I should have a wizard guiding me here
"Ok we're going to create an optimized campaign from the Adwords file you just uploaded"*/

        if($is_existing_link == "uploaded"){
            $is_forward_cid  = $this->uri->segment(5);
            $o['is_forward_cid'] = $is_forward_cid;
            $o['auto_popup_selected_optimizer'] = true; 
        }
        $data['o'] = $o;
        $this->template->load_view('overview/main', array(
            'nav_sidebar' => Modules::run('main/_nav_sidebar', $active, $parent, $data),
            'content' => Modules::run('main/load_view', $active, $data)
        ));
    }



    public function create_new_optimizer()
    {
        $this->load->helper('security');
        $optimizer_name  = $this->cleanup($this->input->post('optimizer_name'));
        $checked         = $this->input->post('checked');
        
        $this->db->trans_start();
            $token_key = do_hash($this->user->id . "_" . time());
        //insert new optimization
            $data = array(
               'name'                   => $optimizer_name,
               'user_id'                => $this->user->id,
               'date_added'             => time(),
               'status'                 =>  1,
               'token_key'              => $token_key
            );
            $this->db->insert('overview', $data);
            $last_insert_id = $this->db->insert_id();

            foreach($checked as $ch){

                $sql = "select id from overview_campaign where cid = '$ch' and oid = '$last_insert_id' LIMIT 1";
                $check_exist = $this->db->query($sql);
                if($check_exist->num_rows() == 0){
                    $data = array(
                       'oid'                    => $last_insert_id,
                       'cid'                    => $ch,
                       'next_check'             => date('Y-m-d', strtotime('+1 days'))
                    );
                    $this->db->insert('overview_campaign', $data);
                }
            }
           
            $this->db->trans_complete();
            if ($this->db->trans_status() === TRUE)
            {
                echo "success|$last_insert_id|$token_key";
            }else{
                echo 'error';
            }
    }

    public function update_opt_detail(){
        $opt_id = $this->uri->segment(4);

        $this->template->set_title('Update Optimizer');
        $active = 'update_optimizer';
        $parent = '';

        $sql = "select id,name,user_id,status from overview 
                where 
                    user_id = '{$this->user->id}' and id='$opt_id' and status = '1' LIMIT 1";
     $check_det = $this->db->query($sql);
        if($check_det->num_rows() == 0){
            $o['msg'] = "Invalid Optimizer id, please try again. <a href=\"" . $this->baseurl . "overview/main\">Go Back</a>";
            $o['msg_type'] = "danger"; 
        }else{

            //check added 
            $sql = "select cl.id,cl.name,target_lists,video_ads,
                    CASE 
                        WHEN cl.id in (select cid from overview_campaign where status = '1' and 
                        oid in (select id from overview where user_id = '{$this->user->id}' and status = '1')) THEN '0'
                        ELSE '1' 
                    END AS is_available,
                    (select oid from overview_campaign where status = '1' and cid=cl.id LIMIT 1) as overview_id
                from 
                    campaign_list as cl 
                where 
                    cl.user_id = '{$this->user->id}'
                order by is_available desc,name";

            $check_camp = $this->db->query($sql);
            if($check_camp->row_array() == 0){
                $o['msg'] = "No Campaign Available. Please create a campaign";
                $o['msg_type'] = "danger"; 
            }else{
                $show_camp = array();
                foreach($check_camp->result_array() as $cc){
                   
                    //count target list
                     $explode_target_list = explode(",",$cc['target_lists']);
                    $explode_video_ads = explode(",",$cc['video_ads']);

                     //check target list
                    $total_videos = 0;
                    foreach($explode_target_list as $etl){
                        $sql = "select data from targets where id = '{$etl}' LIMIT 1";
                        $check_d = $this->db->query($sql);
                        if($check_d->num_rows() == 0){
                            $total_videos = 0;
                        }else{
                            $show_d = $check_d->row_array();
                            $unser_data = unserialize($show_d['data']);
                            $total_videos = $total_videos + count($unser_data);
                        }
                    }
            
                    $cc['total_target_list'] = count($explode_target_list);
                    $cc['total_video_ads'] = $total_videos;
                    if($cc['overview_id'] == $opt_id && $cc['is_available'] == 0){
                   
                        $cc['is_available'] = 1;
                        $cc['is_checked'] = 1;
                    }

                    if($cc['is_checked'] == 0){
                        $show_camp[] = $cc;
                    }
                }
  
                $this->array_sort_by_column($show_camp, 'is_available', SORT_DESC);
                $o['show_camp'] = $show_camp;
            }


            $o['cd'] = $check_det->row_array();
            $o['show_update_optimize_table'] = true;
        }

        $data['o'] = $o;
        $this->template->load_view('overview/main', array(
            'nav_sidebar' => Modules::run('main/_nav_sidebar', $active, $parent),
            'content' => Modules::run('main/load_view', $active, $data)
        ));

    }

    public function update_optimizer(){
       $optimizer_name  = $this->cleanup($this->input->post('optimizer_name'));
       $checked         = $this->input->post('checked');  
       $oid             = $this->input->post('oid');  
        $this->db->trans_start();
        
        $sql = "select token_key from overview where id = '$oid' and user_id = '{$this->user->id}' LIMIT 1";
        $check_token = $this->db->query($sql);
        if($check_token->num_rows() > 0){
            $get_t = $check_token->row_array();
            $token_key = $get_t['token_key'];
        }

        //update campaign name
        $data = array('name' => $optimizer_name);     
            $this->db->where(array('id'=> $oid, 'user_id' => $this->user->id));
            $this->db->update('overview', $data);

        //update add campaign
            foreach($checked as $ch)
            {
                $sql = "select id from overview_campaign where cid = '$ch' and oid = '$oid' and status = '1' LIMIT 1";
                $check_exist = $this->db->query($sql);
                if($check_exist->num_rows() == 0){
                    $data = array(
                       'oid'                   => $oid,
                       'cid'                    => $ch,
                       'next_check'             => date('Y-m-d', strtotime('+1 days'))
                    );
                    $this->db->insert('overview_campaign', $data);
                }
            }  
        // delete campaign not included
            /*
            $sql = $this->db->query("update overview_campaign set status = '2' where 
                    oid = '$oid' and status = '1' and cid NOT IN ( '" . implode($checked, "', '") . "' )");
            */        
        $this->db->trans_complete();
        if ($this->db->trans_status() === TRUE)
        {            
           echo "success|$oid|$token_key";
        }else{
            echo 'error';
        }
    }

    function delete_optimizer(){
       $optimizer_name  = $this->cleanup($this->input->post('optimizer_name'));
       $checked         = $this->input->post('checked');  
       $oid             = $this->input->post('oid');  
        $this->db->trans_start();
        
        //update campaign name
        $data = array('status' => 2);     
            $this->db->where(array('id'=> $oid, 'user_id' => $this->user->id));
            $this->db->update('overview', $data);

        $sql = $this->db->query("update overview_campaign set status = '2' where 
                    oid = '$oid' and status = '1' and cid  IN ( '" . implode($checked, "', '") . "' )");
        
        $this->db->trans_complete();
        if ($this->db->trans_status() === TRUE)
        {
           echo 'success';
        }else{
            echo 'error';
        }
    }

    function view_opt_detail(){

        $this->load->library('google');
        $client = $this->google;
        
        $client->setDeveloperKey($this->generate_ytkey());
        
        // Define an object that will be used to make all API requests.
        $this->load->library('youtube', $client);
        $youtube = $this->youtube;

        $opt_id = $this->uri->segment(4);
        $this->template->set_title('Optimizer Details');
        $active = 'optimizer_details';
        $parent = '';

        $sql = "select id,name,user_id,status,token_key from overview 
                where 
                    user_id = '{$this->user->id}' and id='$opt_id' and status = '1' LIMIT 1";
     $check_det = $this->db->query($sql);
        if($check_det->num_rows() == 0){
            $o['msg'] = "Invalid Optimizer id, please try again. <a href=\"" . $this->baseurl . "overview/main\">Go Back</a>";
            $o['msg_type'] = "danger"; 
        }else{

            $o['cd'] = $check_det->row_array();

            $sql = "select 
                        oc.id as oc_id,
                        oc.cid,
                        lc.target_lists,
                        lc.name
                    from 
                        overview_campaign as oc,
                        campaign_list as lc,
                        overview as ov
                    where 
                        oc.cid = lc.id
                    and
                        ov.id = oc.oid
                    and
                        ov.user_id = '{$this->user->id}'
                    and
                        oc.oid = '$opt_id' 
                    and 
                        oc.status = '1' order by lc.`name`";

            $check_campaign = $this->db->query($sql);
            if($check_campaign->num_rows() == 0){
            $o['msg'] = "No Campaign found, please try again. <a href=\"$this->baseurl/overview/main\">Go Back</a>";
            $o['msg_type'] = "danger"; 

            }else{
                //check campaign added

                $show_list = array();
                $total_video_list = array();
                $unser_tar_upload = "";
                $no_campaign_notification = array();
                foreach($check_campaign->result_array() as $sl){

                    //check campaign uploaded
                    $sql = "select id,data,cid from campaigns_uploaded where cid = '{$sl['cid']}' order by date_uploaded desc LIMIT 1";

                    $check_campup = $this->db->query($sql);
                    if($check_campup->num_rows() == 0){

                    }
                    else{
                        $cca = $check_campup->row_array();
                        $sl['campaign_uploaded_data'] = $cca;
                        $unser_tar_upload = unserialize($cca['data']);
                        $unser_tar_upload_target = $unser_tar_upload['targets'];
                       // $sl['unser_tar_upload'] = $unser_tar_upload;
                       // $sl['unser_tar_upload_target'] = $unser_tar_upload_target;
                        // -------------------------------------->
                        $explode_target_list = explode(",",$sl['target_lists']);
                        $sql = "select id,name,data from targets 
                            where 
                                user_id = '{$this->user->id}' and id IN ( '" . implode($explode_target_list, "', '") . "' )";

                        $check_targets = $this->db->query($sql);
                        if($check_targets->num_rows() > 0){
                            $sl['target_count'] = $check_targets->num_rows();
                            $show_target = array();
                            $total_video_target = 0;
                            foreach($check_targets->result_array() as $ct){
                                $unser_tar = unserialize($ct['data']);


                                $total_video_list = array_merge($total_video_list,$unser_tar);
                                $ct['unser_tar'] = $unser_tar;
                                
                                $total_video_target = $total_video_target + count($ct['unser_tar']);
                                $ct['count_vid_tar'] = count($ct['unser_tar']);

                                //include uploaded campaign
                               // $ct['unser_tar_upload_target'] = array_unique($unser_tar_upload_target,SORT_REGULAR);
                                $show_target[] = $ct;
                            }
                            $sl['show_target'] = $show_target;
                        }
                        $campaign_included[] = "<span class=\"label label-info\">" . $sl['name'] . " ($total_video_target)</span>";
                        
                        $show_list[] = $sl;

                    } 
                }


                $show_list['included_campaign'] = implode($campaign_included, ", ");

                $merge_target = $total_video_list;
                $no_video_unique = array_unique($merge_target,SORT_REGULAR); 

                $show_list['total_video_list']          = count($merge_target);
                $show_list['total_unique_videos']       = count($no_video_unique);
                $show_list['total_duplicate_videos']    = count($merge_target) - count($no_video_unique);

                //show 
              //  echo "<pre>";
              //  print_r($unser_tar_upload['targets']);
               // echo "<hr>";
               // print_r($show_list);
              //  echo "</pre>";
              //  echo "<hr>";

                $final_output = array();
                $final_output_tpl = array();
                $final_unser = array();
                $insert_yt_clicks = array();
                foreach($show_list as $sl){
                    
                    foreach($sl['show_target'] as $st){
                      
                        foreach($st['unser_tar'] as $ut){
                              $final_tar = array();
                            foreach($unser_tar_upload['targets'] as $utu)
                            {
                                if($ut['ytid'] == $utu['id']){
                                    if($utu['views'] > 0){
                                        //echo "Ari ko d! <br>";
                                        $iyc['id']       = $utu['id'];
                                        $iyc['click']    = $utu['views'];
                                        $insert_yt_clicks[]    = $iyc;
                                    }
                                }else{
                                    $utu['click'] = 0;
                                }
                                $final_tar[] = $utu;
                            }
                           $st['final_targets'][] = $ut;
                        }
                        $final_output_tpl = array_merge($final_output_tpl,$final_tar);
                    }
                    
                    $final_output[] = $sl;
                }



/*
                $uniqueEmails = array();
                foreach($show_list as $array)
                {
                    foreach($array['show_target'][''])
                    if(!in_array($array['email'], $uniqueEmails)
                        $uniqueEmails[] = $array['email'];
                }
*/

                /*
                //check details
                $sql = "select 
                    yvc.vl_id,
                    yvc.opt_id,
                    yvc.youtube_id,
                    yvc.video_title,
                    yvc.video_count,
                    (select vid_count from overview_vid_log where y_vid_c_id = yvc.vl_id order by date_added desc LIMIT 1) as latest_count,
                    (select date_added from overview_vid_log where y_vid_c_id = yvc.vl_id order by SUBDATE(date_added,1)  LIMIT 1) as date_yesterday,
                    ((select vid_count from overview_vid_log where y_vid_c_id = yvc.vl_id and SUBDATE(date_added,1) order by SUBDATE(date_added,1) LIMIT 1) - yvc.video_count) as Yesterday_count,
                    ((select vid_count from overview_vid_log where y_vid_c_id = yvc.vl_id order by date_added desc LIMIT 1) - yvc.video_count) as Total_count,
                    (select date_added from overview_vid_log where y_vid_c_id = yvc.vl_id order by date_added desc LIMIT 1) as latest_log_date
                from 
                    youtube_vid_count as yvc
                where
                    yvc.opt_id = '$opt_id'
                and
                    yvc.vl_status = '1'
                GROUP BY 
                    yvc.youtube_id
                order by 
                        Total_count desc";

                $check_data = $this->db->query($sql);
                if($check_data->num_rows() == 0){
                    $o['no_accurate_data_text'] = true;
                }else{
                    //get total count
                    $get_total_result = $check_data->num_rows();
                    $percentage = ceil(($get_total_result * 0.10));
                    //$percentage = 5;
                    $show_data = array();
                    $count = 0;
                    foreach($check_data->result_array() as $cd){
                        if($count < $percentage ){
 
                            $videosResponse = $youtube->videos->listVideos('statistics, snippet', array(
                            'id' => $cd['youtube_id'],
                            ));

                            foreach ($videosResponse['items'] as $key => $value) {
                                $video_count = $value['modelData']['statistics']['viewCount'] - $cd['video_count'];
                                $videoTitle  = $value['snippet']['title'];
                                $videoThumb  = $value['snippet']['thumbnails']['default']['url'];
                                $cd['live_count']   = $video_count;
                                $cd['thumbnail']    = $videoThumb;
                                $cd['ads_count']    = 0;
                            }   
                            $show_data[] = $cd;
                            $video_count = 0;

                        }
                        $count++;
                    }
                
                    $o['date_today'] = date("F d, Y",time());
                    $o['show_data'] = $show_data;
                    $o['show_data_table'] = true;
   
                    // $o['no_accurate_data_text'] = true;
                }
                */

                    $show_data = array();
                    $count = 0;
                    $percentage = ceil((count($final_output_tpl) * 0.10));

                    foreach($final_output_tpl as $cd){
                         if($count < $percentage ){

                            //check foreach if has click
                            foreach($insert_yt_clicks as $iyc){
                                if($cd['id'] == $iyc['id']){
                                    $cd['click'] = $iyc['click'];
                                }
                            }
                           // $cd['video_title'] = urldecode($cd['title']);
                            $cd['youtube_id'] = $cd['id'];
      
                            $videosResponse = $youtube->videos->listVideos('statistics, snippet', array(
                            'id' => $cd['id'],
                            ));

                            
                            foreach ($videosResponse['items'] as $key => $value) {
                                $video_count = $value['modelData']['statistics']['viewCount'];
                                $video_title  = $value['snippet']['title'];
                                $videoThumb  = $value['snippet']['thumbnails']['default']['url'];
                                 $cd['video_title'] = $video_title;
                                $cd['thumbnail']    = $videoThumb;
                                $cd['live_count']   = $video_count;
                                $cd['ads_count']    = 0;
                            } 
    
                            if($cd['click'] > 0){
                                $show_data[] = $cd;
                            }
                            $video_count = 0;
                        }
                        $count++;

                    }

               // echo "<pre>";
               //  print_r($show_data);
               //  echo "<hr>";
               //  print_r($unser_tar_upload['targets']);
               //  echo "</pre>";
               //  echo "<hr>";
               //  die();

                    $this->array_sort_by_column($show_data, 'click', SORT_DESC);

                $o['date_today'] = date("F d, Y",time());
                $o['show_data'] = $show_data;
                $o['show_data_table'] = true;
                $o['show_list'] = $show_list;
                $o['show_details_optimize_table'] = true;
            }
        }

        $o['opt_id'] = $opt_id;
        $o['active'] = "existing";
        $o['active_sub'] = "existing";
        $data['o'] = $o;
        $this->template->load_view('overview/main', array(
            'nav_sidebar' => Modules::run('main/_nav_sidebar', $active, $parent, $data),
            'content' => Modules::run('main/load_view', $active, $data)
        ));
    }


function in_array_r($needle, $haystack, $strict = false) {
    foreach ($haystack as $item) {
        if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && in_array_r($needle, $item, $strict))) {
            return true;
        }
    }

    return false;
}

    function generate_optimizer_report(){

        $this->load->library('google');
        $client = $this->google;
        
        $client->setDeveloperKey($this->generate_ytkey());
        
        // Define an object that will be used to make all API requests.
        $this->load->library('youtube', $client);
        $youtube = $this->youtube;

        $this->db->trans_start();
        $date_checked = date("Y-m-d");//"2015-06-22";
        $is_force       = $this->input->post('is_force');
        $opt_id         = $this->input->post('opt_id');

        if($is_force == 1 && $opt_id != ""){
            $sql = "select 
                    oc.id,
                    oc.next_check,
                    (select date_added from overview_vid_log where ov_camp_id = oc.id order by date_added desc LIMIT 1) as latest_date
                from 
                    overview_campaign as oc 
                where 
                    oc.oid = '$opt_id'
                and
                    oc.status = '1'
                LIMIT 1";

            $check_f = $this->db->query($sql);
            if($check_f->num_rows() == 0){
                echo "error";
                die();
            }else{
                $get_date = $check_f->row_array();
  
                $new_date_checked = date("Y-m-d",time());
                $sql = "select 
                            yvc.youtube_id,
                            yvc.opt_id,
                            yvc.oc_id,
                            yvc.vl_id,
                            oc.next_check,
                            yvc.video_count
                        from
                            youtube_vid_count as yvc,
                            overview_campaign as oc
                        where
                            yvc.oc_id = oc.id
                        and
                            oc.next_check = '{$get_date['next_check']}'";

                if( $get_date['latest_date'] == null){
                    $date_checked = $new_date_checked;
                }else{
                    $date_checked = $get_date['latest_date']; //if has the same, it will update      
                }                    
            }
        }else{
        
            $sql = "select 
                        yvc.youtube_id,
                        yvc.opt_id,
                        yvc.oc_id,
                        yvc.vl_id,
                        oc.next_check,
                        yvc.video_count
                    from
                        youtube_vid_count as yvc,
                        overview_campaign as oc
                    where
                        yvc.oc_id = oc.id
                    and
                        oc.next_check <= '$date_checked'";
        }

        $check_count = $this->db->query($sql);
        if($check_count->num_rows() > 0){

            foreach($check_count->result_array() as $ra){

                $youtube_id          = $ra['youtube_id'];
                $videosResponse = $youtube->videos->listVideos('statistics', array(
                'id' => $youtube_id,
                ));
                $video_count = 0;
                foreach ($videosResponse['items'] as $key => $value) {
                    $video_count    = $value['modelData']['statistics']['viewCount'];      
                    $ra['new_video_count'] = $video_count;  


                }
                
                //insert 
                $sql = "select ov_log_id,vid_count from overview_vid_log 
                            where 
                        date_added = '{$date_checked}' and y_vid_c_id = '{$ra['vl_id']}' LIMIT 1";
                $check_log = $this->db->query($sql);
                if($check_log->num_rows() == 0){
                    $data = array(
                       'ov_camp_id'                 => $ra['oc_id'],
                       'y_vid_c_id'                 => $ra['vl_id'],
                       'vid_count'                  => $ra['new_video_count'],
                       'date_added'                 => $date_checked
                    );
                    $this->db->insert('overview_vid_log', $data);   

                }else{
                    $get_total_vid_count = $check_log->row_array();
                    if($ra['new_video_count'] != $get_total_vid_count['vid_count']){
                        $sql = $this->db->query("update overview_vid_log set vid_count = '{$ra['new_video_count']}' where 
                            ov_log_id = '{$get_total_vid_count['ov_log_id']}'");
                    }
                }
                $new_date_update = date('Y-m-d', strtotime('+1 days'));
                $sql = $this->db->query("update overview_campaign set next_check = '$new_date_update' where 
                    id = '{$ra['oc_id']}' and status = '1'");

            }

            if($is_force == 1 && $opt_id != ""){
                $this->load->library('email');
                $config['protocol'] = 'smtp';
                $config['smtp_host'] = 'box342.bluehost.com';
                $config['smtp_user'] = 'nathan@nathanhague.com';
                $config['smtp_pass'] = '$Wolfman1';
                $config['smtp_port'] = '26';
                $config['charset'] = 'iso-8859-1';
                $config['wordwrap'] = TRUE;
                $config['mailtype'] = 'html';

                $this->email->initialize($config);

                $subject = "Optimizer data complete";

                $this->email->from('support@tubemasterpro.com', 'TubeTargetReview');
                $this->email->from("{$this->user->email}", "$name");
                $this->email->subject("$subject");
                $this->email->message("Gathering of data from youtube count completed. you can check your Optimizer for result.");  
                $this->email->send();
            }
          
        }else{
            echo "no data to update";
            die();
        }

        $this->db->trans_complete();
    }
    function curl_youtube_views($user_id,$overview_id,$overview_key){
        //set POST variables
        $url =  $this->baseurl . "overview/main/generate_youtube_view/$user_id/$overview_id/$overview_key";
        file_get_contents("$url");
        echo "success";
            
    }
    function generate_youtube_view()
    {

        $this->load->library('google');
        $client = $this->google;
        
        $client->setDeveloperKey($this->generate_ytkey());
        
        // Define an object that will be used to make all API requests.
        $this->load->library('youtube', $client);
        $youtube = $this->youtube;
        
        $user_id        = $this->user->id;
        $overview_id    = $this->input->post('overview_id');
        $overview_key   = $this->input->post('overview_key');

        $this->db->trans_start();

        //check optimization
        $sql  = "select 
                    o.id,
                    oc.id as oc_id,
                    oc.cid,
                    cl.target_lists
                from 
                    overview as o,
                    overview_campaign as oc,
                    campaign_list as cl
                where 
                    o.id = oc.oid
                and
                    oc.cid = cl.id
                and
                    o.user_id = '$user_id' 
                and 
                    o.id = '$overview_id'
                and
                    cl.user_id = '$user_id'
                and 
                    o.token_key = '$overview_key' 
                and 
                    o.`status` = '1'
                and
                    oc.`status` = '1'";

        $check_opt = $this->db->query($sql);
        if($check_opt->num_rows() > 0){
            
            foreach($check_opt->result_array() as $sh_opt){
            $exp_targ = explode(",",$sh_opt['target_lists']);

            $sql = "select t.id,t.name,t.data 
                from 
                    targets as t 
                where 
                    t.id not in (select target_id from youtube_vid_count where user_id = '$user_id' and oc_id = '$overview_id' and vl_status = '1' )
                and
                    t.user_id = '$user_id' 
                and 
                    t.id IN ( '" . implode($exp_targ, "', '") . "' )";

                $check_targ = $this->db->query($sql);
                if($check_targ->num_rows() > 0){
                    $unser_tar= array();
                    foreach($check_targ->result_array() as $ct){
                        $unser_tar = unserialize($ct['data']);
                        //for($i=0;$i<1; $i++){
                        for($i=0;$i<count($unser_tar); $i++){

                           $youtube_id          = $unser_tar[$i]['ytid'];
                           $youtube_title       = $unser_tar[$i]['title'];
                            $videosResponse = $youtube->videos->listVideos('snippet, statistics', array(
                            'id' => $youtube_id,
                            ));
                            $video_count = 0;
                            foreach ($videosResponse['items'] as $key => $value) {
                                $video_count = $value['modelData']['statistics']['viewCount'];
                                $videoTitle  = $value['snippet']['title'];

                                   $data = array(
                                       'opt_id'     => $overview_id,
                                       'oc_id'      => $sh_opt['oc_id'],
                                       'user_id'    => $user_id,
                                       'camp_id'    => $sh_opt['cid'],
                                       'target_id'  => $ct['id'],
                                       'video_title' => $videoTitle,
                                       'youtube_id' => $youtube_id,
                                       'video_count'=> $video_count
                                    );
                                   $this->db->insert('youtube_vid_count', $data);
                                    
                           }
                        }
                    }
                }
            }

        }
        $this->db->trans_complete();
    }

    public function addNewCampaignToOptimizer(){
       $optimizer_name  = $this->cleanup($this->input->post('optimizer_name'));
       $campaign_id         = $this->input->post('campaign_id');  
       $optimizer_id             = $this->input->post('optimizer_id');  
        $this->db->trans_start();
        
        $sql = "select token_key from overview where id = '$optimizer_id' and user_id = '{$this->user->id}' LIMIT 1";
        $check_token = $this->db->query($sql);
        if($check_token->num_rows() > 0){
            $get_t = $check_token->row_array();
            $token_key = $get_t['token_key'];
        }


        //update add campaign
        $sql = "select id from overview_campaign where cid = '$campaign_id' and oid = '$optimizer_id' and status = '1' LIMIT 1";
        $check_exist = $this->db->query($sql);
        if($check_exist->num_rows() == 0){
            $data = array(
               'oid'                   => $optimizer_id,
               'cid'                    => $campaign_id,
               'next_check'             => date('Y-m-d', strtotime('+1 days'))
            );
            $this->db->insert('overview_campaign', $data);
        }  
        $this->db->trans_complete();
        if ($this->db->trans_status() === TRUE)
        {            
           echo "success|$optimizer_id|$token_key";
        }else{
            echo 'error';
        }
    }


    function create_the_optimized_campaign(){
        $optimizer_id         = $this->input->post('opt_id');  

        $sql = "select id,name,user_id,status,token_key from overview 
                where 
                    user_id = '{$this->user->id}' and id='$optimizer_id' and status = '1' LIMIT 1";
        $check_det = $this->db->query($sql);
        if($check_det->num_rows() == 0){
            echo "error";
        }else{

            $cd = $check_det->row_array();
            $sql = "select 
                        oc.id as oc_id,
                        oc.cid,
                        oc.oid,
                        lc.target_lists,
                        lc.name,
                        CASE 
                        WHEN oc.cid in (select cid from campaigns_uploaded) THEN '0'
                        ELSE '1' 
                        END AS is_available
                    from 
                        overview_campaign as oc,
                        campaign_list as lc,
                        overview as ov
                    where 
                        oc.cid = lc.id
                    and
                        ov.id = oc.oid
                    and
                        ov.user_id = '{$this->user->id}'
                    and
                        oc.oid = '$optimizer_id' 
                    and 
                        oc.status = '1' order by is_available desc, lc.`name` asc";

            $check_campaign = $this->db->query($sql);
            if($check_campaign->num_rows() == 0){
            $o['msg'] = "No Campaign found, please try again. <a href=\"$this->baseurl/overview/main\">Go Back</a>";
            $o['msg_type'] = "danger"; 

            }else{
                //check campaign added
                $show_av = array();
                $av_count = 0;
                foreach($check_campaign->result_array() as $cc){
                    $show_av[] = $cc;
                    if($cc['is_available'] == 1){
                        $av_count++;
                    }
                }
                if($av_count == 0){
                    echo "success";
                   
                }else{
                    $this->o = $show_av;
                    $this->load->view('div/no_upload_campaign', $this->o);  

                }
            }
        }
    }

    function checkGenerateCampaign(){
        
        $optimizer_id         = $this->input->post('opt_id'); 
        $sql = "select 
                    cl.id,
                    o.token_key,
                    o.id as optimizer_id
                from 
                    campaign_list as cl,
                    overview as o
                where 
                    cl.optimizer_id = o.id
                and
                    cl.optimizer_id =  '$optimizer_id' 
                and 
                    cl.user_id = '{$this->user->id}' LIMIT 1";

        $check_data = $this->db->query($sql);
         if($check_data->num_rows() == 0){
            echo "generate_new";
         }else{
            $show_d = $check_data->row_array();
            
            /* check campaign under this */
            $sql = "select 
                        oc.id as oc_id,
                        oc.cid,
                        oc.oid,
                        lc.target_lists,
                        lc.name,
                        CASE 
                        WHEN oc.cid in (select cid from campaigns_uploaded) THEN '0'
                        ELSE '1' 
                        END AS is_available
                    from 
                        overview_campaign as oc,
                        campaign_list as lc,
                        overview as ov
                    where 
                        oc.cid = lc.id
                    and
                        ov.id = oc.oid
                    and
                        ov.user_id = '{$this->user->id}'
                    and
                        oc.oid = '$optimizer_id' 
                    and 
                        oc.status = '1' order by is_available desc, lc.`name` asc";

            $check_campaign = $this->db->query($sql);
            if($check_campaign->num_rows() == 0){
                echo 'generate_new';
            }else{
                //check campaign added
                $show_av = array();
                $av_count = 0;
                foreach($check_campaign->result_array() as $cc){
                    $show_av[] = $cc;
                }
                $show_d['list_av'] = $show_av;
            }
            $this->data = $show_d;
            $this->load->view('div/existing_campaign', $this->data); 
        }
    }
    function GenerateNewCampaign(){

        $this->load->library('google');
        $client = $this->google;
        
        $client->setDeveloperKey($this->generate_ytkey());
        
        // Define an object that will be used to make all API requests.
        $this->load->library('youtube', $client);
        $youtube = $this->youtube;

        $this->db->trans_start();

        $optimizer_id       = $this->input->post('opt_id');  
        $local_date         = strtotime($this->input->post('local_date'));
        $local_date_target  = date("FdY",$local_date);  
        $create_local_start_date    = date("Y-m-d",$local_date);  
        $sql = "select name,token_key from overview where id =  '$optimizer_id' and status = '1' and user_id = '{$this->user->id}' LIMIT 1";

         $check_data = $this->db->query($sql);
         if($check_data->num_rows() == 0){
            echo "error";
         }else{
            $cdata = $check_data->row_array();
            $token_key = $cdata['token_key'];
        }

                        $sql = "select 
                        oc.id as oc_id,
                        oc.cid,
                        lc.target_lists,
                        lc.video_ads,
                        lc.name
                    from 
                        overview_campaign as oc,
                        campaign_list as lc,
                        overview as ov
                    where 
                        oc.cid = lc.id
                    and
                        ov.id = oc.oid
                    and
                        ov.user_id = '{$this->user->id}'
                    and
                        oc.oid = '$optimizer_id' 
                    and 
                        oc.status = '1' order by lc.`name`";
                        
            $check_campaign = $this->db->query($sql);
            if($check_campaign->num_rows() > 0){
                //check campaign added
                $show_list = array();
                $total_video_list = array();
                $unser_tar_upload = "";
                $show_video_ads = "";
                $comb_video_ads = "";
                foreach($check_campaign->result_array() as $sl){

                    //check campaign uploaded
                    $sql = "select id,data,cid from campaigns_uploaded where cid = '{$sl['cid']}' order by date_uploaded desc LIMIT 1";
                    $check_campup = $this->db->query($sql);
                    if($check_campup->num_rows() > 0){
                        $cca = $check_campup->row_array();
                        $sl['campaign_uploaded_data'] = $cca;
                        $unser_tar_upload = unserialize($cca['data']);

                        $unser_tar_upload_target = $unser_tar_upload['targets'];
                       // $sl['unser_tar_upload'] = $unser_tar_upload;
                       // $sl['unser_tar_upload_target'] = $unser_tar_upload_target;
                        // -------------------------------------->
                        $show_video_ads = $sl['video_ads'];
                       
                        $explode_target_list = explode(",",$sl['target_lists']);
                        
                        $sql = "select id,name,data from targets 
                            where 
                                user_id = '{$this->user->id}' and id IN ( '" . implode($explode_target_list, "', '") . "' )";
                            
                        //$sql = "select id,name,data from targets where user_id = '1' and id IN ( '903', '908' )";

                        $check_targets = $this->db->query($sql);
                        if($check_targets->num_rows() > 0){
                            $sl['target_count'] = $check_targets->num_rows();
                            $show_target = array();
                            $total_video_target = 0;
                            foreach($check_targets->result_array() as $ct){

                                $unser_tar = unserialize($ct['data']);
                                $unser_tar_add = array();
                                foreach($unser_tar as $unt){
                                   // $unt[''] = 
                                    $unser_tar_add[] = $unt;
                                }

                                $total_video_list = array_merge($total_video_list,$unser_tar_add);
                                $ct['unser_tar'] = $unser_tar;
                            
                                //include uploaded campaign
                               // $ct['unser_tar_upload_target'] = array_unique($unser_tar_upload_target,SORT_REGULAR);
                                $show_target[] = $ct;
                            }
                            $sl['show_target'] = $show_target;
                        }
                        $campaign_included[] = "<span class=\"label label-info\">" . $sl['name'] . " ($total_video_target)</span>";
                        
                        $show_list[] = $sl;

                    } 
                     $comb_video_ads = implode($show_video_ads, ",");
                }
                $show_list['included_campaign'] = implode($campaign_included, ", ");

                //Check youtube targets

                $final_output = array();
                $final_output_tpl = array();
                $final_unser = array();
                $insert_yt_clicks = array();
                $insert_final = array();
                foreach($show_list as $sl){
                    
                    foreach($sl['show_target'] as $st){
                      
                        foreach($st['unser_tar'] as $ut){
                            $final_tar = array();
                            foreach($unser_tar_upload['targets'] as $utu)
                            {
                                if($ut['ytid'] == $utu['id']){
                                    if($utu['views'] > 0){
                                        //echo "Ari ko d! <br>";
                                        $iyc['tgroup']   = $utu['tgroup'];
                                        $iyc['id']       = $utu['id'];
                                        $iyc['views']    = $utu['views'];
                                        $iyc['title']    = $ut['title'];
                                        $insert_yt_clicks[$utu['tgroup']][]    = $iyc;
                                        
                                    }
                                }
                             //   $final_tar[] = $utu;
                            }
                         //  $st['final_targets'][] = $ut;
                        }
                       // $final_output_tpl = array_merge($final_output_tpl,$final_tar);
                    }
                    
                   // $final_output[] = $sl;
                }

                $show_data['campaign_name'] = "Optimized " . $cdata['name'];
                $show_data['target_name']   = "Optimized Campaign-" . $local_date_target . "-" . $optimizer_id;


                //get 10% best performing of each target
                $count_vide_per_target = 0;
                $percentage = 0;
                
                $final_video_for_target = array();
                foreach($insert_yt_clicks as $iyc){
                    $count_vide_per_target = count($iyc);
                    $percentage = ceil(($count_vide_per_target * 0.10));
                    $count = 0;
                    foreach($iyc as $i){

                        if($count_vide_per_target < 10){
                                $inc_vd['ytid']    = $i['id'];
                                $inc_vd['title']   = $i['title'];
                                $final_video_for_target[] = $inc_vd;
                        }
                        else{
                            if($count < $percentage ){
                                $inc_vd['ytid']    = $i['id'];
                                $inc_vd['title']   = $i['title'];
                                $final_video_for_target[] = $inc_vd;
                            }
                        }
                        $count++;
                    }
                }

                //serialized all youtube video
                $data_ser = serialize($final_video_for_target);

            //check if user already create a campaign...
            $sql = "select id,optimizer_id,target_lists from campaign_list 
                        where 
                    optimizer_id = '$optimizer_id' and user_id = '{$this->user->id}' LIMIT 1";
            $check_opt = $this->db->query($sql);
            if($check_opt->num_rows() == 0){
                //create target
                $data = array(
                   'data'                   => $data_ser,
                   'user_id'                => $this->user->id,
                   'name'                   => $show_data['target_name'],
                   'status'                 => 0,
                   'is_optimized'           => 1
                );
               
                $this->db->insert('targets', $data);
                $last_target_id = $this->db->insert_id();

                //create campaign
                $data = array(
                   'name'                   => $show_data['campaign_name'],
                   'target_lists'           => $last_target_id,
                  // 'video_ads'              => $comb_video_ads,
                   'user_id'                => $this->user->id,
                   'optimizer_id'           => $optimizer_id,
                   'language'               => 'English',
                   'start_date'             => $create_local_start_date,//date('Y-m-d'),
                   'end_date'               => '#N/A',
                   'mbm_sign'               => 0,
                   'mbm_value'              => 0,
                   'age'                    => 'Unknown,18 - 24,25 - 34,35 - 44,45 - 54,55 - 64,65+',
                   'gender'                 => 'Male,Female,Unknown',
                   'delivery_method'        => 'standard',
                   'countries'              => 'Australia,United Kingdom,United States of America',
                   'daily_budget'           => 0,
                   'max_cpv'                => 0.00,
                   'video_ads'              => "null"
                );
                $this->db->insert('campaign_list', $data);          
            }else{
                $check_camp_list = $check_opt->row_array();
                //update campaign name
                $data = array('data' => $data_ser);     
                $this->db->where(array('id'=> $check_camp_list['target_lists'], 'user_id' => $this->user->id));
                $this->db->update('targets', $data);

            }

            $this->db->trans_complete();
            //echo "success";
            echo $token_key;
         }
  die();
    }

function GenerateNewCampaignTest(){
    echo "For testing purposes only...";
die();
        $this->load->library('google');
        $client = $this->google;
        
        $client->setDeveloperKey($this->generate_ytkey());
        
        // Define an object that will be used to make all API requests.
        $this->load->library('youtube', $client);
        $youtube = $this->youtube;

        $this->db->trans_start();

        $optimizer_id         = $this->input->post('opt_id');  
        $optimizer_id = 8;

        $sql = "select name,token_key from overview where id =  $optimizer_id and status = '1' and user_id = '{$this->user->id}' LIMIT 1";

         $check_data = $this->db->query($sql);
         if($check_data->num_rows() == 0){
            echo "error";
         }else{
            $cdata = $check_data->row_array();
            $token_key = $cdata['token_key'];
        }

                        $sql = "select 
                        oc.id as oc_id,
                        oc.cid,
                        lc.target_lists,
                        lc.video_ads,
                        lc.name
                    from 
                        overview_campaign as oc,
                        campaign_list as lc,
                        overview as ov
                    where 
                        oc.cid = lc.id
                    and
                        ov.id = oc.oid
                    and
                        ov.user_id = '{$this->user->id}'
                    and
                        oc.oid = '$optimizer_id' 
                    and 
                        oc.status = '1' order by lc.`name`";
                        
            $check_campaign = $this->db->query($sql);
            if($check_campaign->num_rows() > 0){
                //check campaign added
                $show_list = array();
                $total_video_list = array();
                $unser_tar_upload = "";
                $show_video_ads = "";
                $comb_video_ads = "";
                foreach($check_campaign->result_array() as $sl){

                    //check campaign uploaded
                    $sql = "select id,data,cid from campaigns_uploaded where cid = '{$sl['cid']}' order by date_uploaded desc LIMIT 1";
                    $check_campup = $this->db->query($sql);
                    if($check_campup->num_rows() > 0){
                        $cca = $check_campup->row_array();
                        $sl['campaign_uploaded_data'] = $cca;
                        $unser_tar_upload = unserialize($cca['data']);

                        $unser_tar_upload_target = $unser_tar_upload['targets'];
                       // $sl['unser_tar_upload'] = $unser_tar_upload;
                       // $sl['unser_tar_upload_target'] = $unser_tar_upload_target;
                        // -------------------------------------->
                        $show_video_ads = $sl['video_ads'];
                       
                        $explode_target_list = explode(",",$sl['target_lists']);
                        /*
                        $sql = "select id,name,data from targets 
                            where 
                                user_id = '{$this->user->id}' and id IN ( '" . implode($explode_target_list, "', '") . "' )";
                                */
                        $sql = "select id,name,data from targets where user_id = '1' and id IN ( '903', '908' )";

                        $check_targets = $this->db->query($sql);
                        if($check_targets->num_rows() > 0){
                            $sl['target_count'] = $check_targets->num_rows();
                            $show_target = array();
                            $total_video_target = 0;
                            foreach($check_targets->result_array() as $ct){

                                $unser_tar = unserialize($ct['data']);
                                $unser_tar_add = array();
                                foreach($unser_tar as $unt){
                                   // $unt[''] = 
                                    $unser_tar_add[] = $unt;
                                }

                                $total_video_list = array_merge($total_video_list,$unser_tar_add);
                                $ct['unser_tar'] = $unser_tar;
                            
                                //include uploaded campaign
                               // $ct['unser_tar_upload_target'] = array_unique($unser_tar_upload_target,SORT_REGULAR);
                                $show_target[] = $ct;
                            }
                            $sl['show_target'] = $show_target;
                        }
                        $campaign_included[] = "<span class=\"label label-info\">" . $sl['name'] . " ($total_video_target)</span>";
                        
                        $show_list[] = $sl;

                    } 
                     $comb_video_ads = implode($show_video_ads, ",");
                }
                $show_list['included_campaign'] = implode($campaign_included, ", ");

                //Check youtube targets

                $final_output = array();
                $final_output_tpl = array();
                $final_unser = array();
                $insert_yt_clicks = array();
                $insert_final = array();
                foreach($show_list as $sl){
                    
                    foreach($sl['show_target'] as $st){
                      
                        foreach($st['unser_tar'] as $ut){
                            $final_tar = array();
                            foreach($unser_tar_upload['targets'] as $utu)
                            {
                                if($ut['ytid'] == $utu['id']){
                                    if($utu['views'] > 0){
                                        //echo "Ari ko d! <br>";
                                        $iyc['tgroup']   = $utu['tgroup'];
                                        $iyc['id']       = $utu['id'];
                                        $iyc['views']    = $utu['views'];
                                        $iyc['title']    = $ut['title'];
                                        $insert_yt_clicks[$utu['tgroup']][]    = $iyc;
                                        
                                    }
                                }
                             //   $final_tar[] = $utu;
                            }
                         //  $st['final_targets'][] = $ut;
                        }
                       // $final_output_tpl = array_merge($final_output_tpl,$final_tar);
                    }
                    
                   // $final_output[] = $sl;
                }

                $show_data['campaign_name'] = "Optimized " . $cdata['name'];
                $show_data['target_name']   = "Optimized Campaign-" . date("FdY",time()) . "-" . $optimizer_id;


                //get 10% best performing of each target
                $count_vide_per_target = 0;
                $percentage = 0;


                $final_video_for_target = array();
                foreach($insert_yt_clicks as $iyc){
                    $count_vide_per_target = count($iyc);
                    $percentage = ceil(($count_vide_per_target * 0.10));
                    $count = 0;

                    foreach($iyc as $i){

                        if($count_vide_per_target < 10){
                                $inc_vd['ytid']    = $i['id'];
                                $inc_vd['title']   = $i['title'];
                                $final_video_for_target[] = $inc_vd;
                        }
                        else{
                            if($count < $percentage ){
                                $inc_vd['ytid']    = $i['id'];
                                $inc_vd['title']   = $i['title'];
                                $final_video_for_target[] = $inc_vd;
                            }
                        }
                        $count++;
                    } 
                }


                //serialized all youtube video
                $data_ser = serialize($final_video_for_target);

            //check if user already create a campaign...
            $sql = "select id,optimizer_id,target_lists from campaign_list 
                        where 
                    optimizer_id = '$optimizer_id' and user_id = '{$this->user->id}' LIMIT 1";
            $check_opt = $this->db->query($sql);
            if($check_opt->num_rows() == 0){
                //create target
                $data = array(
                   'data'                   => $data_ser,
                   'user_id'                => $this->user->id,
                   'name'                   => $show_data['target_name'],
                   'status'                 => 0,
                   'is_optimized'           => 1
                );
                echo "<pre>";
               print_r($data);
               // $this->db->insert('targets', $data);
                $last_target_id = $this->db->insert_id();

                //create campaign
                $data = array(
                   'name'                   => $show_data['campaign_name'],
                   'target_lists'           => $last_target_id,
                  // 'video_ads'              => $comb_video_ads,
                   'user_id'                => $this->user->id,
                   'optimizer_id'           => $optimizer_id,
                   'language'               => 'English',
                   'start_date'             => date('Y-m-d'),
                   'end_date'               => '#N/A',
                   'mbm_sign'               => 0,
                   'mbm_value'              => 0,
                   'age'                    => 'Unknown,18 - 24,25 - 34,35 - 44,45 - 54,55 - 64,65+',
                   'gender'                 => 'Male,Female,Unknown',
                   'delivery_method'        => 'standard',
                   'countries'              => 'Australia,United Kingdom,United States of America',
                   'daily_budget'           => 0,
                   'max_cpv'                => 0.00,
                   'video_ads'              => "null"
                );
                //$this->db->insert('campaign_list', $data); 
                print_r($data);         
            }else{
                $check_camp_list = $check_opt->row_array();
                //update campaign name
                $data = array('data' => $data_ser);     
                $this->db->where(array('id'=> $check_camp_list['target_lists'], 'user_id' => $this->user->id));
                //$this->db->update('targets', $data);

            }

            $this->db->trans_complete();
            //echo "success";
            echo $token_key;
         }
  die();
    }
    function aasort (&$array, $key) {
        $sorter=array();
        $ret=array();
        reset($array);
        foreach ($array as $ii => $va) {
            $sorter[$ii]=$va[$key];
        }
        asort($sorter);
        foreach ($sorter as $ii => $va) {
            $ret[$ii]=$array[$ii];
        }
        $array=$ret;
    }

    function check_existing_opt_camp(){
        //$user = $this->user;
        $sql = "select count(id) as count_id from overview where user_id = '{$this->user->id}' and status = '1' LIMIT 1";
        $check_exist = $this->db->query($sql);
        if($check_exist->num_rows() == 0){
            echo 0;
        }else{
            $check_e = $check_exist->row_array();
            echo $check_e['count_id'];
        }
       // echo 2;
    }
    function array_sort_by_column(&$arr, $col, $dir = SORT_ASC) {
        $sort_col = array();
        foreach ($arr as $key=> $row) {
            $sort_col[$key] = $row[$col];
        }

        array_multisort($sort_col, $dir, $arr);
    }

    function cleanup($word)
    {
        $word = trim($word);
        $word = strip_tags($word, " <STRONG> <EM> <U> <BR> <n> \n ");
        $word = addslashes($word);
        $word = str_replace(array("|","~>","'")," ",$word); 
        return $word;
    }      
    public function _nav_sidebar($active,$parent,$data = null)
    {
        $this->load->view('dashboard/sidebar', array(
            'active' => $active,
            'parent' => $parent,
            'data' => $data
        ));
    }    

    public function load_view($page,$data = null)
    {

        $this->load->view($page, array(
            'active'  => $page,
            'data' => $data
        ));
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