<?php
session_start();
header("Access-Control-Allow-Origin: *");
require_once("database.php");

require "vendor/twitteroauth/autoload.php";
require "vendor/google/Googl.class.php";
$googl = new Googl('AIzaSyDxah3k-cvgTeyxwH9H1rxV7qLC5n9MhMg');

use Abraham\TwitterOAuth\TwitterOAuth;

$consumer_key 			= 'aQhuFOE4PMyLe1TAy2QDZca6R';
$consumer_secret 		= 'hQnYYzhLNJ7oP84XYcb8bSOPKGoY5TAk3nAahxeONJlyCj1sel';
$access_token_key 		= '531108969-IjzwrJq9WwKBfQ9h6QYJ0DJspCpJsVsvOp59thpv';
$access_token_secret 	= 'RB4D8JE0mNOMEjGty1dN8fQXtzdIJ8hK04Tn6peOI6nYi';

//- OPEN TOK
$loader = require __DIR__ . "/vendor/autoload.php";
$loader->addPsr4('OpenTok\\', __DIR__.'/OpenTok');

use OpenTok\OpenTok;
use OpenTok\MediaMode;
use OpenTok\ArchiveMode;
use OpenTok\Session;
use OpenTok\Role;

$apiKey 	= "45391582";
$apiSecret 	= "5aea02a33fac3eba6bafb85e75bcb0ce80c417c0";
$opentok	= new OpenTok($apiKey, $apiSecret);
//- END OPEN TOK

require "vendor/mailchimp/MailChimp.php";
use \DrewM\MailChimp\MailChimp;

