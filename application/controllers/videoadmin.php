<?php defined('BASEPATH') OR exit('No direct script access allowed');
error_reporting(0);
class Videoadmin extends MX_Controller {

    public $data;

    function __construct()
    {
        parent::__construct();
        $this->load->library('authentication', NULL, 'ion_auth');
        $this->load->library('form_validation');
        $this->load->helper('url','form');
        $this->load->library('template');
        $this->template->add_js('contactform.js');
        $this->load->database();

        $this->form_validation->set_error_delimiters($this->config->item('error_start_delimiter', 'ion_auth'), $this->config->item('error_end_delimiter', 'ion_auth'));

        $this->lang->load('auth');
        $this->load->helper('language');

        $this->baseurl = $this->config->config['base_url']; 
        $this->group_name = array('video_admin','admin');
        $this->user  = $this->ion_auth->user()->row();

        $this->smtp_host = "box342.bluehost.com";
        $this->smtp_user = "nathan@nathanhague.com";
        $this->smtp_pass = "$Wolfman1";  
    }

    function index(){
        $user = (array) $this->user;
        $this->check_permission();

        $this->pending();      
    }

    function pending(){
        $user = (array) $this->user;
        $this->check_permission();



        $count_video_uploaded = $this->count_video_pending(1);
        $o['count_video_uploaded'] = number_format($count_video_uploaded,0);

        $count_video_process = $this->count_video_pending(2);
        $o['count_video_process'] = number_format($count_video_process,0);        

        $count_video_done = $this->count_video_pending(3);
        $o['count_video_done'] = number_format($count_video_done,0); 

        /* check pending*/
        $sql = "select id,upload_status,orig_filename,video_path,date_uploaded, (select username from users where id = paypal_exp.user_id LIMIT 1 ) as user_name 
                    from 
                paypal_exp where ppstatus = 'approved' 
        and 
            upload_status = '1' 
        and
            video_path != ''";
           
        $check_pending = $this->db->query($sql);
        if($check_pending->num_rows() == 0){
            $msg = "No Pending videos found";
            $msg_type = 'warning';
        }else{
           $show_pend = array();
           foreach($check_pending->result_array() as $cp){
                $cp['date_uploaded'] = date('m-d-Y H:i a', $cp['date_uploaded']);
                $show_pend[] = $cp;
           }
           $o['show_pend'] = $show_pend;
           $o['show_table_list'] = true;

        }
        $o['video_admin_header'] = true;
        $o['msg_type'] = $msg_type;
        $o['msg'] = $msg;
        $o['title'] = "Pending Videos";
        $o['page'] = 'pending';
        $o['user'] = $user;
        $o['baseurl'] = $this->baseurl;    
        $this->data['o'] = $o;
        $this->load->view('videoadmin/main', $this->data);         
    }

    function process(){
        $user = (array) $this->user;
        $this->check_permission();

        $count_video_uploaded = $this->count_video_pending(1);
        $o['count_video_uploaded'] = number_format($count_video_uploaded,0);

        $count_video_process = $this->count_video_pending(2);
        $o['count_video_process'] = number_format($count_video_process,0);        

        $count_video_done = $this->count_video_pending(3);
        $o['count_video_done'] = number_format($count_video_done,0); 

        /* check process*/
        $sql = "select id,upload_status,orig_filename,video_path,date_uploaded, (select username from users where id = paypal_exp.user_id LIMIT 1 ) as user_name 
                    from 
                paypal_exp where ppstatus = 'approved' 
        and 
            upload_status = '2' 
        and
            video_path != ''";
           
        $check_pending = $this->db->query($sql);
        if($check_pending->num_rows() == 0){
            $msg = "No Process videos found";
            $msg_type = 'warning';
        }else{
           $show_pend = array();
           foreach($check_pending->result_array() as $cp){
                $cp['date_uploaded'] = date('m-d-Y H:i a', $cp['date_uploaded']);
                $show_pend[] = $cp;
           }
           $o['show_pend'] = $show_pend;
           $o['show_table_list'] = true;

        }
        $o['video_admin_header'] = true;
        $o['msg_type'] = $msg_type;
        $o['msg'] = $msg;
        $o['title'] = "Process Videos";
        $o['page'] = 'process';
        $o['user'] = $user;
        $o['baseurl'] = $this->baseurl;    
        $this->data['o'] = $o;
        $this->load->view('videoadmin/main', $this->data);         
    }

