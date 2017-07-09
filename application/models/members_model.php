<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Members_model extends CI_Model {
	public $table = 'paypal';
	
	public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->user = $this->ion_auth->user()->row();
    }
    
    public function get_subscription_details( $user_id ) {
	    $result = false;

        if($this->user->is_jvzoo == 1){
            $sql = "select transaction from jvzoo where user_id = '{$this->user->id}' order by jvzoo_id desc LIMIT 1";
            $check_jv = $this->db->query($sql);
            if($check_jv->num_rows == 0){
                $result[0]->p_status = "N/A";
            }else{
                $get_jv = $check_jv->row_array();
                if($get_jv['transaction'] == "SALE" || $get_jv['transaction'] == "BILL"){
                    $result[0]->p_status = "ACTIVE";
                }else{
                   $result[0]->p_status = "N/A"; 
                }
            }
        }else{

        	$query = $this->db->get_where( $this->table, array('user_id' => $user_id, 'ppstatus' => 1) );
        	if ($query->num_rows() > 0)	{
    		   $result = $query->result();
    		}
        }
		return $result[0];
    
    }
    
    public function update_status ( $user_id, $status ) {
    	$data = array('p_status' => $status );
	    $this->db->update( $this->table, $data, array('user_id' => $user_id) );
    	return true;
    }

    public function count_user_export (){
        $user = $this->ion_auth->user()->row();
        $sql = "select count(id) as count_export from campaign_list where user_id  = '$user->id'
                and exported = '1' LIMIT 1";
        $check_exp = $this->db->query($sql);
        if($check_exp->num_rows() == 0){
            return 0;
        }else{
            $c_exp = $check_exp->row_array();
            return $c_exp['count_export'];
        }
    }
        
}