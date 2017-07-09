<?php
//die();
$baseurl = 'http://www.tubemasterpro.com/awesome/wowcast/';
//- AW_Model for data processing
class AW_Model {
	public $servername = "localhost";
	public $username   = "nathann6_tubelf";
	public $password   = "e9&C2P3&^z3.";
	public $dbname 	   = "aw_video_chat";
	public $conn;
	
	function __construct () {
		// Create connection
		$this->conn = new mysqli($this->servername, $this->username, $this->password, $this->dbname);
		// Check connection
		if ($this->conn->connect_error) {
		    die("Connection failed: " . $conn->connect_error);
		}
	}
	
	public function check_user ( $user_id ) {
		$sql = "SELECT * FROM users WHERE user_id = '".$user_id."' LIMIT 1";
		$result = $this->conn->query($sql);
		
		return $result->fetch_assoc();
	}
	
	public function check_user_by_id ( $user_id ) {
		$sql = "SELECT * FROM users WHERE id = '".$user_id."' LIMIT 1";
		$result = $this->conn->query($sql);
		
		return $result->fetch_assoc();
	}
	
	public function get_users () {
		$sql = "SELECT * FROM users";
		$result = $this->conn->query($sql);
		
		return $result->fetch_assoc();
		//$this->conn->close();
	}
	
	public function add_user ( $data ) {
		$user_exist = $this->check_user($data['user_id']);
		if ( !$user_exist ) {
			$sql = "INSERT INTO users (is_twitter, twitter_id, twitter_name, user_id, name, is_trainer, photo) VALUES ('".$data['is_twitter']."', '".$data['twitter_id']."', '".$data['twitter_name']."', '".$data['user_id']."', '".$data['name']."', '".$data['is_trainer']."', '".$data['photo']."')";
	
			if ($this->conn->query($sql) === TRUE) {
			    return $this->conn->insert_id;
			} else {
				return $sql;
			    //echo "Error: " . $sql . "<br>" . $conn->error;
			}
		}
		else {
			return $user_exist['id'];
		}
	}
	
	public function update_user_twitter ( $data ) {
		$sql = "UPDATE users SET twitter_id = '".$data['twitter_id']."', twitter_name = '".$data['twitter_name']."' WHERE id = '".$data['id']."'";
		$result = $this->conn->query($sql);
		return true;
	}
	
	public function create_room ( $data ) {
	
		//- Check room if exist first
		$room_slug = str_replace(" ", "-", strtolower($data['name']));
		$new_slug  = $data['trainer_id'] . "-" . $room_slug;
		if ($this->check_room($new_slug)) {
			return array('success' => false, 'msg' => 'room exist' );
		}
		else {
			if ( $this->deactivate_rooms( $data['trainer_id'] ) == true ) {
				if ( $data['banner_type'] == 1 ) {
					$sql = "INSERT INTO rooms (name, session_id, trainer_id, guest_id, active, allow_guest_client, schedule_time, schedule_time_timestamp, banner_image, banner_type, mc_api, mc_list, is_sched, twitter_list_id, short_url, slug, date_added, fb_pixel, tt_pixel, ad_pixel) VALUES ('".$data['name']."', '".$data['session_id']."', '".$data['trainer_id']."', '', '".$data['active']."', '".$data['allow_guest_client']."', '".$data['schedule_time']."', '".$data['schedule_time_timestamp']."', '".$data['banner_image']."', '".$data['banner_type']."', '".$data['mc_api']."', '".$data['mc_list']."', '".$data['is_sched']."', '".$data['twitter_list_id']."', '".$data['short_url']."', '".$new_slug."', '".time()."', '".$data['fb_pixel']."', '".$data['tt_pixel']."', '".$data['ad_pixel']."')";
				}
				else if ( $data['banner_type'] == 2 ) {
					$sql = "INSERT INTO rooms (name, session_id, trainer_id, guest_id, active, allow_guest_client, schedule_time, schedule_time_timestamp, banner_image, banner_type, banner_url, is_sched, twitter_list_id, short_url, slug, date_added, fb_pixel, tt_pixel, ad_pixel) VALUES ('".$data['name']."', '".$data['session_id']."', '".$data['trainer_id']."', '', '".$data['active']."', '".$data['allow_guest_client']."', '".$data['schedule_time']."', '".$data['schedule_time_timestamp']."', '".$data['banner_image']."', '".$data['banner_type']."', '".$data['banner_url']."', '".$data['is_sched']."', '".$data['twitter_list_id']."', '".$data['short_url']."', '".$new_slug."', '".time()."', '".$data['fb_pixel']."', '".$data['tt_pixel']."', '".$data['ad_pixel']."')";
				}
				else {
					$sql = "INSERT INTO rooms (name, session_id, trainer_id, guest_id, active, allow_guest_client, schedule_time, schedule_time_timestamp, is_sched, twitter_list_id, short_url, slug, date_added, fb_pixel, tt_pixel, ad_pixel) VALUES ('".$data['name']."', '".$data['session_id']."', '".$data['trainer_id']."', '', '".$data['active']."', '".$data['allow_guest_client']."', '".$data['schedule_time']."', '".$data['schedule_time_timestamp']."', '".$data['is_sched']."', '".$data['twitter_list_id']."', '".$data['short_url']."', '".$new_slug."', '".time()."', '".$data['fb_pixel']."', '".$data['tt_pixel']."', '".$data['ad_pixel']."')";
				}
		
				if ($this->conn->query($sql) === TRUE) {
				    return array('success' => true, 'slug' => $new_slug );
				} else {
					return array('success' => false, 'msg' => 'database error', 'sql' => $sql );
				    //echo "Error: " . $sql . "<br>" . $conn->error;
				}
			}
			else {
				return false;
			}
		}
	}
	