    function done(){
        $user = (array) $this->user;
        $this->check_permission();

        $count_video_uploaded = $this->count_video_pending(1);
        $o['count_video_uploaded'] = number_format($count_video_uploaded,0);

        $count_video_process = $this->count_video_pending(2);
        $o['count_video_process'] = number_format($count_video_process,0);        

        $count_video_done = $this->count_video_pending(3);
        $o['count_video_done'] = number_format($count_video_done,0); 

        /* check done*/
        $sql = "select id,upload_status,orig_filename,video_path,date_uploaded, (select username from users where id = paypal_exp.user_id LIMIT 1 ) as user_name 
                    from 
                paypal_exp where ppstatus = 'approved' 
        and 
            upload_status = '3' 
        and
            video_path != ''";
           
        $check_pending = $this->db->query($sql);
        if($check_pending->num_rows() == 0){
            $msg = "No Done videos found";
            $msg_type = 'warning';
        }else{
           $show_pend = array();
           foreach($check_pending->result_array() as $cp){
                $cp['date_uploaded'] = date('m-d-Y H:i a', $cp['date_uploaded']);
                $show_pend[] = $cp;
           }
           $o['show_pend'] = $show_pend;
           $o['show_table_list'] = true;

        }
        $o['video_admin_header'] = true;
        $o['msg_type'] = $msg_type;
        $o['msg'] = $msg;
        $o['title'] = "Done Videos";
        $o['page'] = 'done';
        $o['user'] = $user;
        $o['baseurl'] = $this->baseurl;    
        $this->data['o'] = $o;
        $this->load->view('videoadmin/main', $this->data);         
    }

    function update_content(){
        $pexp_id   = $this->uri->segment(3);
        $p          = strtolower($this->input->post('p'));
        $user = (array) $this->user;
        $this->check_permission();

        $sess_msg       = $this->session->flashdata('msg');
        $sess_msg_type  = $this->session->flashdata('msg_type');
        if($sess_msg){
            $msg = $sess_msg;
            $msg_type = $sess_msg_type;
        }


        if($p == "update_video_status"){
            $this->db->trans_start();
            $upload_status       = $this->cleanup($this->input->post('upload_status'));
            $notes               = $this->cleanup($this->input->post('notes'));  
            $video_id            = $this->cleanup($this->input->post('video_id'));
            $new_filename        = $this->cleanup($this->input->post('new_filename'));

            if($upload_status == '2'){
               $data = array(
               'upload_status' => $upload_status,
               'notes' => $notes,
               'is_read' => 0,
               'video_path_done' => $new_filename,
               'date_update' => time()
                );  
            }
            else{
                $data = array(
                   'upload_status' => $upload_status,
                   'notes' => $notes,
                   'is_read' => 0,
                   'video_path_done' => $new_filename,
                   'date_update' => time()
                );     
            }

            $this->db->where('id', $video_id);
            $this->db->update('paypal_exp', $data);

            /* send email */
            $this->load->library('email');
            $config['protocol'] = 'smtp';
            $config['smtp_host'] = $this->smtp_host;
            $config['smtp_user'] = $this->smtp_user;
            $config['smtp_pass'] = $this->smtp_pass;
            $config['smtp_port'] = '26';
            $config['charset'] = 'iso-8859-1';
            $config['wordwrap'] = TRUE;
            $config['mailtype'] = 'html';

            if($upload_status == "2"){
                $update_type = "PROCESS";
            }
            if($upload_status == "3"){
                $update_type = "COMPLETE";
            }
            $sql = "select orig_filename, (select first_name from users where id = paypal_exp.user_id LIMIT 1) as firstname,
(select email from users where id = paypal_exp.user_id LIMIT 1) as email from paypal_exp where id = '$video_id' LIMIT 1";

            $check_em = $this->db->query($sql);
            if($check_em->num_rows() == 0){
                $this->session->set_flashdata('msg', 'Failed to update, please try again');
                $this->session->set_flashdata('msg_type', 'danger');
            }
            else{
                $cr = $check_em->row_array();
                /* check details*/
                $this->email->initialize($config);
                $subject = "Video upload is now on $update_type";
                $content = "Hi {$cr['firstname']} <br><br> Your Uploaded file: {$cr['orig_filename']} is now on $update_type <br><br> - TubeTargetPro Team";
                $this->email->from("australiawow@gmail.com", "TubeTargetPro");
                $this->email->to("{$cr['email']}"); 
                //$this->email->to('info@topappstoday.com'); 
                $this->email->subject("$subject");
                $this->email->message("$content");  
            
                if ($this->email->send())
                {
                    $this->session->set_flashdata('msg', 'Update successful');
                    $this->session->set_flashdata('msg_type', 'success');
                    
      
                }else {
                    $this->session->set_flashdata('msg', 'Failed to update, please try again');
                    $this->session->set_flashdata('msg_type', 'danger');
                }
            }
            $this->db->trans_complete();
            redirect($this->baseurl . "videoadmin/update_content/$video_id", 'refresh');

        }
        $count_video_uploaded = $this->count_video_pending(1);
        $o['count_video_uploaded'] = number_format($count_video_uploaded,0);

        $count_video_process = $this->count_video_pending(2);
        $o['count_video_process'] = number_format($count_video_process,0);        

        $count_video_done = $this->count_video_pending(3);
        $o['count_video_done'] = number_format($count_video_done,0); 

        /* check done*/
        $sql = "select *, (select username from users where id = paypal_exp.user_id LIMIT 1 ) as user_name 
                    from 
                paypal_exp where ppstatus = 'approved'
        and id = '$pexp_id' LIMIT 1";
           
        $check_pending = $this->db->query($sql);
        if($check_pending->num_rows() == 0){
            $msg = "No Done videos found";
            $msg_type = 'warning';
        }else{
           $show_pend = array();
           $get_cp = $check_pending->row_array();
            if($get_cp['upload_status'] == "1"){
                    $get_cp['label_det']            = "label-danger";
                    $get_cp['upload_status_text'] = "PENDING";
            }
            if($get_cp['upload_status'] == "2"){
                 $get_cp['label_det']            = "label-warning";
                $get_cp['upload_status_text'] = "IN PROGRESS";
            }
            if($get_cp['upload_status'] == "3"){
                $get_cp['label_det']            = "label-success";
                    $get_cp['upload_status_text'] = "COMPLETE";
            }
           $o['gc'] = $get_cp;
           $o['update_pp_exp_table'] = true;
        }
        $o['video_admin_header'] = true;
        $o['msg_type'] = $msg_type;
        $o['msg'] = $msg;
       //$o['page'] = 'process';
        $o['user'] = $user;
        $o['baseurl'] = $this->baseurl;    
        $this->data['o'] = $o;
        $this->load->view('videoadmin/main', $this->data);     
    }
    function count_video_pending($type){
        $user = $this->user;
        $sql = "select count(id) as count_pending 
                    from 
                paypal_exp where ppstatus = 'approved' 
        and 
            upload_status = '$type' 
        and
            video_path != '' LIMIT 1";
        $count_vid = $this->db->query($sql);
        if($count_vid->num_rows() == 0){
            return 0;
        }else{
            $check_c = $count_vid->row_array();
            return $check_c['count_pending'];
        }

    }
    function check_permission(){
        if (!$this->ion_auth->in_group($this->group_name) or !$this->ion_auth->logged_in() )
        {
            return show_error('Permission denied, you are not allowed to view this page');
        }  
    }

