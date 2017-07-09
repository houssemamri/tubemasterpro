    <?php defined('BASEPATH') OR exit('No direct script access allowed');

    class Promocode extends MX_Controller {
       
        public $data;

        function __construct() {

            parent::__construct();
            $this->load->library('authentication', NULL, 'ion_auth');
            $this->load->library('form_validation');
           
            $this->load->library('template');
            $this->load->database();
            $this->load->model('Admin_model', 'admin'); 

            $this->form_validation->set_error_delimiters($this->config->item('error_start_delimiter', 'ion_auth'), $this->config->item('error_end_delimiter', 'ion_auth'));

            $this->lang->load('auth');
            $this->load->helper(array('url','form','captcha'));
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
            $date_today = date("Y-m-d",time());
          //  $sql = "select * from promo_code where secret_status = '1' and (start_date <= '$date_today'  and end_date >= '$date_today')";
              $sql = "select * from promo_code where secret_status = '1' and  end_date >= '$date_today' order by is_live desc, start_date asc";
          
            $check_promo = $this->db->query($sql);
            if($check_promo->num_rows() == 0){
                $msg = "No Promo code available.";
                $msg_type = 'warning';
            }else{
                $show_promo = array();
                foreach($check_promo->result_array() as $cp){
                    $show_promo[] = $cp;
                }
                $o['show_promo'] = $show_promo;
                $o['show_available_promo_table'] = true;
            }


            $o['promocode_header'] = true;
            $o['msg_type'] = $msg_type;
            $o['msg'] = $msg;
            $o['title'] = "Admin - Promo Code";
            $o['page'] = 'available_code';
            $o['user'] = $user;
            $o['baseurl'] = $this->baseurl;    
            $this->data['o'] = $o;
            $this->load->view('videoadmin/promocode', $this->data);  
        }

        function used_code () {
            $this->admin->check_permission();

            $user = (array) $this->user;
            $sess_msg       = $this->session->flashdata('msg');
            $sess_msg_type  = $this->session->flashdata('msg_type');
            if($sess_msg){
                $msg = $sess_msg;
                $msg_type = $sess_msg_type;
            }
            $date_today = date("Y-m-d",time());
            $sql = "select * from promo_code where secret_status = '1' and end_date < '$date_today' LIMIT 1";
          
            $check_promo = $this->db->query($sql);
            if($check_promo->num_rows() == 0){
                $msg = "No Promo code available.";
                $msg_type = 'warning';
            }else{
                $show_promo = array();
                foreach($check_promo->result_array() as $cp){
                    $show_promo[] = $cp;
                }
                $o['show_promo'] = $show_promo;
                $o['show_used_promo_table'] = true;
            }
            

            $o['promocode_header'] = true;
            $o['msg_type'] = $msg_type;
            $o['msg'] = $msg;
            $o['title'] = "Admin";
            $o['page'] = 'used_code';
            $o['user'] = $user;
            $o['baseurl'] = $this->baseurl;    
            $this->data['o'] = $o;
            $this->load->view('videoadmin/promocode', $this->data);  
        }

        function add_code()
        {
            $this->admin->check_permission();
            $this->load->helper('url');
            $user = (array) $this->user;
            $sess_msg       = $this->session->flashdata('msg');
            $sess_msg_type  = $this->session->flashdata('msg_type');
            if($sess_msg){
                $msg = $sess_msg;
                $msg_type = $sess_msg_type;
            }

            $this->form_validation->set_rules('secret_code', 'Secret Code', 'required|xss_clean|min_length[5]|max_length[10]|is_unique[promo_code.secret_code]');
            $this->form_validation->set_rules('date_from', 'Date From', 'required|xss_clean');
            $this->form_validation->set_rules('date_to', 'Date To', 'required|xss_clean');
            $this->form_validation->set_rules('discount_amt', 'Discount Percentage', 'required|xss_clean');
            $this->form_validation->set_rules('option_desc', 'Short Description', 'required|xss_clean|min_length[5]|max_length[100]');
            $this->form_validation->set_rules('claim_count', 'Claim count', 'required|xss_clean|min_length[1]|max_length[3]');
            $this->form_validation->set_rules('is_live', 'Promo Status', 'required|xss_clean');
          


            $secret_code    = $this->admin->cleanup($this->input->post('secret_code'));
            $date_from      = $this->admin->cleanup($this->input->post('date_from'));
            $date_to        = $this->admin->cleanup($this->input->post('date_to'));
            $discount_amt   = $this->admin->cleanup($this->input->post('discount_amt'));
            $option_desc    = $this->admin->cleanup($this->input->post('option_desc'));
            $is_live        = $this->admin->cleanup($this->input->post('is_live'));
            $claim_count    = $this->admin->cleanup($this->input->post('claim_count'));
            $add_another    = $this->admin->cleanup($this->input->post('add_another'));
            $is_onetime    = $this->admin->cleanup($this->input->post('is_onetime'));

            if($secret_code == ""){
                $secret_code = random_string('alnum', 8);
            }
            if($is_onetime == ""){
                $is_onetime = 0;
            }

            if ($this->form_validation->run() == true)
            { 
                #check country if existing
                $data = array(
                   'secret_code' => $secret_code,
                   'start_date' => $date_from ,
                   'end_date' => $date_to,
                   'discount_amt' => $discount_amt,
                   'option_desc' => $option_desc,
                    'num_claim' => $claim_count,
                   'claim_count' => 0,
                   'date_added' => time(),
                   'is_live'    => $is_live,
                   'is_onetime' => $is_onetime

                );
                $this->db->insert('promo_code', $data); 

                $this->session->set_flashdata('msg', 'Promo code successfully added.');
                $this->session->set_flashdata('msg_type', 'success');
                
                if($add_another == 1){
                    //redirect("promocode/add_code", 'refresh');
                     echo "<script>window.location.replace('" .site_url('promocode/add_code') . "');</script>";

                }else{
                    echo "<script>window.location.replace('" .site_url('promocode') . "');</script>";
                   // $this->index();    
                   // redirect("promocode", 'refresh');
                }
            }
            else
            {
                $msg = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');
                $msg_type = "warning";
                $form_attributes = array('class' => 'form-horizontal', 'id' => 'add');
                $this->data['form']       = form_open(site_url('promocode/add_code'),$form_attributes);

                $this->data['secret_code'] = array(
                            'name'  => 'secret_code',
                            'id'    => 'secret_code',
                            'type'  => 'text',
                            'style' => 'width: 200px;',
                            'value' => $secret_code,
                );

                $this->data['date_from'] = array(
                            'name'  => 'date_from',
                            'id'    => 'date_from',
                            'type'  => 'text',
                            'style' => 'width: 200px;',
                            'placeholder' => 'YYYY-MM-DD',
                            'value' => $date_from,
                            'readonly' => 'true'
                );

                $this->data['date_to'] = array(
                            'name'  => 'date_to',
                            'id'    => 'date_to',
                            'type'  => 'text',
                            'style' => 'width: 200px;',
                            'placeholder' => 'YYYY-MM-DD',
                            'readonly' => 'true',
                            'value' => $date_to
                );

                $this->data['option_desc'] = array(
                            'name'  => 'option_desc',
                            'id'    => 'option_desc',
                            'type'  => 'text',
                            'style' => 'width: 500px;',
                            'placeholder' => 'Description here',
                            'value' => $option_desc,
                );

                //
                $promo_values = $promo_output = array();
                $promo_output[""] = "Please Discount amount";
                for($i=20;$i<=100;$i++){
                    $promo_output[$i]   = $i . "%";
                }
                $this->data['discount_amt']   = form_dropdown('discount_amt',$promo_output,$discount_amt,'class="ui search dropdown"');

                $claim_count_output = array();
                for($i=1;$i<=100;$i++){
                    $claim_count_output[$i]   = $i;
                }
                $this->data['claim_count']   = form_dropdown('claim_count',$claim_count_output,$claim_count,'class="ui search dropdown"');

                    $dd_is_live= array(
                        ""  => "Select Promo status",
                        "0" => "Hold",
                        "1" => "Live"
                    );
                $this->data['is_live']   = form_dropdown('is_live',$dd_is_live,$is_live,'class="ui search dropdown"');
                $this->data['add_another'] = form_checkbox('add_another', '1', FALSE);
                $this->data['is_onetime'] = form_checkbox('is_onetime', '1', FALSE);
                
                $this->data['form_close'] = form_close();
            }

            $o['create_promo_code_table'] = true;
            $o['promocode_header'] = true;
            $o['msg_type'] = $msg_type;
            $o['msg'] = $msg;
            $o['title'] = "Admin - Create Promo Code";
            $o['page'] = 'create_code';
            $o['user'] = $user;
            $o['baseurl'] = $this->baseurl;    
            $this->data['o'] = $o;
            $this->load->view('videoadmin/promocode', $this->data);         
        }

        function updatecode(){
            $this->admin->check_permission();
            $code_id   = $this->uri->segment(3);
            $user = (array) $this->user;
            $sess_msg       = $this->session->flashdata('msg');
            $sess_msg_type  = $this->session->flashdata('msg_type');
            if($sess_msg){
                $msg = $sess_msg;
                $msg_type = $sess_msg_type;
            } 

            $this->form_validation->set_rules('secret_code', 'Secret Code', 'required|xss_clean|min_length[5]|max_length[10]');
            $this->form_validation->set_rules('date_from', 'Date From', 'required|xss_clean');
            $this->form_validation->set_rules('date_to', 'Date To', 'required|xss_clean');
            $this->form_validation->set_rules('discount_amt', 'Discount Percentage', 'required|xss_clean');
            $this->form_validation->set_rules('option_desc', 'Short Description', 'required|xss_clean|min_length[5]|max_length[100]');
            $this->form_validation->set_rules('claim_count', 'Claim count', 'required|xss_clean|min_length[1]|max_length[3]');
            $this->form_validation->set_rules('is_live', 'Promo Status', 'required|xss_clean');
          
          
            $secret_code    = $this->admin->cleanup($this->input->post('secret_code'));
            $date_from      = $this->admin->cleanup($this->input->post('date_from'));
            $date_to        = $this->admin->cleanup($this->input->post('date_to'));
            $discount_amt   = $this->admin->cleanup($this->input->post('discount_amt'));
            $option_desc    = $this->admin->cleanup($this->input->post('option_desc'));
            $is_live        = $this->admin->cleanup($this->input->post('is_live'));
            $claim_count    = $this->admin->cleanup($this->input->post('claim_count'));
            $update_code_id    = $this->admin->cleanup($this->input->post('update_code_id'));
            $is_onetime    = $this->admin->cleanup($this->input->post('is_onetime'));

            $sql = "select * from promo_code where secret_status = '1' and is_live = '0' and promo_code_id = '$code_id' LIMIT 1";
            $check_update_code = $this->db->query($sql);
            if($check_update_code->num_rows() == 0){
                $this->session->set_flashdata('msg', 'Invalid promo code, please try again');
                $this->session->set_flashdata('msg_type', 'danger');
                redirect("promocode", 'refresh');
            }else
            {

            $sp = $check_update_code->row_array();
               if ($this->form_validation->run() == true)
                { 
                    #check country if existing
                        $data = array(
                           'secret_code' => $secret_code,
                           'start_date' => $date_from ,
                           'end_date' => $date_to,
                           'discount_amt' => $discount_amt,
                           'option_desc' => $option_desc,
                           'num_claim' => $claim_count,
                           'is_live'    => $is_live,
                           'is_onetime' => $is_onetime
                        );
                    $this->db->simple_query("SET NAMES 'utf-8'");  
                    $data_where = array('promo_code_id'=> $update_code_id);       
                    $this->db->set($data);
                    $this->db->update('promo_code', $data, $data_where);

                    $this->session->set_flashdata('msg', 'Promo code successfully update.');
                    $this->session->set_flashdata('msg_type', 'success');

                    redirect("promocode/", 'refresh');
                   
                }
                else
                {
                    $msg = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');
                    $msg_type = "warning";
                    $form_attributes = array('class' => 'form-horizontal', 'id' => 'add');
                    $this->data['form']       = form_open(site_url('promocode/updatecode/' . $code_id),$form_attributes);


                        $this->data['secret_code'] = array(
                                    'name'  => 'secret_code',
                                    'id'    => 'secret_code',
                                    'type'  => 'text',
                                    'style' => 'width: 200px;',
                                    'value' => "{$sp['secret_code']}",
                        );
                     


                    $this->data['date_from'] = array(
                                'name'  => 'date_from',
                                'id'    => 'date_from',
                                'type'  => 'text',
                                'style' => 'width: 200px;',
                                'placeholder' => 'YYYY-MM-DD',
                                'value' => $sp['start_date'],
                                'readonly' => 'true'
                    );

                    $this->data['date_to'] = array(
                                'name'  => 'date_to',
                                'id'    => 'date_to',
                                'type'  => 'text',
                                'style' => 'width: 200px;',
                                'placeholder' => 'YYYY-MM-DD',
                                'readonly' => 'true',
                                'value' => $sp['end_date']
                    );

                    $this->data['option_desc'] = array(
                                'name'  => 'option_desc',
                                'id'    => 'option_desc',
                                'type'  => 'text',
                                'style' => 'width: 500px;',
                                'placeholder' => 'Description here',
                                'value' => $sp['option_desc'],
                    );

                    $this->data['promo_code_id'] = array(
                                'name'  => 'update_code_id',
                                'id'    => 'update_code_id',
                                'type'  => 'hidden',
                                'style' => 'width: 500px;',
                                'placeholder' => 'promo_code_id',
                                'value' => $sp['promo_code_id'],
                    );

                //
                $promo_values = $promo_output = array();
                $promo_output[""] = "Please Discount amount";
                for($i=20;$i<=100;$i++){
                    $promo_output[$i]   = $i . "%";
                }
                $this->data['discount_amt']   = form_dropdown('discount_amt',$promo_output,$sp['discount_amt'],'class="ui search dropdown"');

                $claim_count_output = array();
                for($i=1;$i<=100;$i++){
                    $claim_count_output[$i]   = $i;
                }
                $this->data['claim_count']   = form_dropdown('claim_count',$claim_count_output,$sp['num_claim'],'class="ui search dropdown"');

                    $dd_is_live= array(
                        ""  => "Select Promo status",
                        "0" => "Hold",
                        "1" => "Live"
                    );
                $this->data['is_live']   = form_dropdown('is_live',$dd_is_live,$sp['is_live'],'class="ui search dropdown"');


                if($sp['is_live'] == 1){
                    array_push( $this->data['secret_code'], $this->data['secret_code']['readonly'] = true);
                }

                if($sp['is_onetime'] == 1){
                    $this->data['is_onetime'] = form_checkbox('is_onetime', '1', TRUE);
                }else{
                    $this->data['is_onetime'] = form_checkbox('is_onetime', '1', FALSE);
                }
                $this->data['form_close'] = form_close();

                }
                      
                $this->data['sp'] = $sp;
                $o['update_promo_code_table'] = true;
                $o['promocode_header'] = true;
                $o['msg_type'] = $msg_type;
                $o['msg'] = $msg;
                $o['title'] = "Admin - Update Promo code";
                $o['page'] = 'available_code';
                $o['user'] = $user;
                $o['baseurl'] = $this->baseurl;    
                $this->data['o'] = $o;
                $this->load->view('videoadmin/promocode', $this->data);      
            }
        }

        function delete_promo(){
            $this->admin->check_permission();
            $code_id   = $this->uri->segment(3);
            $user = (array) $this->user;
            $sess_msg       = $this->session->flashdata('msg');
            $sess_msg_type  = $this->session->flashdata('msg_type');
            if($sess_msg){
                $msg = $sess_msg;
                $msg_type = $sess_msg_type;
            }   

        $sql = "select * from promo_code where secret_status = '1' and promo_code_id = '$code_id' LIMIT 1";
            $check_update_code = $this->db->query($sql);
            if($check_update_code->num_rows() == 0){
                $this->session->set_flashdata('msg', 'Invalid promo code, please try again');
                $this->session->set_flashdata('msg_type', 'danger');
                redirect("promocode", 'refresh');
            }else
            {

             $sp = $check_update_code->row_array();
                    #check country if existing
                    $data = array(
                       'secret_status' => 0,
                    );
                    $this->db->simple_query("SET NAMES 'utf-8'");  
                    $data_where = array('promo_code_id'=> $sp['promo_code_id']);       
                    $this->db->set($data);
                    $this->db->update('promo_code', $data, $data_where);

                    $this->session->set_flashdata('msg', 'Promo code successfully deleted.');
                    $this->session->set_flashdata('msg_type', 'success');
                    redirect("promocode", 'refresh');
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
    ?>