	public function deactivate_rooms ( $user_id ) {
		$sql = "UPDATE rooms SET active = '0' WHERE trainer_id='".$user_id."' AND active='1'";
		$result = $this->conn->query($sql);
		return true;
	}
	
	public function check_roomname ( $roomname ) {
		$sql = "SELECT * FROM rooms WHERE name = '".$roomname."' LIMIT 1";
		$result = $this->conn->query($sql);
		
		return $result->fetch_assoc();
	}
	
	public function check_room ( $room ) {
		
		$trainer_id = explode("-",$room,2)[0];

		$sql = "SELECT * FROM rooms WHERE slug = '".$room."' AND trainer_id = '".$trainer_id."' LIMIT 1";
		$result = $this->conn->query($sql);
		
		return $result->fetch_assoc();
	}
	
	public function update_room ( $data ) {
		if ($room = $this->check_room($data['room'])) {
			$sql = "UPDATE rooms SET token = '".$data['token']."' WHERE id='".$room['id']."'";
			$result = $this->conn->query($sql);
			return $room;
		}
		else {
			return false;
		}
	}
	
	public function update_room_guest ( $data ) {
		if ($room = $this->check_room($data['room'])) {
			$sql = "UPDATE rooms SET guest_id = '".$data['guest_id']."' WHERE id='".$room['id']."'";
			$result = $this->conn->query($sql);
			return $result;
		}
		else {
			return false;
		}
	}
	
	public function update_trainer_online ( $id, $is_online ) {
		$sql = "UPDATE rooms SET trainer_online = '".$is_online."' WHERE id='".$id."'";
		$result = $this->conn->query($sql);
		return $result;
	}
	
	public function set_archive_id ( $data ) {
		$sql = "UPDATE rooms SET archiveId = '".$data['archive_id']."' WHERE id='".$data['room_id']."'";
		$result = $this->conn->query($sql);
		return $result;
	}
	
	public function set_room_active ( $room ) {
		$sql = "UPDATE rooms SET active = '1' WHERE session_id='".$room."'";
		$result = $this->conn->query($sql);
		return $result;
	}
	
	public function update_allow_guest ( $data ) {
		if ($room = $this->check_room($data['room'])) {
			$sql = "UPDATE rooms SET allow_guest_client = '".$data['allow_guest_client']."' WHERE id='".$room['id']."'";
			$result = $this->conn->query($sql);
			return $room;
		}
		else {
			return false;
		}
	}
	
	public function leave_room ( $data ) {
		$total_views = ($data['total_views']) ? ", total_views = '".$data['total_views']."'" : "";
		$room = $this->check_room($data['room']);
		if ($room['trainer_id'] == $data['user_id']) {
			//$sql = "DELETE FROM rooms WHERE session_id = '".$data['room']."'";
			$sql = "UPDATE rooms SET active = '2', trainer_online = ''".$total_views." WHERE id='".$room['id']."'";
			$result = $this->conn->query($sql);
			return $result;
		}
		else {
			$sql = "UPDATE rooms SET guest_id = ''".$total_views." WHERE id='".$room['id']."'";
			$result = $this->conn->query($sql);
			return false;
		}
	}
	
	public function end_room ( $data ) {
		$room = $this->check_room($data['room']);
		$sql = "UPDATE rooms SET active = '2', trainer_online = '' WHERE id='".$room['id']."'";
		$result = $this->conn->query($sql);
		return $result;
	}
	
	public function add_attendees ( $data ) {
		$room = $this->check_room($data['slug']);
		$check_sql = "SELECT att_id FROM room_attending WHERE room_id = '".$room['id']."' AND user_id = '".$data['user_id']."'";
		$result = $this->conn->query($check_sql);
		
		if ( $result->num_rows <= 0 ) {
			$insert_sql = "INSERT INTO room_attending (room_id, user_id, date_added) VALUES ('".$room['id']."', '".$data['user_id']."', '".time()."')";
			$insert_result = $this->conn->query($insert_sql);
		}
		
		return true;
	}
	