    function cleanup($word)
    {
        $word = trim($word);
        $word = strip_tags($word, " <STRONG> <EM> <U> <BR> <n> \n ");
        $word = addslashes($word);
        $word = str_replace(array("|","~>","'")," ",$word); 
        return $word;
    }  

    function upload_file(){

        if (!empty($_FILES)) {

            $orig_file_name     = $_FILES['afile1']['name'];
            $config['upload_path'] = './assets/uploads/processed';
            $config['allowed_types'] = 'jpeg|jpg|doc|docx|xls|docx|png|pdf|pot|ppt|pptx|dot|dotx|ods|avi|mp4';
            $config['max_size'] = '1024000'; // 50MB
            $config['encrypt_name'] = TRUE;
            $this->load->library('upload', $config);

            // Validate the file type           
            //$fileParts = md5(time() . "_" . $_FILES['Filedata']['name']);
            //$config['file_name'] = $fileParts;
            $filetype   = $_FILES['afile']['type'];         
            $file_name  = $_FILES['afile']['name'];
            $Filedata   = $_FILES['afile']['tmp_name'];     


            if ( ! $this->upload->do_upload('afile1'))
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
                    $data2 = array('upload_data' => $this->upload->data());
                    $new_filename           = $data2['upload_data']['file_name'];
                    $original_filename      = $data2['upload_data']['client_name'];
                    $file_size              = $data2['upload_data']['file_size'];
                    $upload_type            = $data2['upload_data']['file_ext'];
                    $upload_is_image        = $data2['upload_data']['is_image'];



                $json = json_encode(array(
                  'error' => 0,
                  'error_msg'  => "success",
                  'filename' => $new_filename,
                  'orig_filename' => $original_filename
                    ));
                echo $json;

            }
        }   

    }  

    function download_file()
    {
        $video_id      = $this->uri->segment(3);
        $user = (array) $this->user;
        $this->check_permission();

        $this->load->helper('download');
        
        $sql = "select video_path,orig_filename from paypal_exp where id = '$video_id' and video_path != '' and ppstatus = 'approved' LIMIT 1";

        $check_conv = $this->db->query($sql);
        if($check_conv->num_rows() == 0){
            $this->session->set_flashdata('msg', 'Error, No File found, please try again..');
            $this->session->set_flashdata('msg_type', 'danger');
            redirect("$this->baseurl/videoadmin/update_content/$video_id", 'refresh');    
        }else{
            $show_dp = $check_conv->result_array();

                $data = file_get_contents("./assets/uploads/raw/" . $show_dp[0]['video_path']); // Read the file's contents
                $name = "{$show_dp[0]['orig_filename']}";
                force_download($name, $data);

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