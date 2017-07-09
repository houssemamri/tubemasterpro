<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Target_model extends CI_Model {

    protected $table = 'targets';

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }
    
    public function check_videos_remaining ( $id, $user_id ) {
        $target = $this->get( $id );
        $num_tdata = unserialize($target->data);
        
        $return = '';
        if ( $this->ion_auth->in_group(3) ) {
            $num_total = 10 - count($num_tdata);
        }
        else {
            $num_total = 500 - count($num_tdata);
        }
        
        if ( $num_total == 0 ) {
            return false;
        }
        return $num_total;
    }
    
    public function get_keywords ( $user_id ) {
        $result = false;
        $query = $this->db->get_where( 'keywords', array('user_id' => $user_id) );
        if ($query->num_rows() > 0) {
           $result = $query->result()[0];
        }
        return $result;
    }
    
    public function save_keyword ( $id = null, $data = array() ) {
        if ( !is_null( $id ) ) {
            $this->db->update( 'keywords', $data, array('id' => $id) );
            return true;
        }
        else {
            $this->db->insert( 'keywords', $data);
            return $this->db->insert_id();
        }
    }

    public function delete_keywords ( $user_id ) {
        $this->db->where('user_id', $user_id);
        $this->db->delete('keywords');
        return true;
    }

    public function save ( $id = null, $data = array() ) {
        
        $user = $this->ion_auth->user()->row();

        //- Check if update
        if ( !is_null( $id ) ) {
            $target = $this->get( $id );
            
            if ( $this->ion_auth->in_group(3) ) {
                $num_data  = unserialize($data['data']);
                $num_tdata = unserialize($target->data);
                $num_total = 10 - count($num_tdata);
                
                if ( $num_total <= 0 ) {
                    return false;
                }
                else {
                    $new_data  = array();
                    $add_total = 0;
                    
                    if ( count($num_data) > $num_total ) {
                        $add_total = $num_total;
                    }
                    else {
                        $add_total = count($num_data);
                    }
                    // echo 'total == '.$add_total."<br>";
                    for ( $i=0; $i<$add_total; $i++) {
                        array_push($new_data, $num_data[$i] );
                    }
                    $data['data'] = serialize($new_data);
                }
            }

            $this->db->update( $this->table, $data, array('id' => $id) );
            return true;
        }
        else {
            if ( $this->ion_auth->in_group(3) ) {
                $targets = $this->get_all( $user->id );
                if ( $targets && count($targets) > 0 ) {
                    return 'demo';
                }
            }
            
            $this->db->insert( $this->table, $data);
            return $this->db->insert_id();
        }

    }
    
    public function update_status ( $id, $data ) {
        $this->db->update( $this->table, $data, array('id' => $id) );
        return true;
    }

    public function delete_target_list ( $id ) {
        $this->db->where('id', $id);
        $this->db->delete($this->table);
        return true;
    }
    
    public function delete_targets ( $id ) {
        $this->db->where('user_id', $id);
        $this->db->delete($this->table);
        return true;
    }
    
    public function update_target_name($id, $name) {
        $data = array(
            'name'  => $name
        );
        
        $this->db->where('id', $id);
        $this->db->update($this->table, $data);
        
        return true;
    }

    public function get ( $id ) {
        $query = $this->db->get_where( $this->table, array('id' => $id) );
        if ($query->num_rows() > 0) {
           $result = $query->result();
        }
        return $result[0];
    }

    public function get_all ( $user_id ) {
        $result = false;
        $this->db->order_by('name', 'ASC');
        $query = $this->db->get_where( $this->table, array('user_id' => $user_id) );
        if ($query->num_rows() > 0) {
           $result = $query->result();
        }
        return $result;
    }
    
    public function get_targets_by_name ( $name, $user_id ) {
        $result = false;
        $query = $this->db->get_where( $this->table, array('name' => $name, 'user_id' => $user_id) );
        if ($query->num_rows() > 0) {
           $result = $query->result();
        }
        return $result;
    }
    
    //- Campaigns
    public function get_overview ( $cid ) {
    	$result = false;
	    $query = $this->db->get_where( 'overview_campaign', array('cid' => $cid), 1 );
        if ($query->num_rows() > 0) {
           $overview = $query->result()[0];
		   $newquery = $this->db->get_where( 'overview', array('id' => $overview->oid), 1 );
		   if ($newquery->num_rows() > 0) {
               $result = $newquery->result()[0];
		   }
        }
        return $result;
    }
    
    public function get_campaign_uploaded ( $cid ) {
        $result = false;
        $this->db->select('*');
		$this->db->from('campaign_list');
		$this->db->join('campaigns_uploaded', 'campaigns_uploaded.cid = campaign_list.id');
		$this->db->where('cid', $cid); 
        $this->db->order_by('date_uploaded', 'DESC');
		$this->db->limit(1);
		$query = $this->db->get();
        //$query = $this->db->get_where( 'campaign_uploaded', array( 'cid' => $cid ), 1 );
        if ($query->num_rows() > 0) {
           $result = $query->result()[0];
        }
        return $result;
    }
    
    public function save_campaign_uploaded ( $id = null, $data ) {
        if ( !is_null($id) && $id != 0 ) {
	    	$this->db->update( 'campaigns_uploaded', $data, array('id' => $id));
	    	return true;
    	}
    	else {
        	$this->db->insert( 'campaigns_uploaded', $data);
			return $this->db->insert_id();
    	}
    }
    
    public function get_campaigns ( $user_id ) {
        $result = false;
        $this->db->order_by('name', 'ASC');
        $query = $this->db->get_where( 'campaign_list', array('user_id' => $user_id) );
        if ($query->num_rows() > 0) {
           $result = $query->result();
        }
        return $result;
    }
    
    public function get_campaign ( $id, $user_id ) {
        $result = false;
        $query = $this->db->get_where( 'campaign_list', array('id' => $id, 'user_id' => $user_id) );
        if ($query->num_rows() > 0) {
           $result = $query->result()[0];
        }
        return $result;
    }
    
    public function get_campaign_by_name ( $name, $user_id ) {
        $result = false;
        $query = $this->db->get_where( 'campaign_list', array('name' => $name, 'user_id' => $user_id) );
        if ($query->num_rows() > 0) {
           $result = $query->result()[0];
        }
        return $result;
    }
    
    public function check_duplicates ( $duplicate_id, $user_id ) {
        $query 		= $this->db->get_where( 'campaign_list', array( 'is_duplicate' => $duplicate_id, 'user_id' => $user_id) );
        $count		= $query->num_rows() + 1;
    	$campaign 	= $this->get_campaign( $duplicate_id, $user_id );
    	$return		= $campaign->name . ' - ' . $count;
        return $return;
    }
    
    public function copy_campaign ( $id, $user_id ) {
    	$campaign = $this->get_campaign( $id, $user_id );
    	
    	if ( $campaign->is_duplicate != 0 ) {
	    	$duplicate_id 	= $campaign->is_duplicate;
    		$new_name   	= $this->check_duplicates( $duplicate_id, $user_id );
    	}
    	else {
	    	$duplicate_id 	= $id;
    		$new_name   	= $this->check_duplicates( $id, $user_id );
    	}
    	
    	$new_campaign = array(
    		'name'				=> $new_name,
    		'display_url'		=> $campaign->display_url,
    		'destination_url'	=> $campaign->destination_url,
    		'video_ads'			=> $campaign->video_ads,
    		'target_lists'		=> $campaign->target_lists,
    		'daily_budget'		=> $campaign->daily_budget,
    		'max_cpv'			=> $campaign->max_cpv,
    		'start_date'		=> $campaign->start_date,
    		'end_date'			=> $campaign->end_date,
    		'language'			=> $campaign->language,
    		'mbm_sign'			=> $campaign->mbm_sign,
    		'mbm_value'			=> $campaign->mbm_value,
    		'age'				=> $campaign->age,
    		'gender'			=> $campaign->gender,
    		'delivery_method'	=> $campaign->delivery_method,
    		'countries'			=> $campaign->countries,
    		'user_id'			=> $campaign->user_id,
    		'optimizer_id'		=> $campaign->optimizer_id,
    		'is_duplicate'		=> $duplicate_id,
    		'exported'			=> 0,
    		'status'			=> 1
    	);
    	$this->db->insert('campaign_list', $new_campaign);
		return $this->db->insert_id();
    }
    
    public function save_campaign ( $id = null, $data ) {
    	if ( !is_null($id) && $id != 0 ) {
	    	$this->db->update( 'campaign_list', $data, array('id' => $id));
	    	return true;
    	}
    	else {
    		$data['is_duplicate'] = 0;
    		//$data['exported'] 	  = 0;
    		$data['status']       = 1;
        	$this->db->insert( 'campaign_list', $data);
			return $this->db->insert_id();
    	}
    }
    
    public function delete_campaign ( $id, $user_id ) {
        $this->db->where('id', $id);
        $this->db->where('user_id', $user_id);
        $this->db->delete('campaign_list');
        return true;
    }
    
    public function multiple_delete_campaign ( $ids ) {
    	$this->db->where_in('id', $ids);
	    $this->db->delete('campaign_list');
        return true;
    }
    
    private function generate_token () {
		//$token = md5(time() . "_" . rand(0,9));
		$token = '-(copy)-'.time();
		return $token;
	}

}