if ($_POST) {
	$post = $_POST;

	switch ($post['type']) {
		
		case 'get_users':
			$aw_model = new AW_Model();
			echo json_encode($aw_model->get_users());
		break;
		
		case 'create_room':
			$file_upload_done = false;
			$banner_image     = '';
			
			if ($_FILES["banner"]) {
				$maxWidth = 300;
				$maxHeight= 150;
				$image_ok = false;
				$file_error = '';
				$target_dir = "assets/banners/";
				$target_file = $target_dir . basename($_FILES["banner"]["name"]);
				$uploadOk = 1;
				$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
				$target_file   = $target_dir . md5(basename($_FILES["banner"]["name"]) . "_" . time() ). '.'.$imageFileType;
				// Check if image file is a actual image or fake image
			    $check = getimagesize($_FILES["banner"]["tmp_name"]);
			    if($check !== false) {
			        //echo "File is an image - " . $check["mime"] . ".";
			        if ($check[0] <= $maxWidth && $size[1] <= $maxHeight) {
			        	$image_ok = true;
			        }
			        else {
				        $file_error = "File dimension exceeds the default size. Please upload image with dimensions of 300x50 pixels.";
						$image_ok = false;
			        }
			    } else {
			        //echo "File is not an image.";
			        $file_error = "File is not an image.";
			        $image_ok = false;
			    }
			    
				if ($image_ok) {
					// Check if file already exists
					if (file_exists($target_file)) {
					    //echo "Sorry, file already exists.";
					    $file_error = "Sorry, file already exists.";
					    $uploadOk = 0;
					}
					// Check file size
					if ($_FILES["banner"]["size"] > 200000) {
					    //echo "Sorry, your file is too large.";
					    $file_error = "Sorry, your file is too large.";
					    $uploadOk = 0;
					}
					// Allow certain file formats
					//if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
					//&& $imageFileType != "gif" ) {
					if($imageFileType != "jpg" && $imageFileType != "jpeg") {
					    //echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
					    //$file_error = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
					    $file_error = "Sorry, only JPG or JPEG.";
					    $uploadOk = 0;
					}
					// Check if $uploadOk is set to 0 by an error
					if ($uploadOk == 0) {
					    //echo "Sorry, your file was not uploaded.";
					    //$file_error = "Sorry, your file was not uploaded.";
					    $file_upload_done = false;
					// if everything is ok, try to upload file
					} else {
					    if (move_uploaded_file($_FILES["banner"]["tmp_name"], $target_file)) {
					        //echo "The file ". basename( $_FILES["banner"]["name"]). " has been uploaded.";
					        $file_upload_done = true;
					        $banner_image = $baseurl.$target_file;
					    } else {
					    	$file_upload_done = false;
							$file_error = "Sorry, there was an error uploading your file.".$target_file;
					        //echo "Sorry, there was an error uploading your file.".$target_file;
					    }
					}
				}
				else {
					$file_upload_done = false;
				}
			}
			else {
				$file_upload_done = true;
			}
			
			
			if ( $file_upload_done ) {
				// Create a session that attempts to use peer-to-peer streaming:
				$session = $opentok->createSession();
				
				// A session that uses the OpenTok Media Router:
				$session = $opentok->createSession(array( 'mediaMode' => MediaMode::ROUTED ));
				
				// Store this sessionId in the database for later use
				$sessionId = $session->getSessionId();
				
				$aw_model = new AW_Model();
				$data = array(
					"name"					=> $post['name'],
					"session_id" 			=> $sessionId,
					"trainer_id"			=> $post['trainer_id'],
					"banner_type"			=> $post['banner_type'],
					"banner_image"			=> $banner_image,
					"allow_guest_client"	=> $post['guest_client'],
					"fb_pixel"				=> htmlspecialchars(addslashes($post['fb_pixel'])),
					"tt_pixel"				=> htmlspecialchars(addslashes($post['tt_pixel'])),
					"ad_pixel"				=> htmlspecialchars(addslashes($post['ad_pixel']))
				);
				
				if ( $post['banner_type'] == 1 ) {
					$data['mc_api']  = $post['mc_api'];
					$data['mc_list'] = $post['mc_list'];
				}
				else if ( $post['banner_type'] == 2 ) {
					$data['banner_url']  = $post['banner_url'];
				}
				
				if ( $post['twitter_list'] == 0 ) {
					$connection3  = new TwitterOAuth($consumer_key, $consumer_secret, $access_token_key, $access_token_secret);
					$lists  = $connection3->get("lists/list", array('user_id' => 531108969));
					$listId = 0;
					foreach ($lists as $list) {
						if ($list->name == $post['twitter_list_name']) {
							$listId = $list->id;
							break;
						}
					}
					if (!$listId) {
						$listCreated = $connection3->post("lists/create", array('name' => $post['twitter_list_name']));
						$data['twitter_list_id'] = $listCreated->id;
					}
					else {
						$data['twitter_list_id'] = $listId;
					}
				}
				else {
					$data['twitter_list_id']   = $post['twitter_list_id'];
				}
				
				if ( isset($post['schedule_time']) && !empty($post['schedule_time']) ) {
					$data['schedule_time'] = $post['schedule_time'];
					$data['is_sched']      = 1;
					$data['active']        = 0;
					$data['schedule_time_timestamp'] = $post['schedule_time_timestamp'];
				}
				else {
					$data['schedule_time'] = null;
					$data['is_sched']      = 0;
					$data['active']        = 1;
					$data['schedule_time_timestamp'] = null;
				}
				
				// Shorten URL
				$longUrl = $baseurl.'room.php?room='.$sessionId;
				$data['short_url'] = $googl->shorten($longUrl);
				
				// Look up long URL
				//$googl->expand('http://goo.gl/fbsS');
				//unset($googl);
				$result = $aw_model->create_room($data);
				if ( $result['success'] == true ) {
					echo json_encode(array('success' => $result['success'], 'msg' => $result['slug'], 'is_sched' => $data['schedule_time']));
				}
				else {
					echo json_encode(array('success' => $result['success'], 'msg' => $result['msg'], 'sql' => $result['sql']));
				}
				//echo $aw_model->create_room($data);
			}
			else {
				echo json_encode(array('success' => false, 'msg' => $file_error));
			}
		break;
		
		case 'update_room':
			$aw_model = new AW_Model();
			$data = array(
				"room"			=> $post['room'],
				"token" 	    => $post['token']
			);
			$result = $aw_model->update_room($data);
			if ($result) {
				echo json_encode(array('success' => true, 'msg' => $result));
			}
			else {
				echo json_encode(array('success' => false, 'msg' => 'Error DB'));
			}
		break;
		
		case 'update_room_guest':
			$aw_model = new AW_Model();
			$data = array(
				"room"			=> $post['room'],
				"guest_id" 	    => $post['guest']
			);
			
			$token = $opentok->generateToken($post['session_id'], array(
			    'role'       => Role::PUBLISHER,
			    'expireTime' => time()+(7 * 24 * 60 * 60), // in one week
			    'data'		 => 'Guest|'.$post['guest']['name'].'|'.$post['guest']['pic'].'|'.$post['guest']['db_id'].'|1|'.json_encode($post['guest']['twitter_data']).'|web'
			));
			
			//$aw_model->update_room_guest($data);
			echo $token;
		break;
		
		case 'leave_room':
			$aw_model = new AW_Model();
			$data = array(
				"room"			=> $post['room'],
				"user_id" 	    => $post['user_id']
			);
			
			if (isset($post['total_views']) && !empty($post['total_views'])) {
				$data['total_views'] = $post['total_views'];
			}
			
			$result = $aw_model->leave_room($data);
			if ($result) {
				//session_unset();
				//session_destroy(); 
			}
			echo $result;
		break;
		
		case 'end_room':
			$aw_model = new AW_Model();
			$data = array(
				"room"			=> $post['room'],
				"token" 	    => $post['token']
			);
			echo $aw_model->end_room($data);
		break;
		
		case 'check_room':
			$aw_model = new AW_Model();
			$room     = $aw_model->check_room($post['room']);
			echo $room;
		break;
		
		case 'set_room_active':
			$aw_model = new AW_Model();
			echo $aw_model->set_room_active($post['room']);
		break;
		
		case 'add_attendees':
			$aw_model = new AW_Model();
			$data = array(
				"slug"			=> $post['slug'],
				"user_id" 	    => $post['user_id']
			);
			echo $aw_model->add_attendees($data);
		break;
		
		case 'post_tweet':
			if ( isset($_SESSION['aw_user']) ) {
				$connection3  = new TwitterOAuth($consumer_key, $consumer_secret, $_SESSION['aw_user']['access_token']['oauth_token'], $_SESSION['aw_user']['access_token']['oauth_token_secret']);
			}
			else {
				$connection3  = new TwitterOAuth($consumer_key, $consumer_secret, $_SESSION['access_token']['oauth_token'], $_SESSION['access_token']['oauth_token_secret']);
			}
			//$connection3  = new TwitterOAuth($consumer_key, $consumer_secret, $access_token_key, $access_token_secret);
			$status = $connection3->post("statuses/update", array('status' => $post['tweet'].' #wowcast '.$post['room_url']));
			echo json_encode(array('result' => $status));
		break;
		
		case 'twitter_list_join':
			$aw_model = new AW_Model();
			$room     = $aw_model->check_room($post['room']);
			if ( isset($_SESSION['aw_user']) ) {
				$create_list_member  = new TwitterOAuth($consumer_key, $consumer_secret, $_SESSION['aw_user']['access_token']['oauth_token'], $_SESSION['aw_user']['access_token']['oauth_token_secret']);
			}
			else {
				$create_list_member  = new TwitterOAuth($consumer_key, $consumer_secret, $_SESSION['access_token']['oauth_token'], $_SESSION['access_token']['oauth_token_secret']);
			}
			//$create_list_member  = new TwitterOAuth($consumer_key, $consumer_secret, $access_token_key, $access_token_secret);
			if (isset($post['user_name']) && !empty($post['user_name'])) {
				$newttuser = $create_list_member->post("lists/members/create", array("list_id" => $room['twitter_list_id'], "screen_name" => $post['user_name']));
			}
			else {
				$newttuser = $create_list_member->post("lists/members/create", array("list_id" => $room['twitter_list_id'], "user_id" => $post['user_id']));
			}
			
			echo true;
		break;
		
		case 'mailchimp_subscribe':
			$aw_model = new AW_Model();
			$room     = $aw_model->check_room($post['room']);
			$MailChimp= new MailChimp($room['mc_api']);
			$result   = $MailChimp->post('lists/'.$room['mc_list'].'/members', array(
                'email_address'     => $post['email'],
                'status'            => 'subscribed'
            ));
			echo json_encode($result);
		break;
		
		case 'get_live_rooms':
			$aw_model = new AW_Model();
			
			$trainer_rooms = $aw_model->get_rooms(1);
	    	
	    	$room_html  = '';
	    	
	    	if ( !empty($trainer_rooms) ) {
		    	foreach ( $trainer_rooms as $trainer_room ) {
		        	$trainer    = $aw_model->check_user_by_id($trainer_room['trainer_id']);
		        	$guest      = $aw_model->check_user_by_id($trainer_room['guest_id']);
			        $room_html .= '<li id="item_stream_'.$trainer_room['session_id'].'" class="feed-item live shadow" data-is-adult-swim="" data-stream_id="'.$trainer_room['session_id'].'" data-id="'.$trainer_room['id'].'" data-friendly_url="'.$trainer_room['name'].'" data-slug="'.$trainer_room['slug'].'">';
			        $room_html .= '<div class="feed-images two-images watch">';
			        
			        if ( $trainer_room['active'] == 1 ) {
			        	$room_html .= '<span class="watch-label"> <i class="fa fa-play"></i> WATCH </span><div class="overlay-live"></div>';
			        }
			        else if ( $trainer_room['active'] == 0 ) {
			        	$room_html .= '<div class="overlay-live"><span class="watch-label"> Go Live NOW! </span></div>';
			        }
			        
			        $has_guest = ($guest != null) ? 'feed-quest' : '';
			        
			        $room_html .= '<div class="feed-image '.$has_guest.'" style="background-image: url('.$trainer['photo'].')"></div>';
			        
			        if($guest != null) {
			        	$room_html .= '<div class="feed-image feed-quest" style="background-image: url('.$trainer['photo'].')"></div>';
			        }
			        
			        $room_html .= '</div>';
			        if ( $trainer_room['active'] == 1 ) {
			        	$room_html .= '<div class="action"><a href="" class="on-air triangle-topleft"></a></div>';
			        }
			        $room_html .= '<div class="cast-details watch">';
			        
			        $room_html .= '<div class="banner">';
			        //$room_html .= '<img class="img-responsive" src="'.$trainer_room['banner_image'].'" />';
			        $room_html .= '</div">';
			        
			        $room_html .= '<div class="info bg-primary">';
			        $room_html .= '<h2 class="title">'.$trainer_room['name'].'</h2>';
			        $room_html .= '<ul class="host-list">';
			        //$room_html .= '<li class="host">';
			        //$room_html .= '<a class="profile-link host-name" data-user_id="'.$trainer['id'].'">'.$trainer['name'].'</a>,';
			        //$room_html .= '</li>';
			        //$room_html .= '<li class="host">';
			        //$room_html .= '<a class="profile-link host-name" data-user_id="'.$guest['id'].'">'.$guest['name'].'</a>';
			        //$room_html .= '</li>';
			        $room_html .= '</ul></div></div>';
			        $room_html .= '<div class="activity"><div class="meta">';
			        
			        $viewers = ($trainer_room['total_views']) ? $trainer_room['total_views'] : 0;
			        $watching = 0;
			        
			        /*$room_html .= '<p class="participant-count">'.$viewers.' viewers</p>';*/
			        if ( strtolower($user['type']) == 'trainer' ) {
			        	$room_html .= '<p class="live-count"><button id="end-room" data-trainer_id="'.$trainer['id'].'" class="btn">End Room</button></p>';
			        }
			        //$room_html .= '<p class="live-count">7 watching now</p>';
			        $room_html .= '</div><div class="row row-centered">';
					$room_html .=		        '<div class="col-sm-5 col-centered">';
					$room_html .=		          '<div class="item">';
					$room_html .=		            '<div class="content">';
					$room_html .=		              '<label class="f-views">'.$viewers.'</label>';
					$room_html .=		              '<font>Total Viewers</font>';
					$room_html .=		            '</div>';
					$room_html .=		          '</div>';
					$room_html .=		        '</div>';
							
					$room_html .=		        '<div class="col-sm-2 col-centered">';
					$room_html .=		          '<div class="item">';
					$room_html .=		            '<div class="content"><div class="border-sep"></div></div>';
					$room_html .=		          '</div>';
					$room_html .=		        '</div>';
							
					$room_html .=		        '<div class="col-sm-5 col-centered">';
					$room_html .=		          '<div class="item">';
					$room_html .=		            '<div class="content">';
					$room_html .=		              '<label class="f-watch">'.$watching.'</label>';
					$room_html .=		              '<font>Watching Now</font>';
					$room_html .=		            '</div>';
					$room_html .=		          '</div>';
					$room_html .=		        '</div>';
					$room_html .=		      '</div>';
					$room_html .=		    '</div></div>';
			        $room_html .= '</li>';
		        }
	    	}
	    	else {
	    		$room_html  = '<li class="text-center" style="margin: 50px auto;font-size: 5rem; color:#cad4e2;">';
	    		$room_html .= '<h1 style="font-size: 5rem;">No Live Casts!</h1>';
	    		$room_html .= '<a class="create-channel button aw-green-btn" style="font-size: 2rem;">Go Live NOW!</a></li>';
	    	}
		    
		    echo $room_html;
	        
	    break;
	    
	    case 'get_scheduled_rooms':
			$aw_model = new AW_Model();
			/*
if ( strtolower($post['user_type']) == 'trainer' ) {
	        	$trainer_rooms = $aw_model->get_rooms($post['db_id'], 1);
	    	}
	    	else {
		    	$trainer_rooms = $aw_model->get_rooms(null, 1);
	    	}
*/
			$trainer_rooms = $aw_model->get_rooms(0);
			
	    	
	    	$room_html  = '';
	    	
	        if ( !empty($trainer_rooms) ) {
		    	foreach ( $trainer_rooms as $trainer_room ) {
		        	$trainer    = $aw_model->check_user_by_id($trainer_room['trainer_id']);
			        $room_html .= '<li id="item_stream_'.$trainer_room['session_id'].'" class="feed-item scheduled shadow" data-is-adult-swim="" data-stream_id="'.$trainer_room['session_id'].'" data-id="'.$trainer_room['id'].'" data-friendly_url="'.$trainer_room['name'].'" data-slug="'.$trainer_room['slug'].'">';
			        $room_html .= '<div class="feed-images one-image">';
			        
			        $room_html .= '<div class="feed-image" style="background-image: url('.$trainer['photo'].')"></div>';
			        $room_html .= '</div>';
			        
			        $room_html .= '<div class="cast-details watch upcoming">';
			        
			        //$room_html .= '<div class="banner">';
			        //$room_html .= '<img class="img-responsive" src="'.$trainer_room['banner_image'].'" />';
			        //$room_html .= '</div">';
			        
			        $room_html .= '<div class="info bg-primary">';
			        $room_html .= '<h2 class="title">'.$trainer_room['name'].'</h2>';
			        $room_html .= '<ul class="host-list">';
			        //$room_html .= '<li class="host">';
			        //$room_html .= '<a class="profile-link host-name" data-user_id="'.$trainer['id'].'">'.$trainer['name'].'</a>,';
			       // $room_html .= '</li>';
			        //$room_html .= '</ul></div></div>';
			        $room_html .= '</ul></div>';
			        
			        $viewers = 0;
			        
			        $room_html .= '<div class="row row-centered">';
					$room_html .=		        '<div class="col-sm-12 col-centered">';
					$room_html .=		          '<div class="item">';
					$room_html .=		            '<div class="content">';
					$room_html .=		              '<label class="f-views">'.$viewers.'</label>';
					$room_html .=		              '<font>Subscribers</font><br/>';					
					$room_html .=					  '<button class="btn btn-success aw-green-btn" style="margin-top: 20px;">Subscribe</button>';
					$room_html .=		            '</div>';
					$room_html .=		          '</div>';
					$room_html .=		        '</div>';
					$room_html .=		      '</div></div>';
			        
					/*
$date = date_create($trainer_room['schedule_time']);
					echo date_format($date,"Y/m/d");
*/
			        
			        
			        $room_html .= '<p class="scheduled-time" data-timestamp="'.$trainer_room['schedule_time_timestamp'].'"></p>';
			        $room_html .= '</li>';
			        
		        }
	    	}
	    	else {
	    		$room_html  = '<li class="text-center" style="margin: 50px auto;font-size: 5rem; color:#cad4e2;">';
	    		$room_html .= '<h1 style="font-size: 5rem;">No Scheduled Casts!</h1>';
	    		$room_html .= '<a class="create-channel button aw-green-btn" style="font-size: 2rem;">Go Live NOW!</a></li>';
	    	}
			
			echo $room_html;
	    break;
	    
	    case 'allow_guest':
	    	$aw_model = new AW_Model();
	    	$data = array(
				"room"					=> $post['room'],
				"allow_guest_client" 	=> $post['allow']
			);
			echo $aw_model->update_allow_guest($data);
	    break;
	    
	    case 'start_archive':
	    	$aw_model = new AW_Model();
	    	$room = $aw_model->check_room($post['slug']);
	    	$archive = $opentok->startArchive($room['session_id']);
	    	$data = array(
				"archive_id"	=> $archive->id,
				"room_id" 		=> $room['id']
			);
			echo $aw_model->set_archive_id($data);
	    break;
	    
	    case 'stop_archive':
	    	$aw_model = new AW_Model();
	    	$room = $aw_model->check_room($post['slug']);
	    	// Stop an Archive from an archiveId (fetched from database)
			$opentok->stopArchive($room['archiveId']);
			$archive = $opentok->getArchive($archiveId);
	    break;
	    
	    case 'email_invites':
	    	$aw_model = new AW_Model();
	    	$room = $aw_model->check_room($post['slug']);
	    	$from   = trim($post['from']);
	    	$emails = trim($post['emails']);
	    	
	    	$subject  = "Invite to Live WOW!Cast";
            $message .= "Hey! <br><br>";
            $message .= "I've invited you to join my live WOW!Cast happening right now. <br> Just click the link below on a computer using Chrome or Firefox. <br> <br>";
            $message .= '<a href="'.$room['short_url'].'" target="_blank">Click here to join room.</a>';
            
            // Always set content-type when sending HTML email
			$headers = "MIME-Version: 1.0" . "\r\n";
			$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
			
			// More headers
			$headers .= 'From: <'.$from.'>' . "\r\n";
			
			mail($emails,$subject,$message,$headers);
	    	echo true;
	    break;
	    
	    case 'get_archive_rooms':
			$aw_model = new AW_Model();
			
			$trainer_rooms = $aw_model->get_rooms(2, $post['db_id']);
	    	
	    	$room_html  = '';
	    	
	        if ( !empty($trainer_rooms) ) {
		    	foreach ( $trainer_rooms as $trainer_room ) {
		        	$trainer    = $aw_model->check_user_by_id($trainer_room['trainer_id']);
		        	$guest      = null;
		        	if ( isset($trainer_room['guest_id']) && !empty($trainer_room['guest_id']) ) {
		        		$guest  = $aw_model->check_user_by_id($trainer_room['guest_id']);
		        	}
		        	
			        $room_html .= '<li id="item_stream_'.$trainer_room['session_id'].'" class="feed-item scheduled" data-is-adult-swim="" data-stream_id="'.$trainer_room['session_id'].'" data-id="'.$trainer_room['id'].'" data-friendly_url="'.$trainer_room['name'].'" data-slug="'.$trainer_room['slug'].'">';
			        
			        if ( $guest ) {
			        	$room_html .= '<div class="feed-images two-images">';
			        }
			        else {
			        	$room_html .= '<div class="feed-images one-image">';
			        }
			        
			        $room_html .= '<div class="feed-image" style="background-image: url('.$trainer['photo'].')"></div>';
			        $room_html .= '</div>';
			        
			        if ( $guest ) {
			        	$room_html .= '<div class="feed-image" style="background-image: url('.$guest['photo'].')"></div>';
			        }
			        
			        $room_html .= '<div class="stream-details watch">';
			        
			        $room_html .= '<div class="banner">';
			        $room_html .= '<img class="img-responsive" src="'.$trainer_room['banner_image'].'" />';
			        $room_html .= '</div">';
			        
			        $room_html .= '<div class="info">';
			        $room_html .= '<h2 class="title">'.$trainer_room['name'].'</h2>';
			        $room_html .= '<ul class="host-list">';
			        $room_html .= '<li class="host">';
			        $room_html .= '<a class="profile-link host-name" data-user_id="'.$trainer['id'].'">'.$trainer['name'].'</a>,';
			        $room_html .= '</li>';
			        
			        if ( $guest ) {
				        $room_html .= '<li class="host">';
				        $room_html .= '<a class="profile-link host-name" data-user_id="'.$guest['id'].'">'.$guest['name'].'</a>';
				        $room_html .= '</li>';
			        }
			        
			        $room_html .= '</ul></div></div>';
			        $room_html .= '<div class="activity"><div class="meta">';
			        
			        $viewers = ($trainer_room['total_views']) ? $trainer_room['total_views'] : 0;
			        
			        $room_html .= '<p class="participant-count">'.$viewers.' viewers</p>';
			        if ( strtolower($user['type']) == 'trainer' ) {
			        	$room_html .= '<p class="live-count"><button id="end-room" data-trainer_id="'.$trainer['id'].'" class="btn">End Room</button></p>';
			        }
			        //$room_html .= '<p class="live-count">7 watching now</p>';
			        $room_html .= '</div></div>';
			        $room_html .= '</li>';
			        
		        }
	    	}
	    	else {
	    		//$room_html  = '<li class="text-center" style="margin: 50px auto;font-size: 5rem; color:#cad4e2;">';
	    		//$room_html .= '<h1 style="font-size: 5rem;">No Archive Casts!</h1>';
	    		//$room_html .= '<a class="create-channel button aw-green-btn" style="font-size: 2rem;">Go Live NOW!</a></li>';
	    		//sample data
	    		$trainer_room['session_id'] = 0;
	    		$trainer_room['id'] = 0;
	    		$trainer_room['name'] = 'Test';
	    		$trainer_room['slug'] = 'slug';
	    		$viewers = 0;
	    		$trainer['photo'] = 'https://pbs.twimg.com/profile_images/378800000475790995/59c903a9675f2c871e89b1bb12b4be58_reasonably_small.jpeg';
	    		$trainer_room['banner_image'] = 'http://www.tubemasterpro.com/awesome/wowcast/assets/banners/1448488652.jpg';
	    		$trainer_room['schedule_time_timestamp'] = '01/01/1970';
	    		//sample data
	    		
		        $room_html .= '<li id="item_stream_'.$trainer_room['session_id'].'" class="feed-item scheduled shadow" data-is-adult-swim="" data-stream_id="'.$trainer_room['session_id'].'" data-id="'.$trainer_room['id'].'" data-friendly_url="'.$trainer_room['name'].'" data-slug="'.$trainer_room['slug'].'">';
		        $room_html .= '<div class="feed-images one-image">';
		        
		        $room_html .= '<div class="feed-image" style="background-image: url('.$trainer['photo'].')"></div>';
		        $room_html .= '</div>';
		        
		        $banner_text = ($trainer_room['banner_image'] != null) ? 'archived' : 'upcoming';
		        
		        if($trainer_room['banner_image'] != null) {
			        $room_html .= '<div class="cast-archived">';
			        $room_html .= '<img class="img-responsive" src="'.$trainer_room['banner_image'].'" />';
			        $room_html .= '</div>';
		        }
		        
		        $room_html .= '<div class="cast-details watch '.$banner_text.'">';
		        
		        $room_html .= '<div class="info bg-primary">';
		        $room_html .= '<h2 class="title">'.$trainer_room['name'].'</h2>';
		        $room_html .= '<ul class="host-list">';
		        //$room_html .= '<li class="host">';
		        //$room_html .= '<a class="profile-link host-name" data-user_id="'.$trainer['id'].'">'.$trainer['name'].'</a>,';
		       // $room_html .= '</li>';
		        //$room_html .= '</ul></div></div>';
		        $room_html .= '</ul></div>';
		        
		        $viewers = 0;
		        
		        $room_html .= '<div class="row row-centered">';
				$room_html .=		        '<div class="col-sm-12 col-centered">';
				$room_html .=		          '<div class="item">';
				$room_html .=		            '<div class="content">';
				$room_html .=		              '<label class="f-views">'.$viewers.'</label>';
				$room_html .=		              '<font>Attended</font><br/>';					
				$room_html .=					  '<button class="btn btn-success aw-green-btn" style="margin-top: 20px;">View</button>';
				$room_html .=		            '</div>';
				$room_html .=		          '</div>';
				$room_html .=		        '</div>';
				$room_html .=		      '</div></div>';
		        
				/*
$date = date_create($trainer_room['schedule_time']);
				echo date_format($date,"Y/m/d");
*/
		        
		        
		        $room_html .= '<p class="scheduled-time" data-timestamp="'.$trainer_room['schedule_time_timestamp'].'"></p>';
		        $room_html .= '</li>'; 		
	    		
	    		
	    	}
			
			echo $room_html;
	    break;
		
		case 'trainer_token':
			$token = file_get_contents("http://www.tubemasterpro.com/tokbox/server-mao.php?p=generate_publisher_as_trainer");
			echo $token;
		break;
		
		case 'trainer_popup':
			$token = file_get_contents("http://www.tubemasterpro.com/tokbox/server-mao.php?p=generate_publisher_as_trainer_popup");
			echo $token;
		break;
		
		case 'guest':
			$token = file_get_contents("http://www.tubemasterpro.com/tokbox/server-mao.php?p=generate_publisher_as_guest");
			echo $token;
		break;
		
	}
}

?>