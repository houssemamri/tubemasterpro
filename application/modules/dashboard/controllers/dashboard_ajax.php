<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Dashboard_ajax extends Ajax_Controller {

	protected $video_limit = 7000;

	private $yt_dev_key = array(
		"AIzaSyDg1hQx7_9wD0DEbHxkT6bMi7K8kk8uZKs"
	);

	function __construct () {
		parent::__construct();
        $this->load->library('authentication', NULL, 'ion_auth');
        $this->load->model('Logs_model', 'logs');
        // $this->load->model('target_model');
        // $user = $this->ion_auth->user()->row();

        // echo $this->target_model->create();
        // die();
	}

	function generate_ytkey () {
		$key = array_rand($this->yt_dev_key);
		return $this->yt_dev_key[$key];
	}

    function check_yt_ads ($ytlink)
    {
        $_url = htmlspecialchars_decode($ytlink);
        $ch_subs = curl_init();

        curl_setopt($ch_subs, CURLOPT_URL, $_url);
        curl_setopt($ch_subs, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch_subs, CURLOPT_SSL_VERIFYPEER, false);
        $subs_return = curl_exec($ch_subs);
        $str = "";
        $str .= $subs_return;
        curl_close($ch_subs);

        $hasAds = substr_count($subs_return, '"allow_html5_ads":"1"');

        // if ( intval($hasAds) == 1 ) {
        //     $script = "$('#search_result').html('Has Ads.');";
        // }
        // else {
        //     $script = "$('#search_result').html('No Ads.');";
        // }

        // $this->response->script($script);
        // $this->response->send();

        return intval($hasAds);
    }

    function keywords() {
    	// $main    = $this->session->flashdata('video_search_main');
    	// $page    = $this->session->flashdata('video_search_page');
    	// $entries = $this->session->flashdata('video_search_entries');

    	$keyword = $this->input->get_post('keyword');
    	$main    = $this->input->get_post('main');
    	$page    = $this->input->get_post('page');
    	$entries = $this->input->get_post('entries');
    	$sug     = $this->input->get_post('sug');

    	$videoUrl   = site_url('dashboard/video_search?keyword=');
    	$channelUrl = site_url('dashboard/channel_search?keyword=');
    	$base_url   = base_url();
    	$pageLength = ( isset($entries) && !empty($entries) ) ? $entries : 10;
    	$page       = ( isset($page) && !empty($page) ) ? $page : 0;

		$this->logs->insert_logs("keyword_search", "Keyword Search for - ".$keyword);

	    $script = <<< JS
	    	if ( !localStorage.keywords ) {
	    		var key_arr = [];
	    		key_arr.push('$keyword');
	    		localStorage.setItem("keywords", JSON.stringify(key_arr));
	    	}
	    	else {
	    		var key_arr = $.parseJSON(localStorage.keywords);
	    		if ( $.inArray('$keyword', key_arr) < 0 ) {
		    		key_arr.push('$keyword');
		    		localStorage.setItem("keywords", JSON.stringify(key_arr));
	    		}
	    	}

	    	if ( !localStorage.videos ) {
	    		var key_arr = [];
	    		key_arr.push('$keyword');
	    		localStorage.setItem("videos", JSON.stringify(key_arr));
	    	}
	    	else {
	    		var key_arr = $.parseJSON(localStorage.videos);
	    		if ( $.inArray('$keyword', key_arr) < 0 ) {
		    		key_arr.push('$keyword');
		    		localStorage.setItem("videos", JSON.stringify(key_arr));
	    		}
	    	}

			if ( !localStorage.channels ) {
	    		var key_arr = [];
	    		key_arr.push('$keyword');
	    		localStorage.setItem("channels", JSON.stringify(key_arr));
	    	}
	    	else {
	    		var key_arr = $.parseJSON(localStorage.channels);
	    		if ( $.inArray('$keyword', key_arr) < 0 ) {
		    		key_arr.push('$keyword');
		    		localStorage.setItem("channels", JSON.stringify(key_arr));
	    		}
	    	}

	    	$('.main_loader').show();
			$('.main_loader .loader').fadeIn(100);
			$('.main_loader .loader').html( 'Searching...' );

			var ht = '<table id="keywords_table" class="display table table-bordered"><thead><tr><th>Keyword</th><th></th><th></th></tr></thead><tbody></tbody></table>';
			ht = ht + "";

			$("#search_result").html(ht);

			var first = "a", last = "z" , letter = "";
			var sIndex = 0, fIndex = 0, totalKeywords = 0;
			for(var i = first.charCodeAt(0); i <= last.charCodeAt(0); i++) {
				fIndex++;
			    letter = ( eval("String.fromCharCode(" + i + ")") );
				var string = '$keyword' + ' ' + letter;
				// console.log((string));

				jQTubeUtil.suggest((string), function(response){
					// console.log(response);
					sIndex++;

					for(v in response.suggestions){
						var sug = response.suggestions[v];
						var vurl = '#';
						var curl = '#';
						var tr_class = '';
						var vid_checkmark= '';
						var cha_checkmark= '';

						if ( localStorage.keywords ) {
							var key_list= $.parseJSON(localStorage.keywords);
							//console.log(key_list);
							if ( $.inArray(sug, key_list) >= 0 ) {
								tr_class = 'class="alert-success"';
							}

							if ( localStorage.videos ) {
								var vid_list= $.parseJSON(localStorage.videos);
								if ( $.inArray(sug, vid_list) >= 0 ) {
									tr_class = 'class="alert-success"';
									vid_checkmark = "<span style='padding:0 5px;' class='text-success'>✔</span>";
								}
							}

							if ( localStorage.channels ) {
								var cha_list= $.parseJSON(localStorage.channels);
								if ( $.inArray(sug, cha_list) >= 0 ) {
									tr_class = 'class="alert-success"';
									cha_checkmark = "<span style='padding:0 5px;' class='text-success'>✔</span>";
								}
							}
						}

						$("#search_result tbody").append("<tr "+tr_class+" item='"+(parseInt(v)+1)+"' id='keyword-"+(parseInt(v)+1)+"'><td style='border-right:none;'>"+sug+"</td><td style='border-left:none;border-right:none'><a class=\"btn btn-primary video_key_search\" href='#' data-keyword='"+sug+"' >Video Search</a>"+vid_checkmark+"</td><td style='border-left:none;border-right:none'><a class=\"btn btn-primary channel_key_search\" href='#' data-keyword='"+sug+"' >Channel Search</a>"+cha_checkmark+"</td></tr>");
						totalKeywords++;
					}

					if ( fIndex == sIndex ) {
						if ( totalKeywords > 0 ) {
							$('#keywords_table').show(100);
							$("#search_result").slideDown();
							var k_table = $('#keywords_table').DataTable({
								"searching"	    : false,
								"ordering"      : false,
								"pageLength"    : parseInt('$pageLength'),
								"drawCallback"  : function( settings ) {
							        if ( $('.video_key_search').length > 0 ) {
										$('.video_key_search').each(function(){
											$(this).on('click', function(e){
												e.preventDefault();
												$('.main_loader').show();
												$('.main_loader .loader').fadeIn(100);
												$('.main_loader .loader').html( 'Searching...' );
												var keyword = $(this).data('keyword');
												var page    = parseInt($('.pagination li.active a').text()) - 1;
												var entries = $('#keywords_table_length label select').val();

												if ( !localStorage.videos ) {
										    		var key_arr = [];
										    		key_arr.push(keyword);
										    		localStorage.setItem("videos", JSON.stringify(key_arr));
										    	}
										    	else {
										    		var key_arr = $.parseJSON(localStorage.videos);
										    		if ( $.inArray(keyword, key_arr) < 0 ) {
											    		key_arr.push(keyword);
											    		localStorage.setItem("videos", JSON.stringify(key_arr));
										    		}
										    	}

												var request = $.ajax({
													url: '$base_url'+'dashboard/dashboard_ajax/save_video_keyword_search',
													type: "POST",
													data: {
														main   : '$keyword',
														page   : page,
														entries: entries
													},
												});

												request.done(function(msg){
													window.location.href = '$videoUrl'+keyword;
												});
											});
										});
									}

									if ( $('.channel_key_search').length > 0 ) {
										$('.channel_key_search').each(function(){
											$(this).on('click', function(e){
												e.preventDefault();
												$('.main_loader').show();
												$('.main_loader .loader').fadeIn(100);
												$('.main_loader .loader').html( 'Searching...' );
												var keyword = $(this).data('keyword');
												var page    = parseInt($('.pagination li.active a').text()) - 1;
												var entries = $('#keywords_table_length label select').val();

												if ( !localStorage.channels ) {
										    		var key_arr = [];
										    		key_arr.push(keyword);
										    		localStorage.setItem("channels", JSON.stringify(key_arr));
										    	}
										    	else {
										    		var key_arr = $.parseJSON(localStorage.channels);
										    		if ( $.inArray(keyword, key_arr) < 0 ) {
											    		key_arr.push(keyword);
											    		localStorage.setItem("channels", JSON.stringify(key_arr));
										    		}
										    	}

												var request = $.ajax({
													url: '$base_url'+'dashboard/dashboard_ajax/save_channel_keyword_search',
													type: "POST",
													data: {
														main   : '$keyword',
														page   : page,
														entries: entries
													},
												});

												request.done(function(msg){
													window.location.href = '$channelUrl'+keyword;
												});
											});
										});
									}
							    }
							});
							k_table.page($page).draw(false);
							// $('#keywords_table tbody tr').each(function(){
							// 	var td_text = $(this).find('td:first-child').text();
							// 	if ( localStorage.keywords ) {
							// 		var key_list= $.parseJSON(localStorage.keywords);
							// 		if ( $.inArray(td_text, key_list) >= 0 ) {
							// 			$(this).addClass('alert-success');
							// 		}
							// 	}
							// });
						}
						else {
							$("#search_result").append('<div class="alert alert-lg alert-danger" >SORRY! We can\'t find anything matching your search term.</div>');
							$('#keywords_table').hide(100);
							$("#search_result").slideDown();
						}
						$('.main_loader .loader').html( 'Done!' );
						$('.main_loader .loader').fadeOut(100,function(){
							$('.main_loader').hide();
						});
					}

				});
			}
JS;
	    $this->response->script($script);
        $this->response->send();
    }

    function save_video_keyword_search () {

    	$main       = $this->input->post('main');
    	$page       = $this->input->post('page');
    	$entries    = $this->input->post('entries');
    	$keyword    = $this->input->post('keyword');

    	$this->session->set_flashdata('video_search_main', $main);
    	$this->session->set_flashdata('video_search_page', $page);
    	$this->session->set_flashdata('video_search_entries', $entries);
    	$this->session->set_flashdata('video_search_keyword', $keyword);

    	echo true;die();
    }

    function save_channel_keyword_search () {

    	$main       = $this->input->post('main');
    	$page       = $this->input->post('page');
    	$entries    = $this->input->post('entries');
    	$keyword    = $this->input->post('keyword');

    	$this->session->set_flashdata('channel_search_main', $main);
    	$this->session->set_flashdata('channel_search_page', $page);
    	$this->session->set_flashdata('channel_search_entries', $entries);
    	$this->session->set_flashdata('channel_search_keyword', $keyword);

    	echo true;die();
    }

    function get_target_count () {
    	$this->load->model('target_model');
        $user    = $this->ion_auth->user()->row();
	    $targets = $this->target_model->get_all($user->id);
	    $return  = array();
	    $return['free']  = $this->ion_auth->in_group(3);
	    $return['count'] = count($targets);
	    echo json_encode($return);
	    die();
    }

    function get_targets() {
    	$this->load->model('target_model');
        $user = $this->ion_auth->user()->row();
    	$target_id = trim( $this->input->get_post('target_id') );
        $targets = $this->target_model->get_all( $user->id );
        $options = '';

        if ( $targets ) {
            foreach ($targets as $key => &$value) {
                $value->data = unserialize($value->data);
                if ( !$value->status && $value->id != $target_id ) {

	                if ( $this->ion_auth->in_group(3) ) {
	                	if ( count($value->data) <= 10 ) {
	                		$options .= "<option value='".$value->id."'>".$value->name."</option>";
	                	}
	                }
	                else {
	                	$options .= "<option value='".$value->id."'>".$value->name."</option>";
	                }
                }
            }
        }

	    echo $options;die();
    }

    function move_target_list_videos () {
    	$this->load->model('target_model');
        $user = $this->ion_auth->user()->row();

        $ytids  = $this->input->post('ytids');
        $old_id = trim( $this->input->post('old_target') );
        $new_id = trim( $this->input->post('new_target') );

	    $old_target = $this->target_model->get( $old_id );
	    $new_target = $this->target_model->get( $new_id );

	    $old_videos = unserialize($old_target->data);
	    $new_videos = ( empty($new_target->data) ) ? false : unserialize($new_target->data);

	    $removed_videos = array();

		//- remove the videos from the old target
    	foreach ( $old_videos as $key => $value ) {
    		foreach ( $ytids as $ytid ) {
	    		if ( $ytid == $value['ytid'] ) {
	    			array_push($removed_videos, $value);
		    		unset($old_videos[$key]);
		    		break;
	    		}
    		}
    	}
    	$old_arr = array_values($old_videos);
		$old_target_data = array(
	    	'data'	=> serialize( $old_arr )
        );
	    $this->target_model->save( $old_id, $old_target_data );

    	//- check if there are duplicates, skip adding
    	if ( $new_videos ) {
    		foreach ( $removed_videos as $removed_video ) {
    			$exists = false;
	    		foreach ( $new_videos as $new_video ) {
		    		if ( $new_video['ytid'] == $removed_video['ytid'] ) {
		    			$exists = true;
		    			break;
		    		}
	    		}
	    		if ( !$exists ) {
		    		array_push($new_videos, $removed_video);
	    		}
    		}
    	}
    	else {
	    	$new_videos = $removed_videos;
    	}

		$new_target_data = array(
	    	'data'	=> serialize( $new_videos )
        );
	    $this->target_model->save( $new_id, $new_target_data );


		$this->logs->insert_logs("move_target_videos", "Moved ".count($new_videos)." Videos from target ".$old_target->name." to target ".$new_target->name);

        echo true;die();
        //$result = $this->target_model->move_update( $target_id, $target_data );
    }

    function add_target () {
    	$this->load->model('target_model');
        $user = $this->ion_auth->user()->row();
    	$name = trim( $this->input->get_post('target_name') );
    	$is_name_valid = $this->target_model->get_targets_by_name($name, $user->id);
    	$result = false;

    	if ( !$is_name_valid ) {

	        $target_data = array(
	        	'name'		=> $name,
	        	'data'		=> '',
	        	'user_id'	=> $user->id
	        );

	        $result = $this->target_model->save( null, $target_data );

			$this->logs->insert_logs("add_target", "Add target - ".$name);
	    }

	    echo $result;die();
    }

    function rename_target () {
    	$this->load->model('target_model');
        $user = $this->ion_auth->user()->row();
    	$target_id   = trim( $this->input->get_post('target_id') );
    	$target_name = trim( $this->input->get_post('target_name') );

    	$is_name_valid = $this->target_model->get_targets_by_name($target_name, $user->id);
    	if ( !$is_name_valid ) {
    		$result = $this->target_model->update_target_name($target_id, $target_name);
			$this->logs->insert_logs("rename_target", "Renamed target - ".$is_name_valid->name.' to '.$name);
	    }
	    echo $result;die();
    }

    function delete_target_list () {
	    $this->load->model('target_model');
    	$target_id = $this->input->get_post('target_id');
    	$target    = $this->target_model->get( $target_id );

		$this->logs->insert_logs("delete_target", "Deleted target ".$target->name);
    	$result = $this->target_model->delete_target_list($target_id);

	    echo $result;die();
    }

    function delete_target_lists () {
	    $this->load->model('target_model');
    	$target_ids = $this->input->get_post('target_ids');

    	foreach ( $target_ids as $target_id ) {
    		$target = $this->target_model->get( $target_id );
			$this->logs->insert_logs("delete_target", "Deleted target ".$target->name);
    		$result = $this->target_model->delete_target_list($target_id);
    	}

	    echo $result;die();
    }

    function delete_target_list_videos () {
	    $this->load->model('target_model');
    	$target_id = $this->input->get_post('target_id');
    	$ytids     = $this->input->get_post('ytids');
    	$target    = $this->target_model->get( $target_id );
    	$videos    = unserialize($target->data);
    	$removed_videos = 0;
    	//echo json_encode($videos);die();
    	foreach ( $videos as $key => $video ) {
    		foreach ( $ytids as $ytid ) {
	    		if ( $ytid == $video['ytid'] ) {
		    		unset($videos[$key]);
		    		$removed_videos++;
		    		break;
	    		}
    		}
    	}
    	$new_arr = array_values($videos);
		$target_data = array(
	    	'data'	=> serialize( $new_arr )
        );

        $result = $this->target_model->save( $target_id, $target_data );

		$this->logs->insert_logs("delete_videos", "Deleted ".$removed_videos." videos from target - ".$target->name);

	    echo json_encode( $result );die();
    }

    function save_target () {
    	$this->load->model('target_model');
        $user = $this->ion_auth->user()->row();
        $targets = null;

    	$name   = trim( $this->input->get_post('target_name') );
    	//- Check for target name duplicate
    	$is_name_valid = $this->target_model->get_targets_by_name($name, $user->id);
    	if ( !$is_name_valid ) {
	    	$ytdata = $this->input->get_post('ytdata');
	    	$target_id = $this->input->get_post('target_id');

	    	if ( empty( $target_id ) ) {
	    		$target_id = null;
	    	}

	        $target_data = array(
	        	'name'		=> $name,
	        	'data'		=> serialize( $ytdata ),
	        	'user_id'	=> $user->id
	        );

	        $result = $this->target_model->save( $target_id, $target_data );

			$this->logs->insert_logs("save_target", "Saved ".count($ytdata)." videos to target - ".$name);

	        if ( $result && empty( $target_id ) ) {
	        	$data = $this->target_model->get( $result );
	        	$ytdata = unserialize($data->data);
	        	$new_target = "<option value='".$data->id."' data-ytdata='".json_encode( $ytdata )."'>".$data->name."</option>";
	        }
	        else if ( !$result ) {
		        $new_target = 'freemium';
	        }
	    }
	    else {
        	$new_target = false;//array('error'=>true);
	    }
	    echo $new_target;die();
    }

    function check_target_name () {
    	$this->load->model('target_model');
        $user = $this->ion_auth->user()->row();
    	$name   = trim( $this->input->get_post('target_name') );
    	$return = false;

	    //- Check for target name duplicate
    	$is_name_valid = $this->target_model->get_targets_by_name($name, $user->id);

    	if ( $is_name_valid ) {
	    	$return = false;
    	}
    	else {
	    	$return = true;
    	}

        echo $return;die();
    }

    function update_target () {
    	$this->load->model('target_model');
        $user    = $this->ion_auth->user()->row();

    	$ytdata    = $this->input->get_post('ytdata');
    	$target_id = $this->input->get_post('target_id');
    	$target    = $this->target_model->get( $target_id );
		$num_tdata = ( empty($target->data) ) ? false : unserialize($target->data);
		$new_tdata = array();
		$new_target= array();

		if ( $num_tdata ) {
			foreach ( $ytdata as $tdata ) {
				$exists = false;
				foreach ( $num_tdata as $key => $value ) {
					if ( $value['ytid'] == $tdata['ytid'] ) {
						$exists = true;
						// unset($num_tdata[$key]);
						break;
					}
				}

				if ( !$exists ) {
					array_push($num_tdata, $tdata);
				}
			}
			$new_tdata = $num_tdata;//array_values($num_tdata);
		}
		else {
			$new_tdata = ( isset( $ytdata) && !empty( $ytdata ) ) ? $ytdata : array();
		}

		$target_data = array(
			'data' => serialize($new_tdata)
		);

        $result = $this->target_model->save( $target_id, $target_data );

		$this->logs->insert_logs("update_target", "Updated ".count($new_tdata)." videos to target - ".$target->name);

        if ( $result ) {
        	$new_target = $ytdata;
        }
        else {
	        $new_target['freemium'] = 'true';
        }

	    echo json_encode( $new_target );die();
    }

    function add_video () {
	    $this->load->model('target_model');

    	$ytid      = $this->input->get_post('vid_id');
    	$target_id = $this->input->get_post('target_id');

	    if ( ( isset( $ytid) && !empty( $ytid ) ) && ( isset( $target_id) && !empty( $target_id ) ) ) {
	    	$this->load->library('google');
			$client = $this->google;

			$client->setDeveloperKey($this->generate_ytkey());

			// Define an object that will be used to make all API requests.
			$this->load->library('youtube', $client);
			$youtube = $this->youtube;

		    $data = $this->target_model->get( $target_id );
		    $ytvideos = ( empty($data->data) ) ? false : unserialize($data->data);

		    if ( $ytvideos ) {
			    foreach ( $ytvideos as $ytvideo ) {
				    if ( $ytid == $ytvideo['ytid'] ) {
				    	$result['msg'] = 'duplicate';
				    	echo json_encode($result);die();
					    break;
				    }
			    }
		    }
		    else {
			    $ytvideos = array();
		    }

		    $videosResponse = $youtube->videos->listVideos('id, snippet, statistics', array(
				'id' => $ytid,
			));

			$ytlink = "https://www.youtube.com/watch?v=".$videosResponse['items'][0]['id'];
			$hasAds = $this->check_yt_ads($ytlink);

			$result = array();

			if ( $hasAds ) {
				$videoTitle   = $videosResponse['items'][0]['snippet']['title'];
				$videoThumb   = $videosResponse['items'][0]['snippet']['thumbnails']['default']['url'];

				$ytdata = array();
			    $ytdata['ytid'] 	= $videosResponse['items'][0]['id'];
			    $ytdata['title'] 	= urlencode( $videoTitle );

			    array_push($ytvideos, $ytdata);

		        $target_data = array(
		        	'data' => serialize( $ytvideos )
		        );

		        $return = $this->target_model->save( $target_id, $target_data );

				$this->logs->insert_logs("add_video", "Added ".$ytlink." to target - ".$data->name);

		        if ( $return ) {
		        	$result['msg'] = 'ok';
		        }
		        else {
			        $result['msg'] = 'free';
		        }
	        }
	        else {
		        $result['msg'] = 'no ads';
	        }

	        echo json_encode($result);die();
		}
    }

    function get () {

    	$this->load->model('target_model');
    	$result = $this->input->get_post('id');

        if ( $result && empty( $target_id ) ) {
        	$new_target = $this->target_model->get( $result );
        }

        echo json_encode( $new_target );die();
    }

    function get_target_videos () {
    	$target_id = trim( $this->input->get_post('target_id') );

    	if ( isset($target_id) && !empty($target_id) ) {
		    $this->load->library('google');
			$client = $this->google;

			$client->setDeveloperKey($this->generate_ytkey());

			// Define an object that will be used to make all API requests.
			$this->load->library('youtube', $client);
			$youtube = $this->youtube;

			$this->load->model('target_model');
	        $user = $this->ion_auth->user()->row();

	        $videos = $this->target_model->get( $target_id );
	        $videos->data = ( empty($videos->data) ) ? false : unserialize($videos->data);
	        $array_vals = $videos->data;

	        if ( $videos->data ) {

				$current_key  = 0;
				# Merge video ids

				$html = '';
				$videoIds  = '';
				$num_vids  = count($videos->data);

				do {
					$videoResults = array();

					for ( $i=0; $i<=$num_vids; $i++ ) {
						if ( $i < 50 && $current_key < $num_vids) {
							array_push($videoResults, $videos->data[$current_key]['ytid']);
							$current_key++;
						}
					}

					$videoIds = join(',', $videoResults);
					/*
echo 'START<br>';
					echo '<pre>';
					print_r($videoResults);
					echo '</pre>';
					echo 'END<br>';
*/
					$videosResponse = $youtube->videos->listVideos('id, snippet, statistics', array(
						'id' => $videoIds,
					));

				    foreach ($videosResponse['items'] as $key => $value) {
						$ytdata = array();
						$ytlink = "https://www.youtube.com/watch?v=".$value['id'];

					    $videoTitle   = $value['snippet']['title'];
					    $videoThumb   = $value['snippet']['thumbnails']['default']['url'];

						$html .= "<tr data-video-id='".$value['id']."' data-list-id='".$target_id."'>";
						$html .= "<td><input type='checkbox' class='ytcheckbox' /></td>";
						$html .= "<td style='text-align:center;'><img alt='".$videoTitle."' src='".$videoThumb."' width='50px' height='28px' /></td>";
						$html .= "<td><a class='video_link' target='_blank' href='".$ytlink."'>".$videoTitle."</a></td>";
						$html .= "<td>".number_format( $value['statistics']['viewCount'] )."</td>";
						$html .= "<td>".number_format( $value['statistics']['likeCount'] )."</td>";
						$html .= "<td>".number_format( $value['statistics']['dislikeCount'] )."</td>";
						$html .= "<td>".number_format( $value['statistics']['commentCount'] )."</td>";
						$html .= "<td class='text-center'><button type='submit' class='btn btn-sm btn-danger delete_video' title='' data-toggle='tooltip' data-placement='top' data-original-title='Delete Target' data-action='".site_url('dashboard/dashboard_ajax/delete_target_list_videos')."'>X</button></td>";
						$html .= "</tr>";

					}
			    } while ( $current_key < $num_vids );


				echo $html;die();
			}
			echo '<tr>No Videos Found</tr>';die();
		}
    }


    function video_search () {

	    $keyword   = trim( $this->input->get_post('keyword') );
	    $viewCount = trim( $this->input->get_post('viewCount') );
		$pageToken = $this->input->get_post('page');

	    if ( isset( $keyword) && !empty( $keyword ) || isset( $pageToken) && !empty( $pageToken ) ) {

			$this->logs->insert_logs("video_search", "Search for video with keyword ".$keyword);

		    $this->load->library('google');
	        $client = $this->google;

			$client->setDeveloperKey($this->generate_ytkey());

			// Define an object that will be used to make all API requests.
	        $this->load->library('youtube', $client);
	        $youtube = $this->youtube;

			$searchResponse = $youtube->search->listSearch('snippet', array(
		      'q'          => $keyword,
		      'maxResults' => 1,
		      'type'       => 'video',
		      'order' 	   => 'relevance',
		      'pageToken'  => $pageToken
		    ));

		    $videoResults = array();
			$return = array();

			$html = '';
			$hasAds = 0;

            if ( $searchResponse['pageInfo']['totalResults'] > 0 ) {

                $blocked_channels = array( "UCV_fM0hIEaVkVEEnc_lTRKQ", "UCiWvKytMaOifxK5h8YZDi5w", "UC-3lZaW7CYCjabIkcViKw2w" );

				if ( !in_array( $searchResponse['items'][0]['snippet']['channelId'], $blocked_channels ) ) {

					foreach ($searchResponse['items'] as $searchResult) {
						array_push($videoResults, $searchResult['id']['videoId']);
					}
					$videoIds = join(',', $videoResults);

					$videosResponse = $youtube->videos->listVideos('id, snippet, statistics', array(
						'id' => $videoIds,
					));

					foreach ($videosResponse['items'] as $key => $value) {
						$ytdata = array();
						$ytlink = "https://www.youtube.com/watch?v=".$value['id'];
						$hasAds = $this->check_yt_ads($ytlink);

						if ( $hasAds ) {
						    $videoTitle   = $value['snippet']['title'];
						    $videoThumb   = $value['snippet']['thumbnails']['default']['url'];

						    $ytdata['ytid'] 	= $value['id'];
						    $ytdata['title'] 	= urlencode( $videoTitle );

							$html .= "<tr id='".$searchResponse['nextPageToken']."' data-ytdata='".json_encode($ytdata)."'>";
							$html .= "<td><input type='checkbox' class='ytcheckbox' /></td>";
							$html .= "<td><img alt='".$videoTitle."' src='".$videoThumb."' width='120px' height='65px' /></td>";
							$html .= "<td>".$videoTitle."</td>";
							$html .= "<td><a class='video_link' target='_blank' href='".$ytlink."'>".$ytlink."</a></td>";
							$html .= "<td>".number_format( $value['statistics']['viewCount'] )."</td>";
							$html .= "<td>".number_format( $value['statistics']['likeCount'] )."</td>";
							$html .= "<td>".number_format( $value['statistics']['dislikeCount'] )."</td>";
							$html .= "<td>".number_format( $value['statistics']['commentCount'] )."</td>";
							$html .= "<td>".date( 'Y-d-m', strtotime( $value['snippet']['publishedAt'] ) )."</td>";
							$html .= "</tr>";
						}
						else {
							break;
						}
					}
				}
			}

			$return['hasAds'] = $hasAds;
			$return['html'] = $html;
			$return['next'] = $searchResponse['nextPageToken'];

		    $this->save_keyword( $keyword, $return['next'] );

			echo json_encode( $return );die();
		}
    }

    function channel_search () {
	    $keyword   = trim( $this->input->get_post('keyword') );
	    $viewCount = trim( $this->input->get_post('viewCount') );
		$pageToken = $this->input->get_post('page');

		if ( isset( $keyword) && !empty( $keyword ) || isset( $pageToken) && !empty( $pageToken ) ) {

			$this->logs->insert_logs("channel_search", "Search for channel with keyword ".$keyword);

		    $this->load->library('google');
	        $client = $this->google;

			$client->setDeveloperKey($this->generate_ytkey());

			// Define an object that will be used to make all API requests.
	        $this->load->library('youtube', $client);
	        $youtube = $this->youtube;

			$searchResponse = $youtube->search->listSearch('snippet', array(
		      'q'          => $keyword,
		      'maxResults' => 1,
		      'type'       => 'channel',
		      'order' 	   => 'relevance',
		      'pageToken'  => $pageToken
		    ));

		    $channelResults = array();
			$return = array();

			$html = '';
			$hasVideos = 0;

			if ( $searchResponse['pageInfo']['totalResults'] > 0 ) {
				//- Australiawow   = UCV_fM0hIEaVkVEEnc_lTRKQ
				//- John Hardy     = UCiWvKytMaOifxK5h8YZDi5w
				//- Brandee Sweesy = UC-3lZaW7CYCjabIkcViKw2w
				$blocked_channels = array( "UCV_fM0hIEaVkVEEnc_lTRKQ", "UCiWvKytMaOifxK5h8YZDi5w", "UC-3lZaW7CYCjabIkcViKw2w" );

				// echo $searchResponse['items'][0]['id']['channelId'].'<br>';

				if ( !in_array( $searchResponse['items'][0]['id']['channelId'], $blocked_channels ) ) {
					// echo 'Ok <br> '.$searchResponse['nextPageToken'];
					# Merge video ids
					foreach ($searchResponse['items'] as $searchResult) {
						array_push($channelResults, $searchResult['id']['channelId']);
					}
					$channelIds = join(',', $channelResults);

					$channelsResponse = $youtube->channels->listChannels('id, contentDetails, snippet, statistics', array(
						'id' => $channelIds,
						'maxResults' => 1
					));

					foreach ($channelsResponse['items'] as $key => $value) {
						$hasVideos = $value['statistics']['videoCount'];
						if ( $hasVideos > 0 ) {
							$chlink = 'https://www.youtube.com/channel/'.$value['id'];

							// Call the API's channels.list method with mine parameter to fetch authorized user's channel.
						    $listResponse = $youtube->playlistItems->listPlaylistItems('contentDetails, snippet', array(
						        'playlistId' => $value['contentDetails']['relatedPlaylists']['uploads'],
						        'maxResults' => 1
						    ));

						    $channelTitle     = $value['snippet']['title'];
						    $channelThumb     = $value['snippet']['thumbnails']['default']['url'];
						    $recentVideoDate  = strtotime($listResponse['items'][0]['snippet']['publishedAt']);
						    $recentVideoThumb = $listResponse['items'][0]['snippet']['thumbnails']['default']['url'];
						    $recentVideoId    = $listResponse['items'][0]['snippet']['resourceId']['videoId'];

							$html .= "<tr data-channel-title='".$channelTitle."' data-videos='".$hasVideos."' data-chid='".$value['id']."' data-upsid='".$value['contentDetails']['relatedPlaylists']['uploads']."'>";
							$html .= "<td class='text-center'><a href='".$chlink."'><img alt='".$channelTitle."' src='".$channelThumb."' width='50px' height='50px' /></a></td>";
							$html .= "<td><a href='".$chlink."'>".$channelTitle."</a></td>";
							$html .= "<td>".number_format( $value['statistics']['subscriberCount'] )."</td>";
							$html .= "<td>".number_format( $value['statistics']['viewCount'] )."</td>";
							$html .= "<td>".number_format( $value['statistics']['videoCount'] )."</td>";
							$html .= "<td class='text-center'><a class='video_link' data-video-id='".$recentVideoId."' target='_blank' href='#'><img alt='".$channelTitle."' src='".$recentVideoThumb."' width='90px' height='50px' /></a><p>".date('Y-m-d', $recentVideoDate)."</p></td>";
							/*
if ( $this->ion_auth->in_group(3) ) {
								$html .= "<td class='text-center'><a href='#' class='btn btn-primary show_freemium'>Extract Videos</a></td>";
							}
							else {
*/
								$html .= "<td class='text-center'><a href='#' class='btn btn-primary extract_videos'>Extract Videos</a></td>";
							//}
							$html .= "</tr>";
						}
					}
				}
			}

			$return['html'] = $html;
			$return['next'] = $searchResponse['nextPageToken'];
			$return['hasVideos'] = $hasVideos;

			echo json_encode( $return );die();
		}
    }

    function check_recent_searches () {
	    $user = $this->ion_auth->user()->row();
	    $this->load->model('target_model');
		$result = $this->target_model->get_keywords( $user->id );
		$return = array();
		$return['exist'] = false;
		$return['next']  = '';

		$keyword = trim( $this->input->get_post('keyword') );

		if ( $result ) {
			$keywords = unserialize($result->data);
			foreach ( $keywords as $data ) {
				if ( $data['keyword'] == $keyword ) {
					$return['exist'] = true;
					$return['next']  = $data['next_token'];
					break;
				}
			}
		}
		echo json_encode( $return );die();
    }

    function save_keyword ( $keyword, $next = '' ) {
	    $user = $this->ion_auth->user()->row();
		$this->load->model('target_model');
	    //- check if keyword exists, update next_token. Else, insert keyword
		$result_keyword = $this->target_model->get_keywords( $user->id );

		if ( $result_keyword ) {
			$prev_keywords = unserialize($result_keyword->data);
			$key_exist = false;

			foreach ( $prev_keywords as $key => &$value ) {
				if ( $value['keyword'] == $keyword ) {
					$value['next_token'] = $next;
					$key_exist = true;
					break;
				}
			}

			if ( !$key_exist ) {
				array_push($prev_keywords, array("keyword" => $keyword, "next_token" => $next));
			}

	        $keyword_data = array(
	        	'data' => serialize($prev_keywords)
	        );

	        $this->target_model->save_keyword( $result_keyword->id, $keyword_data );
		}
		else {
	        $key_data = array();
	        array_push($key_data, array("keyword" => $keyword, "next_token" => $next));

	        $keyword_data = array(
	        	'data'		=> serialize($key_data),
	        	'user_id'	=> $user->id
	        );

	        $this->target_model->save_keyword( null, $keyword_data );
		}
    }

    function delete_keywords () {
		$this->load->model('target_model');
	    $user = $this->ion_auth->user()->row();
	    $result = $this->target_model->delete_keywords( $user->id );
	    echo $result;die();
    }

    function reset_tour() {
    	$this->delete_keywords();
	    $this->load->model('target_model');
	    $user = $this->ion_auth->user()->row();
	    $result = $this->target_model->delete_targets( $user->id );

	    echo $result;die();
    }

    function update_tour () {
    	$status = $this->input->get_post('status');
    	if ( isset( $status ) && !empty( $status ) ) {
    		$user = $this->ion_auth->user()->row();
			$data = array(
				'has_tour' => $status,
			);
			$this->ion_auth->update($user->id, $data);
    	}
    }

    function bulk_video_search () {
	    $user   = $this->ion_auth->user()->row();
		$return = false;
		$new_target_id = null;

		$this->load->model('target_model');
		$this->load->library('google');

        $client = $this->google;
		$client->setDeveloperKey($this->generate_ytkey());

		// Define an object that will be used to make all API requests.
        $this->load->library('youtube', $client);
        $youtube = $this->youtube;

		$keyword = trim( $this->input->get_post('keyword') );

		if ( isset( $keyword ) && !empty( $keyword ) ) {
			$target_id        = trim( $this->input->get_post('target_id') );
		    $next             = trim( $this->input->get_post('next') );
		    $video_limit      = $this->video_limit;
		    $videos_remaining = $video_limit;
			$data = '';

			$this->logs->insert_logs("bulk_search", "Search for videos up to 7000 with the keyword - ".$keyword);

		    if ( $videos_remaining > 0 ) {
			    if ( isset( $target_id ) && !empty( $target_id ) ) {
					$new_target_id = $target_id;
				    $target = $this->target_model->get( $target_id );
				    $target_name = $target->name;

				    $data = empty($target->data) ? '' : $target->data;

			    	// update status to pending by setting it to 1
			    	$status_data = array(
			    		'data'   => $data,
			        	'status' => 1
			        );
					$this->target_model->save( $target_id, $status_data );

				    // $videos_remaining = $video_limit - count($data);
				    $return = true;
			    }
			    else {
			    	$target_name = trim( $this->input->get_post('target_name') );

					if ( isset( $target_name ) && !empty( $target_name ) ) {
						//$is_name_valid = $this->target_model->get_targets_by_name($target_name, $user->id);
						//if ( !$is_name_valid ) {
							//- save new target
							$target_data = array(
					        	'name'		=> $target_name,
					        	'data'		=> $data,
					        	'user_id'	=> $user->id,
					        	'status'    => 1
					        );
					        $new_target_id = $this->target_model->save( null, $target_data );
					        $return = true;
						//}
						//else {
						//	$return = false;
						//}
					}
					else {
						$return = false;
					}
			    }

		    	//- Check data if empty, then make it an array
		    	$data = (empty($data) && $data == '') ? array() : unserialize($data);

			    if ( $return ) {

				    //- check if keyword exists, update next_token. Else, insert keyword
					$this->save_keyword($keyword, $next);

			        $next_page    = $next;
				    $videos_added = 0;
				    // echo $next_page;
			        do {

					    $searchResponse = $youtube->search->listSearch('snippet', array(
					      'q'          => $keyword,
					      'maxResults' => 1,
					      'type'       => 'video',
					      'order' 	   => 'relevance',
					      'pageToken'  => $next_page
					    ));

					    $videoResults = array();
						$hasAds = 0;

						if ( $searchResponse['pageInfo']['totalResults'] > 0 ) {
							//- Australiawow   = UCV_fM0hIEaVkVEEnc_lTRKQ
							//- John Hardy     = UCiWvKytMaOifxK5h8YZDi5w
							//- Brandee Sweesy = UC-3lZaW7CYCjabIkcViKw2w
							$blocked_channels = array( "UCV_fM0hIEaVkVEEnc_lTRKQ", "UCiWvKytMaOifxK5h8YZDi5w", "UC-3lZaW7CYCjabIkcViKw2w" );
							foreach ($searchResponse['items'] as $key => $value) {
								if ( !in_array( $value['snippet']['channelId'], $blocked_channels ) ) {
									$ytlink = "https://www.youtube.com/watch?v=".$value['id']['videoId'];
									$hasAds = $this->check_yt_ads($ytlink);

									if ( $hasAds ) {
										$videoTitle      = $value['snippet']['title'];
										$ytdata          = array();
									    $ytdata['ytid']  = $value['id']['videoId'];
									    $ytdata['title'] = urlencode( $videoTitle );
									    array_push($data, $ytdata);
										$videos_added++;
										if ( $videos_added >= $videos_remaining ) break;
									}
								}
							}
						}

					    $next_page = $searchResponse['nextPageToken'];
				    } while ( isset($next_page) && $next_page != '' && $videos_added < $videos_remaining );

				    $email_data = array();

				    if ( $videos_added > 0 ) {
					    //- Save to target
					    $new_target_data = array(
				        	'data'   => serialize( $data ),
				        	'status' => 0
				        );
				        $this->target_model->save( $new_target_id, $new_target_data );

						$result_keyword = $this->target_model->get_keywords( $user->id );
				        $prev_keywords = unserialize($result_keyword->data);

						foreach ( $prev_keywords as $key => &$value ) {
							if ( $value['keyword'] == $keyword ) {
								$value['next_token'] = $next_page;
								break;
							}
						}
				        $keyword_data = array(
				        	'data' => serialize($prev_keywords)
				        );
				        $this->target_model->save_keyword( $result_keyword->id, $keyword_data );

				        $email_data['subject'] = "SUCCESS! Videos found for ".$keyword."!";
						$email_data['body']    = "Hey <strong>".$user->first_name."</strong> - just letting you know that ".$videos_added." videos were added to your <strong>".$target_name."</strong> target list using your search term of <strong>".$keyword."</strong>. Login and select which ones you want to save or remove in the Target List section.<br><br>TubeMasterPro Team";
			        }
			        else {
			        	$new_target_data = array(
				        	'data'   => serialize( $data ),
			    			'status' => 0
				        );

				        $this->target_model->save( $new_target_id, $new_target_data );
				        $email_data['subject'] = "SEARCH FAIL! No Videos found for ".$keyword."!";
						$email_data['body']    = "Meh. Sorry about that :/  We searched but failed at life trying to come up with monetised videos for the search term \"".$keyword."\". Go ahead and login and try a different search term.<br><br>TubeMasterPro Team";
			        }

			        $email_data['email'] = $user->email;
					$this->send_confirmation_email($email_data);
		        }//- End of if $return
		        else {
		        	$new_target_data = array(
			        	'data'   => serialize( $data ),
		    			'status' => 0
			        );
			        $this->target_model->save( $new_target_id, $new_target_data );

		        	$email_data['subject'] = "SEARCH FAIL!";
					$email_data['body']    = "Meh. Sorry about that :/ Something went wrong with the search. Go ahead and login and try again.<br><br>TubeMasterPro Team";
		        	$email_data['email']   = $user->email;
					$this->send_confirmation_email($email_data);
		        }
		    }//- End of if $video_remaining
		    else {
			    $email_data['subject'] = "SEARCH FAIL! Can not add anymore videos to target ".$target_name."!";
				$email_data['body']    = "Meh. Sorry about that :/  You have reached the maximum of 500 videos. Please login and create another Target list.<br><br>TubeMasterPro Team";
				$email_data['email'] = $user->email;
				$this->send_confirmation_email($email_data);
		    }
		}

		// echo $return;die();
    }

    function extract_videos () {
	    $target_id     = trim( $this->input->get_post('target_id') );
	    $playlist_id   = trim( $this->input->get_post('playlist_id') );
	    $videos        = trim( $this->input->get_post('videos') );
    	$channel_title = trim( $this->input->get_post('channel_title') );

		if ( ( isset( $target_id ) && !empty( $target_id ) ) &&
			 ( isset( $playlist_id ) && !empty( $playlist_id ) ) &&
			 ( isset( $videos ) && !empty( $videos ) ) &&
			 ( isset( $channel_title ) && !empty( $channel_title ) ) ) {

		    $this->load->model('target_model');
		    $target = $this->target_model->get( $target_id );

		    $this->logs->insert_logs("extract_videos", "Extract videos from channel ".$channel_title." and will add to target - ".$target->name);

		    $data = empty($target->data) ? array() : unserialize($target->data);

	    	// update status to pending by setting it to 1
	    	$status_data = array(
	    		'data'   => serialize($data),
	        	'status' => 1
	        );
			$this->target_model->save( $target_id, $status_data );

		    $video_limit = $this->video_limit;
			if ( $this->ion_auth->in_group(3) ) {
				$video_limit = 10;
				$videos_remaining = $video_limit - count($data);
			}
			else {
				$videos_remaining = $video_limit;
			}

			$result = array();
			//$data   = array();

			if ( $videos_remaining > 0 ) {

				$max_results = 50;//( $videos > 10 ) ? 10 : $videos;
				$next_page = '';

				$this->load->library('google');
		        $client = $this->google;

				$client->setDeveloperKey($this->generate_ytkey());

				// Define an object that will be used to make all API requests.
		        $this->load->library('youtube', $client);
		        $youtube = $this->youtube;

		        $videoResults = array();

			    do {
				    $listResponse = $youtube->playlistItems->listPlaylistItems('contentDetails', array(
				        'playlistId' => $playlist_id,//'UUbUsB9V4PVob0EiQqcCZ3Tw',
				        'maxResults' => $max_results,
				        'pageToken'  => $next_page
				    ));
				    $next_page = $listResponse['nextPageToken'];

				    # Merge video ids
					foreach ($listResponse['items'] as $searchResult) {
						array_push($videoResults, $searchResult['contentDetails']['videoId']);
					}
			    } while ( isset($next_page) && $next_page != '' );

			    $videos_added = 0;

			    foreach ( $videoResults as $videoResult ) {
				    $videosResponse = $youtube->videos->listVideos('id, snippet', array(
						'id' => $videoResult,
					));
					// echo 'Fetched == '.$videosResponse['items'][0]['id'].'<br>';

					if ( !in_array( $videosResponse['items'][0]['id'], $data) ) {
						$ytlink = "https://www.youtube.com/watch?v=".$videosResponse['items'][0]['id'];
						$hasAds = $this->check_yt_ads($ytlink);

						if ( $hasAds ) {
							$ytdata = array();
						    $videoTitle   = $videosResponse['items'][0]['snippet']['title'];
						    $videoThumb   = $videosResponse['items'][0]['snippet']['thumbnails']['default']['url'];

						    $ytdata['ytid'] 	= $videosResponse['items'][0]['id'];
						    $ytdata['title'] 	= urlencode( $videoTitle );

						    array_push($data, $ytdata);
						    $videos_added++;
						    if ( $videos_added == $videos_remaining ) {
							    break;
						    }
						}
					}
			    }

			    // if ( $videos_added > 0 ) {
				   //  //- Save to target
				   //  $target_data = array(
			    //     	'data'   => serialize( $data ),
	      //   			'status' => 0
			    //     );

			    //     $this->target_model->save( $target_id, $target_data );
		     //    }
				$result['videos'] = $videos_added;

				if ( $result['videos'] > 0 ) {
					$data['subject'] = "EXTRACT DONE! We found ".$videos_added." videos for ".$channel_title." YouTube channel";
					$data['body']    = "Yup, we did it! We found ".$result['videos']." videos for the \"".$channel_title."\" YouTube channel. Go ahead and login and do your thing!";
				}
				else {
					$data['subject'] = "EXTRACT FAIL! We couldn't find any monetized videos for ".$channel_title." YouTube channel ";
					$data['body']    = "Meh. Sorry about that :/  We searched but failed at life trying to come up with monetised videos for the \"".$channel_title."\" YouTube channel. Go ahead and login and try a different channel.";
				}

		    }
		    else {

				$data['subject'] = "EXTRACT FAIL! We couldn't add anymore videos for ".$channel_title." YouTube channel ";
		    	if ( $this->ion_auth->in_group(3) ) {
					$data['body'] = "Meh. Sorry about that :/  There are MORE videos, but your 7 days evaluation only allows you the first 10.";
		    	}
		    	else {
					$data['body'] = "Meh. Sorry about that :/  You have reached the maximum of 7000 videos.";
		    	}
		    }

		    $target_data = array(
	        	'data'   => serialize( $data ),
    			'status' => 0
	        );

	        $this->target_model->save( $target_id, $target_data );

			$data['email']   = $user->email;
			$this->send_confirmation_email($data);
		}

    }

    function extract_videos_new () {
    	$user = $this->ion_auth->user()->row();
		$result = array();
		$data = array();

    	$target_name   = trim( $this->input->get_post('target_name') );
    	$channel_title = trim( $this->input->get_post('channel_title') );
	    $playlist_id   = trim( $this->input->get_post('playlist_id') );
	    $videos        = trim( $this->input->get_post('videos') );

		if ( ( isset( $target_name ) && !empty( $target_name ) ) &&
			 ( isset( $playlist_id ) && !empty( $playlist_id ) ) &&
			 ( isset( $videos ) && !empty( $videos ) ) &&
			 ( isset( $channel_title ) && !empty( $channel_title ) ) ) {

			$video_limit = $this->video_limit;;
			if ( $this->ion_auth->in_group(3) ) {
				$video_limit = 10;
			}

		    $this->load->model('target_model');

			//$is_name_valid = $this->target_model->get_targets_by_name($target_name, $user->id);

			$this->logs->insert_logs("extract_videos", "Extract videos from channel ".$channel_title." and saved to target - ".$target_name);

		    //if ( !$is_name_valid ) {

			    // update status to pending by setting it to 1
		    	$status_data = array(
		    		'name'		=> $target_name,
		        	'data'		=> '',
		        	'user_id'	=> $user->id,
		        	'status'    => 1
		        );
				$new_target_id = $this->target_model->save( $name, $status_data );

				$max_results = 50;//( $videos > 10 ) ? 10 : $videos;
				$next_page = '';

				$this->load->library('google');
		        $client = $this->google;

				$client->setDeveloperKey($this->generate_ytkey());

				// Define an object that will be used to make all API requests.
		        $this->load->library('youtube', $client);
		        $youtube = $this->youtube;

		        $videoResults = array();

			    do {
				    $listResponse = $youtube->playlistItems->listPlaylistItems('contentDetails', array(
				        'playlistId' => $playlist_id,//'UUbUsB9V4PVob0EiQqcCZ3Tw',
				        'maxResults' => $max_results,
				        'pageToken'  => $next_page
				    ));
				    $next_page = $listResponse['nextPageToken'];

				    # Merge video ids
					foreach ($listResponse['items'] as $searchResult) {
						array_push($videoResults, $searchResult['contentDetails']['videoId']);
					}
			    } while ( isset($next_page) && $next_page != '' );

			    $videos_added = 0;

			    foreach ( $videoResults as $videoResult ) {
				    $videosResponse = $youtube->videos->listVideos('id, snippet', array(
						'id' => $videoResult,
					));
					// echo 'Fetched == '.$videosResponse['items'][0]['id'].'<br>';

					if ( !in_array( $videosResponse['items'][0]['id'], $data) ) {
						$ytlink = "https://www.youtube.com/watch?v=".$videosResponse['items'][0]['id'];
						$hasAds = $this->check_yt_ads($ytlink);

						if ( $hasAds ) {
							$ytdata = array();
						    $videoTitle   = $videosResponse['items'][0]['snippet']['title'];
						    $videoThumb   = $videosResponse['items'][0]['snippet']['thumbnails']['default']['url'];

						    $ytdata['ytid'] 	= $videosResponse['items'][0]['id'];
						    $ytdata['title'] 	= urlencode( $videoTitle );

						    array_push($data, $ytdata);
						    $videos_added++;
						    if ( $videos_added == $video_limit ) {
							    break;
						    }
						}
					}
			    }

			    $result['valid'] = true;
		    // }
		    // else {
			   //  $result['valid'] = false;
		    // }

			$result['videos'] = $videos_added;
		    // echo json_encode( $result );die();
			// echo json_encode($result);die();

			//$data = array();
			$email_data = array();

			if ( $result['videos'] > 0 ) {
				//- Save to target
			    $target_data = array(
		        	'data' 	  => serialize( $data ),
	        		'status'  => 0
		        );

		        $this->target_model->save( $new_target_id, $target_data );

				$email_data['subject'] = "EXTRACT DONE! We found ".$videos_added." videos for ".$channel_title." YouTube channel";
				$email_data['body']    = "Yup, we did it! We found ".$result['videos']." videos for the \"".$channel_title."\" YouTube channel. Go ahead and login and do your thing!";
			}
			else {
				$target_data = array(
		        	'data' 	  => '',
	        		'status'  => 0
		        );

				$this->target_model->save( $new_target_id, $target_data );

				$email_data['subject'] = "EXTRACT FAIL! We couldn't find any monetized videos for ".$channel_title." YouTube channel ";
				$email_data['body']    = "Meh. Sorry about that :/  We searched but failed at life trying to come up with monetised videos for the \"".$channel_title."\" YouTube channel. Go ahead and login and try a different channel.";
			}
			$email_data['email']   = $user->email;
			$this->send_confirmation_email($email_data);
		}

    }

	function send_confirmation_email ( $data ) {
		$this->load->library('email');

		$config['protocol']  = 'smtp';
		$config['smtp_host'] = 'localhost';
		$config['smtp_port'] = '25';
		$config['mailtype']  = 'html';
		$config['charset']   = 'iso-8859-1';
		$config['wordwrap']  = TRUE;

		// $config['protocol']  = 'smtp';
		// $config['smtp_host'] = 'box342.bluehost.com';
		// $config['smtp_port'] = '26';
		// $config['smtp_user'] = 'nathan@nathanhague.com';
		// $config['smtp_pass'] = '$Wolfman1';
		// $config['mailtype']  = 'html';
		// $config['charset']   = 'iso-8859-1';
		// $config['wordwrap']  = TRUE;

		$this->email->initialize($config);

		$this->email->from('support@tubemasterpro.com', 'Support');
		$this->email->to($data['email']);

		$this->email->subject($data['subject']);
		$this->email->message($data['body']);

		$this->email->send();
		echo $this->email->print_debugger();
		// echo json_encode(array( 'error' => 'test'));die();
	}

	function password_check () {
		//echo $this->input->ip_address();die();
        $this->load->config('ion_auth', TRUE);
        $this->load->model('ion_auth_model');
		$user = $this->ion_auth->user()->row();
		$password = $this->input->post('password');

		$old_password_matches = $this->ion_auth_model->hash_password_db($user->id, $password);

        echo $old_password_matches;
        die();
	}

	function save_profile_pic () {
		$user = $this->ion_auth->user()->row();
        $pid  = $this->input->get_post('id');
        $id   = null;
        if ( $user && isset($user->id) && !empty($user->id) ) {
        	$id = $user->id;
        }
        else if ( isset($pid) && !empty($pid) ) {
        	$id = $pid;
        }

        if ( !is_null($id) ) {
	        $file_name = $this->input->get_post('pic');
	        $data = array(
				'profile_pic' => $file_name
			);
			$this->ion_auth->update($id, $data);
        }
	}

	function save_profile () {
		$identity = $this->session->userdata('identity');
		$change   = $this->ion_auth->change_password($identity, $this->input->post('old'), $this->input->post('new'));
		$return   = array();

        if ($change)
        {
            //if the password was successfully changed
            $this->logs->insert_logs("change_password", "Password Successfully Changed.");

            //- check if profile pic is set
            $file_name = $this->input->post('pic');
            if ( isset($file_name) && !empty($file_name) ) {
	        	$user = $this->ion_auth->user()->row();
	            $data = array(
					'profile_pic' => $file_name
				);
				$this->ion_auth->update($user->id, $data);
			}
            $return['valid']    = true;
            $return['message']	= "Profile Saved.";
            $return['filename']	= $file_name;
            $this->session->set_flashdata('message', $this->ion_auth->messages());
            //$this->logout();
        }
        else
        {
            $this->logs->insert_logs("change_password", $this->ion_auth->errors());
            $return['valid']    = false;
            $return['message']	= $this->ion_auth->errors();
            $this->session->set_flashdata('message', $this->ion_auth->errors());
        }
		echo json_encode( $return );die();
	}

	function send_email () {
		$email = $this->input->get('email');
		$this->load->library('email');

		$config['protocol']  = 'smtp';
		$config['smtp_host'] = 'localhost';
		$config['smtp_port'] = '25';
		// $config['smtp_user'] = 'support';
		// $config['smtp_pass'] = 'TripOut2015';
		$config['mailtype']  = 'html';
		$config['charset']   = 'iso-8859-1';
		$config['wordwrap']  = TRUE;

		$this->email->initialize($config);

		$this->email->from('support@tubemasterpro.com', 'TubeMasterPro');
		$this->email->to($email);

		$this->email->subject('Subject');
		$this->email->message('Body');

		$this->email->send();
		echo $this->email->print_debugger();
		// echo json_encode(array( 'error' => 'test'));die();
	}


}

/* End of file skeleton_ajax.php */
/* Location: ./application/modules/skeleton/controllers/skeleton_ajax.php */