	public function get_rooms ( $active, $trainer_id = null ) {
	
		if ($active == 0) {
			$sql = "SELECT * FROM rooms WHERE active = 0 AND is_sched = 1 ORDER BY schedule_time_timestamp DESC";
		}
		else if ($active == 1) {
			$sql = "SELECT * FROM rooms WHERE active = 1 AND is_sched = 0 ORDER BY date_added DESC";
		}
		else if ($active == 2) {
			$sql = "SELECT * FROM rooms WHERE trainer_id = '".$trainer_id."' AND active = 2 AND is_sched = 0 ORDER BY date_added DESC";
		}
		
		$result = $this->conn->query($sql);
		$return = array();
		
		if ($result->num_rows > 0) {
		    // output data of each row
		    while($row = $result->fetch_assoc()) {
		       $return[] = $row;
		    }
		} else {
			$return = false;
		}
		return $return;
	}
	
	/*
public function get_rooms ( $user_id = null, $is_sched = 0, $is_archive = 0 ) {
	
		if ($is_archive == 0) {
			if ( $user_id ) {
				$sql = "SELECT * FROM rooms WHERE trainer_id = '".$user_id."' AND active != 2 AND is_sched = '".$is_sched."'";
			}
			else {
				$sql = "SELECT * FROM rooms WHERE active = 1 AND is_sched = '".$is_sched."'";
			}
		}
		else {
			$sql = "SELECT * FROM rooms WHERE trainer_id = '".$user_id."' AND active = 2";
		}
		
		$result = $this->conn->query($sql);
		$return = array();
		
		if ($result->num_rows > 0) {
		    // output data of each row
		    while($row = $result->fetch_assoc()) {
		       $return[] = $row;
		    }
		} else {
			$return = false;
		}
		return $return;
	}
*/
}

class AW_WSDL {
	/**
	 * Check for valid account in AW
	 * Sends the email and password to AW wsdl
	 *
	 *
	 * @since    1.0.0
	 * @return    mixed    $value
	 */
	 public function aw_login_valid ( $username, $password ) {
		
		$proc = 'pcChat_UserDetails';
		$args = array(
			'sUsr' 		=> $username,
			'sPwd' 		=> $password
		);
		$response = $this->aw_service_wp($proc,$args);
		return $response;
		/*
echo '<pre>';
		print_r($response->retMsgst->eMessage);
		print_r(count($response->ProductDataStructure));
		echo '</pre>';die();
*/
		
		/*
if ( !$response->retMsgst->eMessage ) {
			return $response->ProductDataStructure;
		}
		else {
			return (string)$response->retMsgst->eMessage[0];
		}
*/
		
	 }
	 
	 public function aw_prod_info ( $id ) {
		
		$proc = 'pcPrdct_DisplayInfo';
		$args = array(
			'argDta' 		=> $id,
			'argIsSale' 	=> '',
			'argUsr'		=> ''
		);
		$response = $this->aw_service_wp($proc,$args);

		/*
echo '<pre>';
		print_r($response->retMsgst->eMessage);
		print_r(count($response->ProductDataStructure));
		echo '</pre>';die();
*/
		
		if ( !$response->retMsgst->eMessage ) {
			return $response->EntityDataStructure;
		}
		else {
			return (string)$response->retMsgst->eMessage[0];
		}
		
	 }
	 
	/**
	 * Retrieve infor from AW wsdl
	 *
	 * @param  string $proc The procedure to call 
	 *
	 * @param  array $args Parameters for the procedure
	 *
	 * @return array                   SOAP response
	 */
	 public function aw_service_wp ( $proc, $args ) {
	 	//$args = array('argDta' => $productID,'argIsSale' => 1,'argUsr' => '');
		$url 		= 'http://203.201.129.15/AWSERVICE_WP_WEB/awws/AWService_WP.awws?wsdl';
		$client 	= new SoapClient($url);
		$response  	= $client->__soapCall( $proc, array($args) );
		
		switch ( $proc ) {
			case 'Trainers_OnlineProduct_Listing':
				$simpleXml = simplexml_load_string($response->Trainers_OnlineProduct_ListingResult);
			break;

			case 'CorporateClientsList':
				$simpleXml = simplexml_load_string($response->CorporateClientsListResult);
			break;
			
			case 'pcPrdct_DisplayInfo':
				$simpleXml = simplexml_load_string($response->pcPrdct_DisplayInfoResult);
			break;
			
			case 'pcChat_UserDetails':
				$simpleXml = $response->pcChat_UserDetailsResult;
			break;
			
		}
		
	 	return $simpleXml;
	 }

}

/*
$sql = "SELECT id, firstname, lastname FROM MyGuests";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        echo "id: " . $row["id"]. " - Name: " . $row["firstname"]. " " . $row["lastname"]. "<br>";
    }
} else {
    echo "0 results";
}
$conn->close();
*/
?>