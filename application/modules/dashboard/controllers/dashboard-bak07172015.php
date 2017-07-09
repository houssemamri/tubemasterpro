<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Dashboard extends MY_Controller {

	protected $user;

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
        
		$this->user = $this->ion_auth->user()->row();
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
        
    }

    public function index()
    {

        // $active = 'monetize_check';
        // $parent = 'target';

        // $this->template->load_view('dashboard/dashboard', array(
        //     'nav_sidebar' => Modules::run('dashboard/_nav_sidebar', $active, $parent),
        //     'content' => Modules::run('dashboard/'.$active, $active)
        // ));

        $this->keyword_search();
    }

    public function home()
    {
        $active = 'home';
        $parent = '';

        $this->template->load_view('dashboard/dashboard', array(
            'nav_sidebar' => Modules::run('dashboard/_nav_sidebar', $active, $parent),
            'content' => Modules::run('dashboard/load_view', $active)
        ));
    }

    public function monetize_check()
    {
        $active = 'monetize_check';
        $parent = 'target';

        $this->template->load_view('dashboard/dashboard', array(
            'nav_sidebar' => Modules::run('dashboard/_nav_sidebar', $active, $parent),
            'content' => Modules::run('dashboard/load_view', $active)
        ));
    }

    public function keyword_search()
    {
        $active = 'keyword_search';
        $parent = 'target';

        $this->template->load_view('dashboard/dashboard', array(
            'nav_sidebar' => Modules::run('dashboard/_nav_sidebar', $active, $parent),
            'content' => Modules::run('dashboard/load_view', $active)
        ));
    }
    
    public function video_search()
    {
        $this->load->model('target_model');

        $targets = $this->target_model->get_all( $this->user->id );
        $options = '';

        if ( $targets ) {
            foreach ($targets as $key => &$value) {
                if ( !$value->status ) {
                    $value->data = unserialize($value->data);
                	$options .= "<option value='".$value->id."' data-ytdata='".json_encode( $value->data )."' >".$value->name."</option>";
                }
            }
        }

		$active  = 'video_search';
        $parent  = 'target';
        $keyword = $this->input->get_post('keyword');

        $this->template->load_view('dashboard/dashboard', array(
            'nav_sidebar' => Modules::run('dashboard/_nav_sidebar', $active, $parent),
            'content' => Modules::run('dashboard/load_view', $active, $keyword, $options)
        ));

    }

    public function channel_search()
    {
    	$this->load->model('target_model');

        $targets = $this->target_model->get_all( $this->user->id );
        $options = '';

        if ( $targets ) {
            foreach ($targets as $key => &$value) {
                if ( !$value->status ) {
                    $value->data = unserialize($value->data);
                    $options .= "<option value='".$value->id."' data-ytdata='".json_encode( $value->data )."' >".$value->name."</option>";
                }
            }
        }
        
        $active = 'channel_search';
        $parent = 'target';
        $keyword = $this->input->get_post('keyword');

        $this->template->load_view('dashboard/dashboard', array(
            'nav_sidebar' => Modules::run('dashboard/_nav_sidebar', $active, $parent),
            'content' => Modules::run('dashboard/load_view', $active, $keyword, $options)
        ));

    }
    
    public function target_list()
    {
        $this->load->model('target_model');

        $targets = $this->target_model->get_all( $this->user->id );
        $options = '';
        $html 	 = '';
        
        /*
echo '<pre>';
        print_r($targets);
        echo '</pre>';die();
*/

        if ( $targets ) {
            $tour_pending = false;
            $tour_target  = false;
        	$html .= "<input type='hidden' value='".count($targets)."' id='num_targets' />";
            foreach ($targets as $key => &$value) {
                $value->data = ( empty( $value->data ) ) ? 0 : unserialize($value->data);
                $num_vid = ( empty( $value->data ) ) ? 0 : count( $value->data );
                $style_red  = ( $num_vid <= 0 && !$value->status ) ? "style='color:red;'": "";
                $get_links_class = ( $num_vid <= 0 ) ? "class='btn btn-sm btn-disabled get_links' disabled='disabled'": "class='btn btn-sm btn-primary get_links'";
                $video_links = array();
                $links = array();
                $ta_title = ( $value->status ) ? "" : "ta_title";
                $pending_class = ( $value->status ) ? "pending_target" : "";
                
                if ( !empty( $value->data ) ) {
	                foreach( $value->data as $video ){
		                // $links .= $video['link_url'].'\n';
		                //$links .= ("".$video['link_url']."\n");
		                // $links .= nl2br($video['link_url'].'\n\b', true);
						$ytlink = "https://www.youtube.com/watch?v=".$video['ytid'];
		                array_push($links, $ytlink);
	                }
	                $links = join('%0A', $video_links);
                }

                //- FOR TOUR
                $hook_nine = '';
                $hook_ten  = '';
                if ( $value->status && !$tour_pending ) {
                    $hook_nine = 'id="hook-nine"';
                    $tour_pending = true;
                }
                else if ( !$tour_target ) {
                    $hook_ten = 'id="hook-ten"';
                    $tour_target = true;
                }
                
                $row  = "";
                $row .= "<div ".$hook_nine." class='row row-target ".$pending_class."' data-list-id='".$value->id."' data-num-vids='".$num_vid."'>";
                $row .= "<div class='col-sm-1' style='width:50px;text-align:center;'>";
                
                if ( !$value->status ) {
                	$row .= "<input type='checkbox' name='list_to_delete' data-list-name='' value='' class='list_to_delete'>";
                }
                else {
	                $row .= "<span class='glyphicon glyphicon-time pending_watch'></span>";
                }
                
                $row .= "</div>";
                $row .= "<div class='col-sm-3 ".$ta_title."' ".$style_red.">".$value->name." (".$num_vid.")</div>";
                
                if ( !$value->status ) {
                
                $row .= "<div ".$hook_ten." class='col-sm-8'>";
                
                $row .= "<div class='form-group col-sm-4'>";
                $row .= "<input type='text' name='list_name' class='list_name_input form-control input-sm' placeholder='Edit List Name' required=''>";
                $row .= "</div>";
                
                $row .= "<div class='form-group col-sm-2'>";
                $row .= "<button type='button' class='btn btn-sm btn-default rename_list1' title='' data-toggle='tooltip' data-placement='top' data-original-title='Change Name'>Rename</button>";
                $row .= "<button type='button' class='btn btn-sm btn-success rename_list' title='' data-toggle='tooltip' data-placement='top' data-original-title='Save Name' data-action='".site_url('dashboard/dashboard_ajax/rename_target')."'>Save</button>";
                $row .= "</div>";
                
                $row .= "<div class='form-group col-sm-2'>";
                $row .= "<button type='button' ".$get_links_class." title='' data-toggle='tooltip' data-placement='top' data-original-title='Get Links' >Get Links</button>";
                $row .= "<textarea class='links' style='display:none;'>".$links."</textarea>";
                $row .= "</div>";
                
                $row .= "<div class='form-group col-sm-2'>";
                $row .= "<button type='button' class='btn btn-sm btn-warning add_video' title='' data-toggle='tooltip' data-placement='top' data-original-title='Add Custom Video' data-action='".site_url('dashboard/dashboard_ajax/add_video')."'>Add Video</button>";
                $row .= "</div>";
                
                $row .= "<div class='form-group col-sm-2'>";
                $row .= "<button type='button' class='btn btn-sm btn-danger delete_list' title='' data-toggle='tooltip' data-placement='top' data-original-title='Delete List' data-action='".site_url('dashboard/dashboard_ajax/delete_target_list')."'>Delete List</button>";
                $row .= "</div>";
                
                $row .= "</div>";
                
                }// end if status
                else {
                    $row .= "<div class='col-sm-8'>";
                    $row .= "<div class='form-group col-sm-offset-10 col-sm-2'>";
                    $row .= "<button type='button' class='btn btn-sm btn-danger delete_list' title='' data-toggle='tooltip' data-placement='top' data-original-title='Delete List' data-action='".site_url('dashboard/dashboard_ajax/delete_target_list')."'>Delete List</button>";
                    $row .= "</div>";
                    $row .= "</div>";
                }
                
                $row .= "</div>";
                
                $row .= "<div class='row row-target2'>";
                $row .= "<div class='col-sm-12'>";
                $row .= "<table class='stupid table table-bordered'>";
                $row .= "<thead><tr>";
                $row .= "<th><input class='target_check_all' type='checkbox' /></th>";
                
                $row .= "<th style='text-align:center;'>";


                if ( !$this->ion_auth->in_group(3) ) {
				    $row .= "<button type='button' disabled='disabled' class='col-sm-12 btn btn-sm btn-disabled bulk_mover'>Move Selected</button>";
                }
				$row .= "<button type='button' disabled='disabled' class='col-sm-12 btn btn-sm btn-disabled bulk_deleter' data-action='".site_url('dashboard/dashboard_ajax/delete_target_list_videos')."'>Delete Selected</button>";
				$row .= "</th>";
				
				$row .= "<th>Title</th>";
				$row .= "<th>Views</th>";
				$row .= "<th>Likes</th>";
				$row .= "<th>Dislikes</th>";
				$row .= "<th>Comments</th>";
				$row .= "<th class='text-center'><button class='btn btn-default toggle_columns' type='button' data-toggle='tooltip' data-placement='top' data-original-title='Toggle Columns'>Toggle</button></th>";
				$row .= "</tr></thead><tbody></tbody></table>";
				$row .= "</div></div>";
                
                $html .= $row;
            }
        }

		$active  = 'target_list';
        $parent  = 'target';

        $this->template->load_view('dashboard/dashboard', array(
            'nav_sidebar' => Modules::run('dashboard/_nav_sidebar', $active, $parent),
            'content' => Modules::run('dashboard/load_view', $active, null, $html)
        ));

    }

    public function get_all () {
        $this->load->model('target_model');
        /*
 $targets = $this->target_model->get_all( $this->user->id );

        foreach ($targets as $key => &$value) {
            $value->data = unserialize($value->data);
        }

        echo '<pre>';
        print_r($targets);
        echo '</pre>';
*/
		$targets = $this->target_model->save( 13 );
		echo '<pre>';
		echo $targets;
		//print_r($targets);
		//echo count(unserialize($targets->data));
		echo '</pre>';
    }
    
    public function profile () {
        $this->load->database();
        $this->load->model('target_model');
        $this->template->add_js('jquery.validify.js');
        $this->template->add_js('modules/profile.js');
        
        $active = 'profile';
        $parent = '';
        
        $this->template->load_view('dashboard/dashboard', array(
            'nav_sidebar' => Modules::run('dashboard/_nav_sidebar', $active, $parent),
            'content'     => Modules::run('dashboard/load_profile', $active, $this->user)
        ));
    }
    
    public function adwords_export()
    {	
        $this->template->add_css('jqueryui/jquery-ui.min.css');
        $this->template->add_js('jqueryui/jquery-ui.min.js');
        $this->template->add_js('moment.min.js');
        $this->template->add_js('modules/campaign.js?v='.time());
        $this->template->add_js('modules/overview.js?v='.time());
        
        $this->load->database();
        $this->load->model('target_model');
        
        $active = 'adwords_export';
        $parent = '';
 
         //check if has segments from optimizer
        $from_opt = $this->uri->segment(3);
        $opt_key  = $this->uri->segment(4);

        if($from_opt == "opt" && $opt_key != ""){
             $sql = "select id,name,user_id,status,token_key from overview 
                where 
                    user_id = '{$this->user->id}' and token_key='$opt_key' and status = '1' LIMIT 1";
            $check_opt = $this->db->query($sql);
            if($check_opt->num_rows() > 0 ){
                $gopt = $check_opt->row_array();
                $gopt['show_optimization_data'] = true;
                $optimizer['gopt'] = $gopt;


            }
        }

        if($from_opt == "uopt" && $opt_key != ""){
            $opt_id  = $this->uri->segment(5);
           // $sql = "select id from campaign_list where optimizer_id = '$opt_id' and user_id = '{$this->user->id}' LIMIT 1";
            $sql = "select  o.id as opt_id,
                            o.name,
                            o.user_id,
                            o.status,
                            o.token_key, 
                            cl.`name` as campaign_name,
                            cl.id 
                from 
                    overview as o,
                    campaign_list as cl
                where 
                    cl.optimizer_id = o.id
                and
                    cl.user_id = '{$this->user->id}' 
                and
                    cl.optimizer_id = '$opt_id'
                and 
                    o.token_key='$opt_key' 
                and 
                    o.status = '1' LIMIT 1
                ";
            $check_opt = $this->db->query($sql);
            if($check_opt->num_rows() > 0 ){
                $gopt = $check_opt->row_array();
                $gopt['opt_update_id'] = $gopt['id'];
                $gopt['open_update_optimized_campaign'] = true;
                $gopt['show_optimization_data'] = true;
                $optimizer['gopt'] = $gopt;
            }    
        }
                       
        $targets = $this->target_model->get_all( $this->user->id);
        $campaign_list = $this->target_model->get_campaigns( $this->user->id);
        $options = '';
        $campaigns = null;

        if ( $targets ) {

            foreach ($targets as $key => &$value) {
                $value->data = unserialize($value->data);
                if(substr_count(json_encode( $value->data ),"ytid") != 0)
                {
                    $options .= "<li><label data-name='\"".$value->name."\"' data-count=".substr_count(json_encode( $value->data ),"ytid")."><input type='checkbox' name='target' data-ytdata='".json_encode( $value->data )."' class='target_checkbox' value='".$value->id."'>
    <span class='disabled-detector'></span>".$value->name." (".substr_count(json_encode( $value->data ),"ytid").")</label></li>";
                }     
            }

        }
        
        $campaigns  = '<select id="campaign_list" name="campaign_list" class="form-control">';
        $campaigns .= '<option value="0"> -- New Campaign -- </option>';
        if ( $campaign_list ) {
	        foreach ($campaign_list as $key => $value) {
                $campaigns .= '<option value="'.$value->id.'">'.$value->name.'</option>';
            }
        }
        $campaigns .= '</select>';

        $this->template->load_view('dashboard/dashboard', array(
            'nav_sidebar' => Modules::run('dashboard/_nav_sidebar', $active, $parent),
            'content' => Modules::run('dashboard/load_view', $active, null, $options, $campaigns, $optimizer)
        ));

    }
    
    public function campaign_optimizer ()
    {
        $this->load->database();
        $this->load->model('target_model');
        
        $active = 'campaign_optimizer';
        $parent = '';
        
        $targets = $this->target_model->get_all( $this->user->id);
        $options = '';

        if ( $targets ) {

            /*
foreach ($targets as $key => &$value) {
                $value->data = unserialize($value->data);
                if(substr_count(json_encode( $value->data ),"ytid") != 0)
                {
                    $options .= "<li><label data-count=".substr_count(json_encode( $value->data ),"ytid")."><input type='checkbox' name='target' data-ytdata='".json_encode( $value->data )."' class='target_checkbox' value='".$value->id."'>".$value->name." (".substr_count(json_encode( $value->data ),"ytid").")</label></li>";
                }     
            }
*/

        }

        $this->template->load_view('dashboard/dashboard', array(
            'nav_sidebar' => Modules::run('dashboard/_nav_sidebar', $active, $parent),
            'content' => Modules::run('dashboard/load_view', $active, null, $options)
        ));

    }
    
    public function campaign_upload ()
    {
        $this->load->database();
        $this->load->model('target_model');

        $this->template->add_css('jquery-fileupload/jquery.fileupload.css?v='.time());
        $this->template->add_css('jquery-fileupload/jquery.fileupload-ui.css?v='.time());
        $this->template->add_css('modules/compare.css?v='.time());
        $this->template->add_js('jqueryui/jquery-ui.min.js?v='.time());
        $this->template->add_js('jquery-fileupload/jquery.fileupload.js?v='.time());
        $this->template->add_js('jquery-fileupload/jquery.iframe-transport.js?v='.time());
        $this->template->add_js('jquery-fileupload/jquery.fileupload-process.js?v='.time());
        $this->template->add_js('jquery-fileupload/jquery.fileupload-validate.js?v='.time());
        $this->template->add_js('modules/compare.js?v='.time());
        
        $active = 'campaign_upload';
        $parent = 'optimizer';
        
        $campaign_list = $this->target_model->get_campaigns($this->user->id);
        $campaigns = '';
        if ( $campaign_list ) {
			
	        foreach ($campaign_list as $key => $value) {
	        	$uploaded = $this->target_model->get_campaign_uploaded($value->id);
	        	
	        	if ($uploaded) {
	        		$campaigns .= '<tr>';
	                $campaigns .= '<td>'.$value->name.'</td>';
	                $campaigns .= '<td>'.date('Y-m-d h:i:s A',$uploaded->date_uploaded).'</td>';
	                $campaigns .= '<td class="text-center">';
	                $campaigns .= '<p>';
	                $campaigns .= '<button data-cid="'.$value->id.'" type="button" class="btn btn-warning btn-jumbo btn-info"><span class="glyphicon glyphicon-info-sign"></span> Info</button> ';
	                $campaigns .= '<span class="btn btn-jumbo btn-success fileinput-button"><i class="glyphicon glyphicon-upload"></i><input class="btn-upload" data-cid="'.$value->id.'" type="file" name="campaign_file"><span> Update</span></span> ';
	                $campaigns .= '<button data-cid="'.$value->id.'" type="button" class="btn btn-danger btn-jumbo btn-delete"><span class="glyphicon glyphicon-remove-sign"></span> Delete</button> ';
	                $campaigns .= '</p>';
	                $campaigns .= '</td>';
					$campaigns .= '</tr>';
	        	}
	        	else {
	        		$campaigns .= '<tr>';
	                $campaigns .= '<td>'.$value->name.'</td>';
	                $campaigns .= '<td></td>';
	                $campaigns .= '<td class="text-center">';
	                $campaigns .= '<p>';
	                $campaigns .= '<button data-cid="'.$value->id.'" type="button" class="btn btn-warning btn-jumbo btn-info"><span class="glyphicon glyphicon-info-sign"></span> Info</button> ';
	                $campaigns .= '<span class="btn btn-jumbo btn-primary fileinput-button"><i class="glyphicon glyphicon-upload"></i><input class="btn-upload" data-cid="'.$value->id.'" type="file" name="campaign_file"><span> Upload</span></span> ';
	                $campaigns .= '<button data-cid="'.$value->id.'" type="button" class="btn btn-danger btn-jumbo btn-delete"><span class="glyphicon glyphicon-remove-sign"></span> Delete</button> ';
	                $campaigns .= '</p>';
	                $campaigns .= '</td>';
					$campaigns .= '</tr>';
	        	}
            }
        }
        else {
	        $campaigns = false;
        }

        $this->template->load_view('dashboard/dashboard', array(
            'nav_sidebar' => Modules::run('dashboard/_nav_sidebar', $active, $parent),
            'content' => Modules::run('dashboard/load_view', $active, null, $options, $campaigns)
        ));

    }
    
    function covtime($youtube_time) {
	    preg_match_all('/(\d+)/',$youtube_time,$parts);
	    
	    // Put in zeros if we have less than 3 numbers.
	    if (count($parts[0]) == 1) {
	        array_unshift($parts[0], "0", "0");
	    } elseif (count($parts[0]) == 2) {
	        array_unshift($parts[0], "0");
	    }
	
	    $sec_init = $parts[0][2];
	    $seconds = $sec_init%60;
	    $seconds_overflow = floor($sec_init/60);
	
	    $min_init = $parts[0][1] + $seconds_overflow;
	    $minutes = ($min_init)%60;
	    $minutes_overflow = floor(($min_init)/60);
	
	    $hours = $parts[0][0] + $minutes_overflow;
	
	    if($hours != 0) {
	    	$minutes = ($minutes < 10) ? '0'.$minutes : $minutes;
	    	$seconds = ($seconds < 10) ? '0'.$seconds : $seconds;
	        return $hours.':'.$minutes.':'.$seconds;
	    }
	    else {
	    	$seconds = ($seconds < 10) ? '0'.$seconds : $seconds;
	        return $minutes.':'.$seconds;
	    }
	}
    
    public function upload_videos()
    {
        
        $this->load->database();

        $user = $this->ion_auth->user()->row();
        $ads = "";
        $sql = "select id from paypal_exp where user_id = '$user->id' and ppstatus = 'approved' limit 1";
        $shw_r = $this->db->query($sql);

/*        foreach($shw_r->result_array() as $key=>$value){
            $p_data[$key] = $value;
        }*/
        
        $options['paypal_data'] = $shw_r->result_array();

        if($this->uri->segment(3)=="popup") {
	    	$active = 'uv_popup';
			$parent = '';

            $options['uri'] = site_url('');
            
            if(count($options['paypal_data']) > 0) {
                $options['approved'] = 1;
            }
            else {
                $options['approved'] = 0;
            }

	        $this->template->load_view('dashboard/uv_popup', array(
	            'content' => Modules::run('dashboard/load_view', $active, null, $options)
	        ));    
        }
        elseif ($this->uri->segment(3)=="view_details") {
            $active = 'view_details';
            $parent = '';
            $vid_id = $this->uri->segment(4);

            if((int)$vid_id != null) {

                $vid_id = (int)$vid_id;

                $options['uri'] = site_url('');

                $sql = "select * from paypal_exp where id = '$vid_id'";

                $shw_r = $this->db->query($sql);

                if(count($shw_r->result_array()) > 0) {
                    $options['vid_data'] = $shw_r->result_array();
                }
                else {
                    $options['vid_data'] = null;
                }

                $options['vid_id'] = $this->uri->segment(4);                                       
                
                $this->template->load_view('dashboard/view_details', array(
                    'content' => Modules::run('dashboard/load_view', $active, null, $options)
                ));
            }
            else {
                echo "<script> window.location.replace('".site_url('')."dashboard');</script>";
            }

        }
        else{
        	$this->template->add_css('jqueryui/jquery-ui.min.css');
	        $this->template->add_js('jqueryui/jquery-ui.min.js');
	        $this->template->add_js('moment.min.js');
	        $this->template->add_js('modules/upload_videos.js');
	        
	        $active = 'upload_videos';
	        $parent = '';

            $options['uri'] = site_url('');

            $sql = "select id from paypal_exp where user_id = '$user->id' and upload_status = 0 and ppstatus = 'approved' limit 1";
            $shw_r = $this->db->query($sql);

            if(count($options['paypal_data']) > 0) {
                $options['approved'] = 1;
                $options['uv_id'] = $shw_r->result_array()[0]['id'];
            }
            else {
                $options['approved'] = 0;
            }            

            if(count($shw_r->result_array()) > 0) {
                $options['pending'] = 1;
            }
            else {
                $options['pending'] = 0;
            }

            $sql = "select id, upload_status, orig_filename, date_uploaded, date_update from paypal_exp where user_id = '$user->id' and upload_status > 0 and ppstatus = 'approved'";
            $shw_r = $this->db->query($sql);

            if(count($shw_r->result_array()) > 0) {
                $options['vid_data'] = $shw_r->result_array();
            }
            else {
                $options['vid_data'] = null;
            }                                        
	        
	        $this->template->load_view('dashboard/dashboard', array(
	            'nav_sidebar' => Modules::run('dashboard/_nav_sidebar', $active, $parent),
	            'content' => Modules::run('dashboard/load_view', $active, null, $options)
	        ));
        }

    }

    public function load_view($page,$keyword = null,$targets = null,$campaigns = null, $optimizer = null)
    {
        $this->load->view($page, array(
            'active'  => $page,
            'keyword' => $keyword,
            'targets' => $targets,
            'campaigns'=> $campaigns,
            'optimizer'=> $optimizer
        ));
    }

    public function load_profile($page,$user)
    {
        $pagelet_upload_control = Modules::run('photo/_pagelet_upload_control', array(
            'message' => FALSE,

            'is_multiple' => FALSE,

            'success_callback' => 'function(file) {
                // Hide progress bar
                CIS.FileUpload.getControl($(this), "progress").addClass("hide");
                // Empty error messages
                CIS.FileUpload.getControl($(this), "holder").empty();
                $("#file_container").show();
                $("#file_name").text(file.name);
                $("#file_hidden").val(file.name);

                // Save to db
                CIS.FileUpload.saveAvatar($(this), file.name, "'.site_url('dashboard/dashboard_ajax/save_profile_pic').'" );

                $("#file_remove").on("click", function(e){
                    e.preventDefault();
                    console.log("test");
                    $("#file_hidden").val("");
                    $("#file-upload").find(".files").empty();
                    $("#file_container").hide();
                    $("#image-holder").prop("src", "'.assets_url('avatar/nophoto.png').'");
                    $("#image-holder").css("width", "50px");
                    $("#image-holder").css("height", "50px");
                });
            }',

            // Use a larger wrapper
            'parent' => '.js-custom-control',
            // A fixed holder for uploaded photo
            'image_holder_target' => '#image-holder',

            'profile_pic' => $user->profile_pic,
            'user_id' => $user->id,

            // Customize uploaded photo template
            'item_template' => '
                <img id="image-holder" class="img-thumbnail" src="{{thumbnailUrl}}">
            '
        ));
        $this->load->view($page, array(
            'active' => $page,
            'user'   => $user,
            'message'=> $this->session->flashdata('message'),
            'pagelet_upload_control' => $pagelet_upload_control
        ));
    }

    public function _nav_sidebar($active,$parent)
    {
        $this->load->view('sidebar', array(
            'active' => $active,
            'parent' => $parent
        ));
    }

    public function download_file()
    {
        $video_id      = $this->uri->segment(3);
        $user = (array) $this->user;

        $this->load->helper('download');
        
        $sql = "select video_path_done, orig_filename from paypal_exp where id = '$video_id' and video_path_done != '' and ppstatus = 'approved' and user_id = '{$user['id']}' LIMIT 1";

        $check_conv = $this->db->query($sql);
        if($check_conv->num_rows() == 0){
            $this->session->set_flashdata('msg', 'Error, No File found, please try again..');
            $this->session->set_flashdata('msg_type', 'danger');
            redirect("$this->baseurl/dashboard/upload_videos", 'refresh');    
        }else{
            $show_dp = $check_conv->result_array();

                $data = file_get_contents("./assets/uploads/processed/" . $show_dp[0]['video_path_done']); // Read the file's contents
                $name = "{$show_dp[0]['orig_filename']}";
                force_download($name, $data);

        }
    }

}