$(document).ready(function(){

	/*=========== START VIDEO/CHANNEL SEARCH EVENTS ===========*/
	
	var cancelled   = false;
	var complete    = false;
	var max_results = 10;
	var ajax_done   = false;
	var ajax_results= 0;
	var next_page   = '';
	var has_ads     = 0;
	var has_videos  = 0;
	var current_keyword = '';
	//var settings_url = "http://localhost/myvideoads/";
	var settings_url = "http://www.tubemasterpro.com/";
	
	if ( $('#keyword_input').length > 0 ) {
		input_checker( $('#keyword_input'), $('#keyword_search'), 'btn-primary');
		if ( $('#keyword_input').val() != '' ) {
			$('#keyword_form').submit();
		}
	}

	if ( $('#v_breadcrumb').length > 0 ) {
		$('#v_breadcrumb').on('click',function(e){
			e.preventDefault();
			var keyword = $(this).data('keyword');

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
				url: settings_url+'dashboard/dashboard_ajax/save_video_keyword_search',
				type: "POST",
				crossDomain: true,
				data: {
					keyword: keyword,
					main   : $(this).data('main'),
					page   : $(this).data('page'),
					entries: $(this).data('entries'),
					ttype  : $(this).data('type')
				}
			});

			request.done(function(msg){
				window.location.href = settings_url+'dashboard/keyword_search';
			});
		});
	}

	if ( $('#c_breadcrumb').length > 0 ) {
		$('#c_breadcrumb').on('click',function(e){
			e.preventDefault();
			var keyword = $(this).data('keyword');

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
				url: settings_url+'dashboard/dashboard_ajax/save_channel_keyword_search',
				type: "POST",
				crossDomain: true,
				data: {
					keyword: $(this).data('keyword'),
					main   : $(this).data('main'),
					page   : $(this).data('page'),
					entries: $(this).data('entries'),
					ttype  : $(this).data('type')
				}
			});

			request.done(function(msg){
				window.location.href = settings_url+'dashboard/keyword_search';
			});
		});
	}
	
	if ( $('#temp_videos_new_input').length > 0 ) {
		$('#temp_videos_new_input').on('keyup', function(){
			if ( $.trim( $(this).val() ) != '' ) {
				$(this).parent().removeClass('has-error');
				$(this).parent().addClass('has-success');
				$(this).popover('hide');
			}
			else {
				$(this).parent().removeClass('has-success');
				$(this).parent().addClass('has-error');
			}
		});
		input_checker( $('#temp_videos_new_input'), $('#temp_videos_new'), 'btn-success');
	}
	
	$('#temp_search_modal').on('hidden.bs.modal', function (e) {
		$('#temp_videos_new_input').val('');
		$('#temp_videos_new_input').popover('hide');
		if ( $('#temp_videos_new_input').parent().hasClass('has-success') ) {
			$('#temp_videos_new_input').parent().removeClass('has-success');
			$('#temp_videos_new_input').parent().addClass('has-error');
		}
		
		if ( $('#temp_videos_new').hasClass('btn-success') ) {
			$('#temp_videos_new').removeClass('btn-success');
			$('#temp_videos_new').addClass('btn-disabled');
			$('#temp_videos_new').prop('disabled', true);
		}
	})
	
	if ( $('#extract_videos_new_input').length > 0 ) {
		input_checker( $('#extract_videos_new_input'), $('#extract_videos_new'), 'btn-success');
	}
	
	if ( $('#channel_targets').length > 0 ) {
		input_checker( $('#channel_targets'), $('#extract_videos'), 'btn-success');
		/*
if ( $('#channel_targets option').length > 0 ) {
			$('#extract_videos_new_input').prop('disabled', true);
		}
*/
		// var request = $.ajax({
		// 	url: settings_url+"dashboard/dashboard_ajax/get_target_count",
		// 	type: "POST",
		// 	crossDomain: true,
		// 	dataType: "json"
		// });
		
		// request.done(function( msg ) {
		// 	if ( msg.free && msg.count > 0 ) {
		// 		$('#extract_videos_new_input').prop('disabled', true);
		// 	}
		// });
	}

  	if ( $('#max-results').length != 0 ) {
  		max_results = $('#max-results').val();
  		$(document).on('change','#max-results',function(){
			max_results = $(this).val();
		});
  	}
	
	//- Event if not subscribed
	$(document).on('click','.upgrade_now',function(){
			
		$('#upgrade_modal').modal('show');
			
	});

	//- Event cancel search
	$(document).on('click','#cancel-search',function(){
		cancelled = true;
		$(this).fadeOut(100, function(){
			$('#cancel-notice').text('Cancelling Search... Please wait a moment...');
			$('#cancel-notice').fadeIn();
		});
	});
	
	if ( $('#continue_search').length > 0 ) {
		$('#continue_search').on('click', function(){
			next_page = $(this).data('next');
			$('#recent_search_modal').modal('hide');
			$('#temp_search_modal').data('next', $(this).data('next'));
			if ( max_results == 500 ) {
				$('#temp_search_modal').modal('show');
			}
			else {
				pre_search();
			}
		});
	}
	
	if ( $('#reset_search').length > 0 ) {
		$('#reset_search').on('click', function(){
			next_page = '';
			$('#recent_search_modal').modal('hide');
			$('#temp_search_modal').data('next', $(this).data('next'));
			if ( max_results == 500 ) {
				$('#temp_search_modal').modal('show');
			}
			else {
				pre_search();
			}
		});
	}
	
	//- Event save new temp target
	if ( $('#temp_videos_new').length > 0 ) {
		$(document).on('click','#temp_videos_new',function(){
				
			var target_name = $.trim( $('#temp_videos_new_input').val() );
			var keyword     = $.trim( $('#search_input').val() );
			var name_request = $.ajax({
				url: settings_url+'dashboard/dashboard_ajax/check_target_name',
				type: "POST",
				crossDomain: true,
				data: { 
					target_name : target_name
				}
			});
			
			name_request.done(function(msg){
				console.log(msg);
				if ( msg ) {
					$.ajax({
						url: $("#temp_videos_new").data('action'),
						type: "POST",
						crossDomain: true,
						data: { 
							target_name : target_name,
							keyword     : keyword,
							next		: $('#temp_search_modal').data('next')
						}
					});
					$('#temp_search_modal').modal('hide');
					$('#video_confirmation_modal').modal('show');
				}
				else {
					$('#temp_videos_new_input').parent().removeClass('has-success');
					$('#temp_videos_new_input').parent().addClass('has-error');
					$('#temp_videos_new_input').popover('show');
				}
			});
			
			/*
request.done( function(msg) {
				$('#temp_search_modal').modal('hide');
				$('#video_confirmation_modal').modal('show');
			});
*/
		});
	}
	
	//- Event update temp target
	if ( $('#temp_videos').length > 0 ) {
		$(document).on('click','#temp_videos',function(){
			var target_id = $.trim( $('#temp_targets').val() );
			var keyword   = $.trim( $('#search_input').val() );
			var request   = $.ajax({
				url: $("#temp_videos").data('action'),
				type: "POST",
				crossDomain: true,
				data: { 
					target_id   : target_id,
					keyword     : keyword,
					next		: $('#temp_search_modal').data('next')
				},
				dataType: "json"
			});

			var target_selected = $('#temp_targets option:selected').text();
			$('#targets option').each(function(){
				if ( $(this).text() == target_selected ) {
					$(this).remove();
				}
			});
			
			$('#temp_search_modal').modal('hide');
			$('#video_confirmation_modal').modal('show');
			
			/*
request.done( function(msg) {
				console.log(msg);
			});
*/
		});
	}

	//- Event save new target
	$(document).on('click','#save-target-button',function(){
		var new_name = $.trim( $('#save-target-input').val() );
		if ( new_name != '' && $('#search_result .ytcheckbox:checked').length > 0 ) {
		
			show_loader('Saving Target...', true, 100);
			
			var ytdata = [];
			$('#search_result .ytcheckbox:checked').each(function(i){
				var vid_data = $(this).parent().parent().data('ytdata');
				ytdata.push( vid_data );
			});
			
			console.log(ytdata.length);
			console.log(ytdata);

			var request = $.ajax({
				url: $(this).data('action'),
				type: "POST",
				crossDomain: true,
				data: { 
					"target_name" : new_name,
					"ytdata"      : ytdata
				},
				dataType: 'html'
			});

			request.done(function( msg ) {
				// console.log(msg);
				// console.log( new_name+" successfully added.");
				if ( !msg ) {
					$('.main_loader .loader').fadeOut(100,function(){
						$('.main_loader').hide();
						show_modal('Target Name Exist.');
					});
				}
				else {
					if ( msg == 'freemium' ) {
						show_loader('Request failed!', false, 'fast');
						$('#freemium_modal .modal-body').html("You can add MORE Targets, but your 24 hour evaluation only allows you to create 1.");
						$('#freemium_modal').modal('show');
					}
					else {
						$('#targets').append(msg);
						$('#temp_targets').append(msg);
						$('#show-target').append(msg);
						
						show_loader('Target Saved!', false, 300);
					}
				}
			});

			request.fail(function( jqXHR, textStatus ) {
				// alert( "Request failed: " + textStatus );
				// console.log( "Can\'t add new target. Please try again later." );
				show_loader('Target Save Failed!', false, 300);
			});
			// console.log( $('#save-target-input').val() );
		}
		else if ( new_name == '' ) {
			show_modal('Target Name is Empty.');
		}
		else if ( $('#search_result .ytcheckbox:checked').length <= 0 ) {
			$('#copy-description').hide();
			show_modal('Please select videos to add.');
		}
	});

	//- Event update target
	$(document).on('click','#add-target-button',function(){
		if ( $('#targets').val() != 0 ) {
			if ( $('#search_result .ytcheckbox:checked').length > 0 ) {
			
				show_loader('Updating Target...', true, 100);
				
				var ytdata = [];
				$('#search_result .ytcheckbox:checked').each(function(i){
					ytdata.push( $(this).parent().parent().data('ytdata') );
				});
	
				var request = $.ajax({
					url: $(this).data('action'),
					type: "POST",
					crossDomain: true,
					data: { 
						"target_id"   : $('#targets').val(),
						"ytdata"      : ytdata
					},
					dataType: 'html'
				});
	
				request.done(function( msg ) {
					// console.log( new_name+" successfully added.");
					if ( !msg ) {
						$('.main_loader .loader').fadeOut(100,function(){
							$('.main_loader').hide();
							show_modal('Target Name Exist.');
						});
					}
					else {
						var un_parse = msg;
						var result = $.parseJSON(msg);
						if ( result.freemium == 'true' ) {
							show_loader('Request failed!', false, 'fast');
							$('#freemium_modal .modal-body').html('There ARE more videos, but your 24 hour evaluation only allows you the first 10');
							$('#freemium_modal').modal('show');
						}
						else {
							// $('#targets').append(msg);
							$('#targets').find('option:selected').data('ytdata', $.parseJSON(un_parse));
							$('#targets').find('option:selected').attr('data-ytdata', $.parseJSON(un_parse));
							$('#show-target').find('option:selected').data('ytdata', $.parseJSON(un_parse));
							$('#show-target').find('option:selected').attr('data-ytdata', $.parseJSON(un_parse));
							$('#temp_targets').find('option:selected').data('ytdata', $.parseJSON(un_parse));
							$('#temp_targets').find('option:selected').attr('data-ytdata', $.parseJSON(un_parse));
							
							check_results_with_target( $.parseJSON(un_parse) );
							
							show_loader('Target Updated!', false, 300);
						}
					}
				});
	
				request.fail(function( jqXHR, textStatus ) {
					alert( "Request failed: " + textStatus );
					// console.log( "Can\'t add new target. Please try again later." );
					show_loader('Update Failed!', false, 300);
				});
			}
			else {
				show_modal('Please select videos to add.');
			}
		}
		else {
			show_modal('Please select an Existing Target.');
		}
	});
	
	$(document).mouseup(function (e) {
        var save_btn     = $(".rename_list");
        var rename_btn   = $(".rename_list1");
        var rename_input = $(".list_name_input");
        // console.log(e.target);
        // console.log(test);
        // console.log(test.is(e.target));
        if (!save_btn.is(e.target) // if the target of the click isn't the container...
            && save_btn.has(e.target).length === 0
            && !rename_btn.is(e.target)
            && rename_btn.has(e.target).length === 0
            && !rename_input.is(e.target)
            && rename_input.has(e.target).length === 0 ) // ... nor a descendant of the container
        {
        	// console.log('test');
        	//revert opened elements
			$('.rename_list').slideUp(100, function(){
				//this_el.slideDown('fast');
				setTimeout(function(){
	  				$('.rename_list1').slideDown(100);
	  				$('.list_name_input').fadeOut();
				}, 200);
			});
        }
    });

	//- Event get links
	if ( $("#get-ytlinks-button").length > 0 ) {
		var client = new ZeroClipboard( document.getElementById("get-ytlinks-button") );
		var links = [];
		client.on( "ready", function( readyEvent ) {
		  // alert( "ZeroClipboard SWF is ready!" );
		
		  client.on( "copy", function( event ) {
			    if ( $('#search_result .ytcheckbox:checked').length > 0 ) {
					$('p#notice-content').text('');
					$('p#notice-content').removeClass('text-center');
					$('p#notice-content').addClass('text-left');
					links = [];
					$('#search_result .ytcheckbox:checked').each(function(i){
						var data = $(this).parent().parent().data('ytdata');
						var link_url = "https://www.youtube.com/watch?v="+data.ytid;
						links.push( link_url+'&#13;&#10;' );
						// console.log( data.link_url );
					});
					
					$('#links').html( links );
					var clipboard = event.clipboardData;
					clipboard.setData( "text/plain", $('#links').html() );
					
				  }
				  else {
					  links = [];
				  }
		  });
		  client.on( "aftercopy", function( event ) {
		    // `this` === `client`
		    // `event.target` === the element that was clicked
		    // event.target.style.display = "none";
		    if (links.length > 0) {
			    var msg = ( links.length > 1 ) ? links.length+' Links' : links.length+' Link';
		    	alert("Copied " + msg + " to Clipboard." );
		    }
		  } );
		} );
	}

	//- Event submit search
	$(document).on('submit','#search_form',function(e){
		e.preventDefault();
		if ( $('#search_input').length != 0 && $('#search_input').val() != "" ) {
			if ( $.fn.dataTable.isDataTable( '#search_result table' ) ) {
				var ytDatatable = $('#search_result table').DataTable();
				ytDatatable.destroy();
			}
			
			search_init();
		}
	  	else if ( $('#search_input').val() == "" ) {
			show_modal('Search field is Empty.');
	  	}
  	});
  	
  	if ( $('#check_all').length > 0 ) {
  		$('#check_all').on('change',function(){
  			if ( $(this).prop('checked') == true ) {
	  			$('#search_result .ytcheckbox').each(function(i){
	  				$(this).prop('checked', true);
	  			});
	  			$('#get-ytlinks-button').removeClass('btn-disabled');
			  	$('#get-ytlinks-button').addClass('btn-primary');
			  	$('#get-ytlinks-button').prop('disabled', false);
  			}
  			else {
	  			$('#search_result .ytcheckbox').each(function(i){
	  				$(this).prop('checked', false);
	  			});
	  			$('#get-ytlinks-button').removeClass('btn-primary');
			  	$('#get-ytlinks-button').addClass('btn-disabled');
			  	$('#get-ytlinks-button').prop('disabled', true);
  			}
  		});
  	}
  	
  	if ( $('#check_all_targets').length > 0 ) {
	  	$('#check_all_targets').on('change',function(){
	  		console.log('test');
  			if ( $(this).prop('checked') == true ) {
	  			$('#target_lists .list_to_delete').each(function(i){
	  				$(this).prop('checked', true);
	  			});
	  			$('#delete_target_list_button').fadeIn();
  			}
  			else {
	  			$('#target_lists .list_to_delete').each(function(i){
	  				$(this).prop('checked', false);
	  			});
	  			$('#delete_target_list_button').fadeOut();
  			}
  		});
  	}
  	
  	if ( $('.list_to_delete').length > 0 ) {
	  	$('.list_to_delete').each(function(){
		  	$(this).on('change', function(){
			  	if ( $(this).prop('checked') == true ) {
		  			if ( $('#delete_target_list_button').css('display') == 'none' ) {
		  				$('#delete_target_list_button').fadeIn();
		  			}
		  			
		  			if ( $('.list_to_delete:checked').length == $('.list_to_delete').length ) {
			  			$('#check_all_targets').prop('checked', true);
		  			}
	  			}
	  			else {
		  			if ( $('.list_to_delete:checked').length <= 0 ) {
		  				$('#delete_target_list_button').fadeOut();
		  			}
		  			
		  			$('#check_all_targets').prop('checked', false);
	  			}
		  	});
	  	});
  	}

  	if ( $('#targets').length > 0 ) {
  		$('#targets').on('change',function(){
  			if ( $(this).val() != 0 ) {
  				check_results_with_target( $(this).find('option:selected').data('ytdata') );
  				$('#add-target-button').removeClass('btn-disabled');
  				$('#add-target-button').addClass('btn-success');
  				$('#add-target-button').prop('disabled', false);
  			}
  			else {
  				$('#add-target-button').removeClass('btn-success');
  				$('#add-target-button').addClass('btn-disabled');
  				$('#add-target-button').prop('disabled', true);
  				
	  			if ( $('#search_result .ytcheckbox').length > 0 ) {
		  			$('#search_result .ytcheckbox').each(function(i){
		  				$(this).prop('checked', false);
		  				$('#check_all').prop('checked', false);
		  			});
	  			}
  			}
  		});
  	}

	if ( $('#save-target-input').length != 0 ) {
		input_checker ( $('#save-target-input'), $('#save-target-button'), 'btn-success' )
  		$(document).on('keyup', '#save-target-input', function(e){
  			var this_val = $(this).val();
  			this_val = this_val.toLowerCase();
  			$(this).val(this_val);
  		});

  		// $('#targets option').each(function(){
  		// 	console.log( $(this).data('ytdata') );
  		// });
  	}

  	if ( $('#search_input').length != 0 && $('#search_input').val() != "" ) {
  		search_init();
  	}
  	
  	if ( $('#search_input').length != 0 ) {
	  	input_checker ( $('#search_input'), $('#search_button'), 'btn-primary' )
  	}
  	
  	if ( $('#extract_videos').length > 0 ) {
  		$('#channel_targets').on('change', function(e){
	  		$('#extract_videos').data('target-id', $(this).val());
	  		$('#extract_videos').prop('data-target-id', $(this).val());
  		});
  		
		$('#extract_videos').on('click', function (e) {
			extract_videos( $('#extract_videos').data('target-id'), $('#extract_videos').data('playlist-id'), $('#extract_videos').data('videos'), $('#extract_videos').data('channel-title') );
		});
	}
	
	if ( $('#extract_videos_new').length > 0 ) {
		$('#extract_videos_new').on('click', function(){
			extract_videos_new( $('#extract_videos_new_input').val(), $('#extract_videos_new').data('playlist-id'), $('#extract_videos_new').data('videos'), $('#extract_videos_new').data('channel-title') );
		});
	}
	
	$('#channel_modal').on('hidden.bs.modal', function (e) {
		$('#extract_videos_new_input').val('');
		$('#extract_videos_new').prop('disabled', true);
		$('#extract_videos_new').removeClass('btn-success');
		$('#extract_videos_new').addClass('btn-disabled');
	});
	
	$('#videoModal').on('hidden.bs.modal', function(e){
		$('#videoModal iframe').attr("src", $('#videoModal iframe').attr("src"));
	});
	
	if ( $('#video-confirmation-ok').length > 0 ) {
		$('#video-confirmation-ok').on('click', function(e){
			e.preventDefault();
			var keyword = $('#v_breadcrumb').data('keyword');

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
				url: settings_url+'dashboard/dashboard_ajax/save_video_keyword_search',
				type: "POST",
				crossDomain: true,
				data: {
					keyword: keyword,
					main   : $('#v_breadcrumb').data('main'),
					page   : $('#v_breadcrumb').data('page'),
					entries: $('#v_breadcrumb').data('entries'),
					ttype  : $('#v_breadcrumb').data('type')
				}
			});

			request.done(function(msg){
				window.location.href = settings_url+'dashboard/keyword_search';
			});
		});
	}
	
	if ( $('.toggle_columns').length > 0 ) {
		$('.toggle_columns').on('click', function(){
			if ( $(this).hasClass('toggled') ) {
				$(this).removeClass('toggled');
				$(this).parent().parent().find('th:nth-child(4)').fadeIn();
				$(this).parent().parent().find('th:nth-child(5)').fadeIn();
				$(this).parent().parent().find('th:nth-child(6)').fadeIn();
				$(this).parent().parent().find('th:nth-child(7)').fadeIn();
				
				$(this).parent().parent().parent().next().find('td:nth-child(4)').fadeIn();
				$(this).parent().parent().parent().next().find('td:nth-child(5)').fadeIn();
				$(this).parent().parent().parent().next().find('td:nth-child(6)').fadeIn();
				$(this).parent().parent().parent().next().find('td:nth-child(7)').fadeIn();
			}
			else {
				$(this).addClass('toggled');
				$(this).parent().parent().find('th:nth-child(4)').fadeOut();
				$(this).parent().parent().find('th:nth-child(5)').fadeOut();
				$(this).parent().parent().find('th:nth-child(6)').fadeOut();
				$(this).parent().parent().find('th:nth-child(7)').fadeOut();
				
				$(this).parent().parent().parent().next().find('td:nth-child(4)').fadeOut();
				$(this).parent().parent().parent().next().find('td:nth-child(5)').fadeOut();
				$(this).parent().parent().parent().next().find('td:nth-child(6)').fadeOut();
				$(this).parent().parent().parent().next().find('td:nth-child(7)').fadeOut();
			}
		});
	}
  	
  	/*=========== END VIDEO/CHANNEL SEARCH EVENTS ===========*/
  	
  	/*=========== START TARGET LIST EVENTS ===========*/
  	if ( $('#num_targets').length > 0 ) {
  		var num_targ = $('#num_targets').val();
  		var num_vids = 0;
  		var msg = '';
  		
  		if ( $('.row-target').length > 0 ) {
	  		$('.row-target').each(function(){
		  		num_vids += $(this).data('num-vids');
	  		});
  		}
  		
  		if ( num_targ > 0 ) {
  			vids = ( num_vids > 1 ) ? ( ( num_vids > 1 ) ? numeral(num_vids).format('0,0') + ' videos'  : num_vids + ' video') : '';
	  		msg  = ( num_targ > 1 ) ? 'My Target Lists (' + num_targ + ')' : 'My Target List (' + num_targ + ')';
  		}
  		else {
	  		msg = "My Target List";
  		}
  		
	  	$('#target_header').text(msg);
	  	$('#target_header').append(' <span style="font-size:14px;">' + vids + '</span>');
  	}
  	
  	if ( $('.ta_title').length > 0 ) {
	  	$('.ta_title').each(function(i){
		  	$(this).on('click',function(){
		  		var target_id = $(this).parent().data('list-id');
		  		if ( $(this).hasClass('toggled') ) {
		  			$(this).parent().css('background-color','#f7f7f7');
			  		$(this).removeClass('toggled');
			  		$(this).parent().next().slideUp(300,function(){
			  			if ( $.fn.dataTable.isDataTable( $(this).find('table') ) ) {
							var ytDatatable = $(this).find('table').DataTable();
				  			ytDatatable.destroy();
						}
						$(this).find('table tbody').html('');
			  		});
		  		}
		  		else {
		  			if ( $(this).parent().data('num-vids') > 0 ) {
		  				$(this).parent().css('background-color','#e0e0e0');
				  		$(this).addClass('toggled');
				  		$('.ta_title.toggled').not(this).parent().css('background-color','#f7f7f7');
				  		$('.ta_title.toggled').not(this).parent().next().slideUp(300,function(){
					  		if ( $.fn.dataTable.isDataTable( $(this).find('table') ) ) {
								var ytDatatable = $(this).find('table').DataTable();
					  			ytDatatable.destroy();
							}
					  		$(this).find('table tbody').html('');
				  		});
				  		$('.ta_title.toggled').not(this).removeClass('toggled');
				  		$(this).parent().next().slideDown(300, function() {
					  		get_target_videos( target_id, $(this) );
				  		});
			  		}
		  		}
		  	});
	  	});
  	}
  	
  	if ( $('#target_list_name').length > 0 ) {
  		input_checker ( $('#target_list_name'), $('#add_target_button'), 'btn-success' );
  		$(document).on('keyup', '#target_list_name', function(e){
  			var this_val = $(this).val();
  			this_val = this_val.toLowerCase();
  			$(this).val(this_val);
  		});
  	}
  	
  	if ( $('.list_name_input').length > 0 ) {
  		$('.list_name_input').each(function(i){
  			$(this).on('keyup', function(e){
	  			var this_val = $(this).val();
	  			this_val = this_val.toLowerCase();
	  			$(this).val(this_val);
	  		});
  		});
  	}
  	
  	if ( $('#delete_target_list_button').length > 0 ) {
	  	$('#delete_target_list_button').on('click', function(){
	  		var target_action = $(this).data('action');
	  		
	  		if ( $('.list_to_delete:checked').length > 0 ) {
	  			var target_ids = [];
		  		var msg  = "<p>Are you sure you want to delete the following target lists?</p>";
		  		msg += "<div id='delete_target_lists_list'></div>";
		  		
  				$('#target_list_modal .modal-body').html('');
  				$('#target_list_modal .modal-body').html(msg);
  				
		  		$('.list_to_delete:checked').each(function(i){
		  			target_ids.push( $(this).parent().parent().data('list-id') );
			  		var list_name = $(this).parent().next('.ta_title').text();
			  		$('#delete_target_lists_list').append("<strong>"+list_name+"</strong><br>");
		  		});
		  		
  				$('#target_list_modal').modal('show');
  				$('#modal-no').text('No');
  				$('#modal-yes').show();
  				
  				$('#modal-yes').on('click',function(){
  					
					show_loader('Processing...', true, 100);
					
			  		var request = $.ajax({
						url: target_action,
						type: "POST",
						crossDomain: true,
						data: {
							target_ids   : target_ids
						}
					});
			
					request.done(function( msg ) {
						if ( msg ) {
							location.reload(true);
						}
					});
			
					request.fail(function( jqXHR, textStatus ) {
						// alert( "Request failed: " + textStatus );
						//ajax_done = false;
						show_loader('Request failed!', false, 'slow');
					});
  				});
	  		}
	  		else {
		  		var msg = "Please select from the Target Lists";
  				$('#target_list_modal .modal-body').html('');
  				$('#target_list_modal .modal-body').html(msg);
  				$('#target_list_modal').modal('show');
  				$('#modal-no').text('Ok');
  				$('#modal-yes').hide();
	  		}
	  	});
	}
	
  	if ( $('#add_target_button').length > 0 ) {
  		
  		/*
$('#target_list_name').popover({
  			content: 'Please fill out this field',
  			placement: 'bottom',
  			trigger: 'click'
  		});
*/
  		
	  	$('#add_target_button').on('click', function(){
	  		if ( $('#target_list_name').val() != '' ) {
				show_loader('Processing...', true, 100);
				
			  	var request = $.ajax({
					url: $(this).data('action'),
					type: "POST",
					crossDomain: true,
					data: {
						target_name : $.trim( $('#target_list_name').val() )
					},
					//dataType: "html"
				});
		
				request.done(function( msg ) {
					//console.log(msg);
					if ( msg ) {
						location.reload(true);
					}
					else if ( msg == 'demo' ) {
						show_loader('Demo!', false, 400);
						$('#targetDemoModal').modal('show');
					}
					else {
						show_loader('Target Name Exist!', false, 3000);
					// 	$('#freemium_modal .modal-body').html("You can add MORE Targets, but your 24 hour evaluation only allows you create only 1.");
					// 	$('#freemium_modal').modal('show');
					}
				});
		
				request.fail(function( jqXHR, textStatus ) {
					// alert( "Request failed: " + textStatus );
					//ajax_done = false;
					show_loader('Request failed!', false, 'slow');
				});
			}
			else {
		  		$('#target_list_name').popover('show');
			}
	  	});
  	}
  	
  	if ( $('.rename_list1').length > 0 ) {
  		$('.rename_list1').each(function(i){
  			$(this).on('click',function(){
  				var this_el = $(this);
  				this_el.slideUp('fast', function(){
	  				$(this).parent().find('.rename_list').slideDown('fast');
	  				//$(this).fadeOut('fast');
	  				$( this_el.parent().prev().find('.list_name_input') ).fadeIn();
  				});
	  			
	  			//revert opened elements
	  			$('.rename_list').not($(this).parent().find('.rename_list')).slideUp(100, function(){
	  				//this_el.slideDown('fast');
	  				setTimeout(function(){
		  				$('.rename_list1').not(this_el).slideDown(100);
		  				$('.list_name_input').not($( this_el.parent().prev().find('.list_name_input') )).fadeOut();
	  				}, 200);
  				});
  			});
  		});
  	}
  	
  	if ( $('.rename_list').length > 0 ) {
  		$('.rename_list').each(function(i){
  			$(this).on('click',function(){
	  			var target_id   = $(this).parent().parent().parent().data('list-id');
	  			var target_name = $.trim( $(this).parent().parent().find('.list_name_input').val() );
  				if ( target_name != '' ) {
					show_loader('Processing...', true, 100);
				
		  			// console.log(target_name);
			  		var request = $.ajax({
						url: $(this).data('action'),
						type: "POST",
						crossDomain: true,
						data: {
							target_name : target_name,
							target_id   : target_id
						}
					});
			
					request.done(function( msg ) {
						if ( msg ) {
							location.reload(true);
						}
					});
			
					request.fail(function( jqXHR, textStatus ) {
						// alert( "Request failed: " + textStatus );
						//ajax_done = false;
						show_loader('Request failed!', false, 'slow');
					});
				}
  			});
  		});
  	}
  	
  	if ( $('.add_video').length > 0 ) {
  		$('.add_video').each(function(i){
  			$(this).on('click',function(){
  				var target_id = $(this).parent().parent().parent().data('list-id');
  				$('#add_video_btn').data('target-id', target_id);
  				$('#add_video_btn').prop('data-target-id', target_id);
  				$('#add_video_modal').modal('show');
  			});
  		});
  	}
  	
  	if ( $('#add_video_btn').length > 0 ) {
	  	$('#add_video_btn').on('click', function(){
		  	var target_id = $(this).data('target-id');
		  	var vid_url   = $('#add_video_input').val();
		  	var vid_id    = vid_url.split('v=')[1];
		  	
		  	show_loader('Adding Video!', true, 100);
		  	
		  	var request = $.ajax({
				url: $(this).data('action'),
				type: "POST",
				crossDomain: true,
				data: {
					target_id   : target_id,
					vid_id		: vid_id
				},
				dataType: 'json'
			});
	
			request.done(function( msg ) {
				if ( msg['msg'] == 'ok' ) {
					location.reload(true);
				}
				else if ( msg['msg'] == 'free' ) {
					$('#add_video_modal').modal('hide');
					show_loader('Request failed!', false, 'fast');
					$('#freemium_modal .modal-body').html("You can add MORE videos, but your 24 hour evaluation only allows you the first 10");
					$('#freemium_modal').modal('show');
				}
				else if ( msg['msg'] == 'duplicate') {
					show_loader('Duplicate Video', false, 2000);
				}
				else if ( msg['msg'] == 'no ads' ) {
					show_loader('Not Monetized', false, 2000);
				}
			});
			
			request.fail(function( jqXHR, textStatus ) {
				show_loader('Fetching Failed!', false, 2000);
			});
	  	});
  	}
  	
  	if ( $('#add_video_input').length > 0 ) {
	  	$('#add_video_input').on('keyup', function(){
		  	var ytlink   = '/^(?:https?:\/\/)?(?:www\.)?youtube\.com\/watch\?(?=.*v=((\w|-){11}))(?:\S+)?$/g';
		  	var this_val = $(this).val();
		  	var is_valid = this_val.match(/^(?:https?:\/\/)?(?:www\.)?youtube\.com\/watch\?(?=.*v=((\w|-){11}))(?:\S+)?$/g);
		  	if ( is_valid ) {
			  	$('#add_video_btn').removeClass('btn-disabled');
			  	$('#add_video_btn').addClass('btn-success');
			  	$('#add_video_btn').prop('disabled', false);
		  	}
		  	else {
			  	$('#add_video_btn').removeClass('btn-success');
			  	$('#add_video_btn').addClass('btn-disabled');
			  	$('#add_video_btn').prop('disabled', true);
		  	}
	  	});
  	}
  	
  	if ( $('.delete_list').length > 0 ) {
  		$('.delete_list').each(function(i){
  			$(this).on('click',function(){
	  			var target_id   = $(this).parent().parent().parent().data('list-id');
	  			var target_action = $(this).data('action');
	  			
  				var msg = "Are you sure you that want to delete this Target List and its Targets??";
  				$('#target_list_modal .modal-body').html('');
  				$('#target_list_modal .modal-body').html(msg);
  				$('#target_list_modal').modal('show');
  				$('#modal-no').text('No');
  				$('#modal-yes').show();
  				
  				$('#modal-yes').on('click',function(){
					show_loader('Processing...', true, 100);
					
	  				// console.log(target_id);
			  		var request = $.ajax({
						url: target_action,
						type: "POST",
						crossDomain: true,
						data: {
							target_id   : target_id
						}
					});
			
					request.done(function( msg ) {
						if ( msg ) {
							location.reload(true);
						}
					});
			
					request.fail(function( jqXHR, textStatus ) {
						// alert( "Request failed: " + textStatus );
						//ajax_done = false;
						$('.main_loader .loader').html('Something went wrong. Please try again later.');
						$('.main_loader .loader').fadeOut('slow',function(){
							$('.main_loader').hide();
						});
						
						show_loader('Request failed!', false, 'slow');
					});
  				});
  			});
  		});
  	}
  	
  	if ( $('.bulk_deleter').length > 0 ) {
  		$('.bulk_deleter').each(function(i){
  			$(this).on('click',function(){
	  			var target_action = $(this).data('action');
	  			var target_id     = $(this).parent().parent().parent().parent().parent().parent().parent().parent().parent().prev().data('list-id');
	  			
	  			if ( $('.ytcheckbox:checked').length > 0 ) {
	  				var ytids     = [];
			  		var msg       = "<p>Are you sure you want to delete the following videos?</p>";
			  		msg += "<div id='delete_target_lists_list'></div>";
			  		
			  		$('#target_list_modal .modal-body').html('');
	  				$('#target_list_modal .modal-body').html(msg);
	  				
			  		$('.ytcheckbox:checked').each(function(i){
			  			ytids.push( $(this).parent().parent().data('video-id') );
			  		});
			  		
			  		console.log(ytids);
			  		
	  				$('#target_list_modal').modal('show');
	  				$('#modal-no').text('No');
	  				$('#modal-yes').show();
	  				
	  				$('#modal-yes').on('click',function(){
						show_loader('Processing...', true, 100);
						
	  					var request = $.ajax({
							url: target_action,
							type: "POST",
							crossDomain: true,
							data: {
								target_id   : target_id,
								ytids		: ytids
							}
						});
				
						request.done(function( msg ) {
							//console.log(msg);
							if ( msg ) {
								location.reload(true);
							}
						});
				
						request.fail(function( jqXHR, textStatus ) {
							// alert( "Request failed: " + textStatus );
							//ajax_done = false;
							show_loader('Request failed!', false, 'slow');
						});
	  				});
			  		
	  			}
	  			else {
	  				var msg = "Please Select a Target";
	  				$('#target_list_modal .modal-body').html('');
	  				$('#target_list_modal .modal-body').html(msg);
	  				$('#target_list_modal').modal('show');
	  				$('#modal-no').text('Ok');
	  				$('#modal-yes').hide();
	  			}
  			});
  		});
  	}
  	
  	if ( $('.bulk_mover').length > 0 ) {
		$('.bulk_mover').on('click', function(){
			var videos_checked = $(this).parent().parent().parent().parent().find('.ytcheckbox:checked');
			var ytids = [];
			var prev_target_id = '';
			
			$.each(videos_checked, function(){
				ytids.push( $(this).parent().parent().data('video-id') );
				prev_target_id = $(this).parent().parent().data('list-id');
			});
			
			var request = $.ajax({
				url: settings_url+'dashboard/dashboard_ajax/get_targets',
				type: "POST",
				crossDomain: true,
				data: { target_id : prev_target_id }
			});
			
			request.done(function( msg ) {
				$('#mover_targets').html(msg);
				$('#mover_modal').data('ytids', ytids);
				$('#mover_modal').data('list_id', prev_target_id);
				$('#mover_modal').modal('show');
			});
		});
  	}
  	
  	if ( $('#mover_videos').length > 0 ) {
	  	$('#mover_videos').on('click', function(){
	  		var ytids = $('#mover_modal').data('ytids');
	  		var old_target = $('#mover_modal').data('list_id');
	  		var new_target = $('#mover_targets').val();
	  		var target_action = $(this).data('action');
	  		show_loader('Moving...', true, 100);
	  		var request = $.ajax({
				url: target_action,
				type: "POST",
				crossDomain: true,
				data: {
					old_target  : old_target,
					new_target  : new_target,
					ytids		: ytids
				}
			});
	
			request.done(function( msg ) {
				console.log(msg);
				location.reload(true);
			});
	  	});
  	}
  	
  	if ( $('.get_links').length > 0 ) {
  		$('.get_links').each(function(i){
  			var this_el = $(this);
  			var links = this_el.next('textarea.links');
  			var data  = this_el.data('links');
  			
  			$.each(data,function(key, value){
  				var new_val = value+"&#13;&#10;";
				$(links).append(new_val);
			});
			
			this_el.on('click', function(){
				$('#linksModal .modal-body textarea').html($(links).html());
				$('#linksModal').modal('show');
				$('#linksModal .modal-body textarea').focus();
				$('#linksModal .modal-body textarea').select();
				$('#linksModal .modal-body textarea').on('mouseup touchend',function(){
					if(isTextSelected(this)){
						//console.log('selected');
						return false;
					}
					else {
						$(this).select();
					}
					//$('#linksModal .modal-body textarea').focus();
					
				});
			});
			// console.log($(links).html());
  			/*
var client = new ZeroClipboard( $(this) );

			client.on( "ready", function( readyEvent ) {
			  // alert( "ZeroClipboard SWF is ready!" );
			
				client.on( "copy", function( event ) {
					var clipboard = event.clipboardData;
					// console.log($(links).html());
					clipboard.setData( "text/plain", $(links).html() );
				});
			    client.on( "aftercopy", function( event ) {
				    // `this` === `client`
				    // `event.target` === the element that was clicked
				    // event.target.style.display = "none";
				    if (data.length > 0) {
					    var msg = ( data.length > 1 ) ? data.length+' Links' : data.length+' Link';
				    	alert("Copied " + msg + " to Clipboard." );
				    }
				});
			});
*/
  		});
	}
  	
  	/*=========== END TARGET LIST EVENTS ===========*/
  	
  	/*=========== START FUNCTIONS/METHODS ===========*/
  	function isTextSelected(input){
	   var startPos = input.selectionStart;
	   var endPos = input.selectionEnd;
	   var doc = document.selection;
	
	   if(doc && doc.createRange().text.length != 0){
	      return true;
	   }else if (!doc && input.value.substring(startPos,endPos).length != 0){
	      return true;
	   }
	   return false;
	}
  	
  	function show_modal ( msg ) {
  		msg = msg || 0;
		if ( msg ) {
			$('p#notice-content').removeClass('text-left');
			$('p#notice-content').addClass('text-center');
			$('p#notice-content').text( msg );
		}
		$('#video_search_modal').modal('show');
  	}
  	
  	function check_checkbox () {
	  	$('#search_result .ytcheckbox').each(function(){
	  		$('#check_all').prop('checked', true);
	  		$(this).prop('checked', true);
	  		if ( $('#get-ytlinks-button').length > 0 ) {
	  			$('#get-ytlinks-button').prop('disabled', false);
	  			$('#get-ytlinks-button').removeClass('btn-disabled');
	  			$('#get-ytlinks-button').addClass('btn-primary');
	  		}
	  		
		  	$(this).on('change',function(){
			  	if ( $(this).prop('checked') == false ) {
				  	$('#check_all').prop('checked', false);
			  	}
			  	else if ( $('#search_result .ytcheckbox').length == $('#search_result .ytcheckbox:checked').length ) {
				  	$('#check_all').prop('checked', true);
			  	}
			  	
			  	if ( $('#search_type').val() == 'video' ) {
				  	
				  	if ( $('#search_result .ytcheckbox:checked').length > 0 ) {
					  	$('#get-ytlinks-button').removeClass('btn-disabled');
					  	$('#get-ytlinks-button').addClass('btn-primary');
					  	$('#get-ytlinks-button').prop('disabled', false);
				  	}
				  	else {
					  	$('#get-ytlinks-button').removeClass('btn-primary');
					  	$('#get-ytlinks-button').addClass('btn-disabled');
					  	$('#get-ytlinks-button').prop('disabled', true);
				  	}
			  	}
		  	});
	  	});
  	}

  	function check_results_with_target ( ytdata ) {
		var check_length = $('#search_result .ytcheckbox').length;
		var check_count  = 0;
		
		if ( ytdata !== false ) {
	  		$('#search_result .ytcheckbox').each(function(i){
	  			var check_this = $(this);
				var data = check_this.parent().parent().data('ytdata');
				$.each( ytdata, function(key,value){
				
					if ( data.ytid == value.ytid ) {
						//console.log(data.ytid);
						$(check_this).prop('checked', true);
						check_count++;
						return false;
					}
					else {
						$(check_this).prop('checked', false);
					}
				});
				
				if ( check_count == check_length ) {
					$('#check_all').prop('checked', true);
				}
				else {
					$('#check_all').prop('checked', false);
				}
			});
		}
  	}
  	
  	function get_target_videos ( target_id, target_el ) {
		//- Show loader
		show_loader('Fetching Videos!', true, 100);
		
		// $('#check_all').prop('checked', true);
		
		// console.log($("#target_lists").data('action'));
		// console.log(target_id);
		
		var request = $.ajax({
			url: $("#target_lists").data('action'),
			type: "POST",
			crossDomain: true,
			data: { 
				target_id : target_id
			},
			dataType: "html"
		});

		request.done(function( msg ) {
			// console.log(msg);
			$(target_el).find('table tbody').html(msg);
			$('.main_loader .loader').html('Fetching Done!');
			$('.main_loader .loader').fadeOut(300,function(){
				$('.main_loader').hide();
				
				$(target_el).find('table').DataTable({
					"searching"	: false,
					"paging" 	: false,
					"info"		: false,
					"columns": [
					    { "orderable": false },
					    { "orderable": false },
					    null,
					    null,
					    null,
					    null,
					    null,
					    { "orderable": false }
					]
				});
				
				$('.video_link').each(function(){
					$(this).on('click', function(e){
						e.preventDefault();
						$.magnificPopup.open({
						    items: {
						      src: $(this).prop('href')
						    },
						    type: 'iframe'
						});
					});
				});
				
				$('.target_check_all').each(function(){
					$(this).on('change',function(){
						if ( $(this).prop('checked') == true ) {
				  			$(target_el).find('table tbody .ytcheckbox').each(function(i){
				  				$(this).prop('checked', true);
				  			});
				  			$(this).parent().next().find('.bulk_deleter').removeClass('btn-disabled');
				  			$(this).parent().next().find('.bulk_deleter').addClass('btn-danger');
				  			$(this).parent().next().find('.bulk_deleter').prop('disabled', false);
				  			
				  			
				  			$(this).parent().next().find('.bulk_mover').removeClass('btn-disabled');
				  			$(this).parent().next().find('.bulk_mover').addClass('btn-warning');
				  			$(this).parent().next().find('.bulk_mover').prop('disabled', false);
			  			}
			  			else {
				  			$(target_el).find('table tbody .ytcheckbox').each(function(i){
				  				$(this).prop('checked', false);
				  			});
				  			
				  			$(this).parent().next().find('.bulk_deleter').removeClass('btn-danger');
				  			$(this).parent().next().find('.bulk_deleter').addClass('btn-disabled');
				  			$(this).parent().next().find('.bulk_deleter').prop('disabled', true);
				  			
				  			
				  			$(this).parent().next().find('.bulk_mover').removeClass('btn-warning');
				  			$(this).parent().next().find('.bulk_mover').addClass('btn-disabled');
				  			$(this).parent().next().find('.bulk_mover').prop('disabled', true);
			  			}
					});
				});
				
				$(target_el).find('table tbody .ytcheckbox').each(function(){
					$(this).on('change', function(){
						if ( $(target_el).find('table tbody .ytcheckbox:checked').length > 0 ) {
							$(target_el).find('table thead .bulk_deleter').removeClass('btn-disabled');
				  			$(target_el).find('table thead .bulk_deleter').addClass('btn-danger');
				  			$(target_el).find('table thead .bulk_deleter').prop('disabled', false);
				  			
				  			
							$(target_el).find('table thead .bulk_mover').removeClass('btn-disabled');
				  			$(target_el).find('table thead .bulk_mover').addClass('btn-warning');
				  			$(target_el).find('table thead .bulk_mover').prop('disabled', false);
						}
						else {
							$(target_el).find('table thead .bulk_deleter').removeClass('btn-danger');
				  			$(target_el).find('table thead .bulk_deleter').addClass('btn-disabled');
				  			$(target_el).find('table thead .bulk_deleter').prop('disabled', true);
				  			
				  			
							$(target_el).find('table thead .bulk_mover').removeClass('btn-warning');
				  			$(target_el).find('table thead .bulk_mover').addClass('btn-disabled');
				  			$(target_el).find('table thead .bulk_mover').prop('disabled', true);
						}
					});
				});
				
				$('.delete_video').each(function(i){
		  			$(this).on('click',function(){
			  			var target_id     = $(this).parent().parent().data('list-id');
			  			var ytid          = $(this).parent().parent().data('video-id');
			  			var target_action = $(this).data('action');
			  			
		  				var msg = "Are you sure you that want to delete this Target?";
		  				$('#target_list_modal .modal-body').html('');
		  				$('#target_list_modal .modal-body').html(msg);
		  				$('#target_list_modal').modal('show');
		  				$('#modal-no').text('No');
		  				$('#modal-yes').show();
		  				
		  				$('#modal-yes').on('click',function(){
			  				var ytids = [];
			  				ytids.push(ytid);
					  		var request = $.ajax({
								url: target_action,
								type: "POST",
								crossDomain: true,
								data: {
									target_id   : target_id,
									ytids		: ytids
								}
							});
					
							request.done(function( msg ) {
								if ( msg ) {
									location.reload(true);
								}
							});
					
							request.fail(function( jqXHR, textStatus ) {
								show_modal("Request failed: " + textStatus + ". Please try again later.");
								// alert( "Request failed: " + textStatus );
								//ajax_done = false;
							});
		  				});
		  			});
		  		});
			});
		});

		request.fail(function( jqXHR, textStatus ) {
			// alert( "Request failed: " + textStatus );
			//ajax_done = false;
			show_loader('Fetching Failed!', false, 'slow');
		});
  	}
  	
	function input_checker ( input_el, button_el, btn_class ) {
	
		if ( $(input_el).val() == '' || $(input_el).val() == null ) {
			$(button_el).removeClass(btn_class);
			$(button_el).addClass('btn-disabled');
			$(button_el).prop('disabled',true);
		}
		
		$(input_el).on('keyup', function(){
			if ( $(this).val() != '' ) {
				$(button_el).removeClass('btn-disabled');
				$(button_el).addClass(btn_class);
				$(button_el).prop('disabled',false);
			}
			else {
				$(button_el).removeClass(btn_class);
				$(button_el).addClass('btn-disabled');
				$(button_el).prop('disabled',true);
			}
		});
	}

  	function search_init () {
		var keyword = $.trim( $('#search_input').val() );

		if ( $('#search_type').val() == 'video' ) {
		//pre_search();

			// if ( !localStorage.videos ) {
	  //   		var key_arr = [];
	  //   		key_arr.push(keyword);
	  //   		localStorage.setItem("videos", JSON.stringify(key_arr));
	  //   	}
	  //   	else {
	  //   		var key_arr = $.parseJSON(localStorage.videos);
	  //   		if ( $.inArray(keyword, key_arr) < 0 ) {
		 //    		if ( max_results == 500 ) {
			// 			$('#temp_search_modal').modal('show');
			// 		}
			// 		else {
			// 			pre_search();
			// 		}
	  //   		}
	  //   		else {
	  //   			$('#recent_search_modal .modal-body').html("You have recently searched for <strong>\""+keyword+"\"</strong>. Do you want to continue where you left off? or start from the beginning?");
			// 		//$('#recent_search_modal #continue_search').data('next', msg.next);
			// 		$('#recent_search_modal #reset_search').data('next', '');
			// 		$('#recent_search_modal').modal('show');
	  //   		}
	  //   	}
			
			var request = $.ajax({
				url: settings_url+'dashboard/dashboard_ajax/check_recent_searches',
				type: "POST",
				crossDomain: true,
				data: { 
					keyword : keyword
				},
				dataType: 'json'
			});

			request.done(function( msg ){
				console.log(msg);
				next_page = msg.next;
				//--  MAO: UNCOMMENT ONCE THE TOUR IS FINALISED!!! --//
				if ( msg.exist ) {
					$('#recent_search_modal .modal-body').html("You have recently searched for <strong>\""+keyword+"\"</strong>. Do you want to continue where you left off? or start from the beginning?");
					$('#recent_search_modal #continue_search').data('next', msg.next);
					$('#recent_search_modal #reset_search').data('next', '');
					$('#recent_search_modal').modal('show');
				}
				else {
					if ( max_results == 500 ) {
						$('#temp_search_modal').modal('show');
					}
					else {
						pre_search();
					}
				}
			});

		}
		else {
			pre_search();
		}
  	}
  	
  	function pre_search () {
	  	$('#show-target').val(0);
  		$('#update-target').slideUp('fast');
  		$('#search_result').fadeIn();
  		$('#check_all').prop('checked',false);
  		
  		if ( $('#search_type').val() == 'video' ) $('#right-side').fadeIn();
  		
  		// Set defaults
		if ( !cancelled ) {
			$('#search_result table tbody').html('');
			max_results = $('#max-results').val();
			ajax_results= 0;
		}
		else if ( cancelled && current_keyword == $('#search_input').val() && ajax_results == max_results ) {
			$('#search_result table tbody').html('');
			max_results = $('#max-results').val();
			ajax_results= 0;
		}
		else if ( cancelled && current_keyword != $('#search_input').val() ) {
			$('#search_result table tbody').html('');
			max_results = $('#max-results').val();
			ajax_results= 0;
			//next_page   = '';
		}
		else if ( !cancelled && $('#show-target').val() == 0 ) {
			$('#search_result table tbody').html('');
			max_results = $('#max-results').val();
			ajax_results= 0;
			//next_page   = '';
		}
		else if ( next_page == '' ) {
			$('#search_result table tbody').html('');
			max_results = $('#max-results').val();
			ajax_results= 0;
		}

		// Disable search input and button
		$('#search_input').attr('disabled', true);
		$('#search_button').attr('disabled', true);

		// Show progress
		$(".main_loader").show();
		$('#progress-containter').fadeIn(function(){
			$('#cancel-search').fadeIn(100);
		});
		cancelled = false;
		current_keyword = $('#search_input').val();
	  	search();
  	}


  	function search () {
  		var search_type = $('#search_type').val();
  		var progress_text   = 'Fetching '+ajax_results+' of '+max_results+' '+search_type+'s. This may take a while.';
  		var progress_status = ( ajax_results == 0 ) ? 1 : ( ajax_results / max_results ) * 100;
  		$('#progress-containter .progress .progress-bar').css('width', progress_status+'%');
  		$('#cancel-notice').text(progress_text);
  		$('#cancel-notice').fadeIn();

	  	var request = $.ajax({
			url: $("#search_form").data('action'),
			type: "POST",
			crossDomain: true,
			data: { 
				keyword 	: $('#search_input').val(),
				viewCount 	: $('#view-count').val(),
				page        : next_page
			},
			dataType: "json"
		});

		request.done(function( msg ) {
			console.log(msg);
			ajax_done = true;
			next_page = msg['next'];
			
			if ( $('#search_type').val() == 'video' ) {
				has_ads = msg['hasAds'];
				if ( has_ads ) {
					$('#search_result table tbody').append(msg['html']);
				}
			}
			else {
				has_videos = parseInt(msg['hasVideos']);
				if ( has_videos ) {
					$('#search_result table tbody').append(msg['html']);
				}
			}
			
		});

		request.fail(function( jqXHR, textStatus ) {
			// alert( "Request failed: " + textStatus );
			show_modal("Request failed: " + textStatus + ". Please try again later.");
			ajax_done = false;
			cancel_search();
		});
	}

	function cancel_search () {
		cancelled = true;
		// next_page = '';
		var video_text = ( ajax_results <= 1 ) ? 'video' : 'videos';
		$('#progress-containter .progress .progress-bar').css('width', '1%');
		$('#progress-containter').fadeOut(200, function(){
			$(".main_loader").hide();
		});
		$('#search_input').attr('disabled', false);
		$('#search_button').attr('disabled', false);
		// $('#search_result > p').text(ajax_results+' '+video_text+' found.');var title = '';
		var msg   = '';
		if ( ajax_results < 1 ) {
			title = 'FAILED';
			msg   = 'No monetized videos found.';
			$('#search_notice_modal .modal-body').removeClass('alert-success');
			$('#search_notice_modal .modal-body').addClass('alert-danger');
			$('#search_notice_modal .modal-header .modal-title').css('color','#a94442');
		}
		else {
			title = 'SUCCESS';
			msg   = ajax_results+' monetized '+video_text+' found.';
			$('#search_notice_modal .modal-body').removeClass('alert-danger');
			$('#search_notice_modal .modal-body').addClass('alert-success');
			$('#search_notice_modal .modal-header .modal-title').css('color','#3c763d');
		}

		$('#search_notice_modal .modal-body').html( msg );
		$('#search_notice_modal').modal('show');
		
		init_datatable();
	}
	
	function init_datatable () {
		var search_type = $('#search_type').val();
		if ( search_type == "video" ) {
			$('#search_result table').DataTable({
				"searching"	: false,
				"paging" 	: false,
				// "info"		: false,
				"columns": [
				    { "orderable": false },
				    { "orderable": false },
				    { "orderable": false },
				    { "orderable": false },
				    null,
				    null,
				    null,
				    null,
				    null
				]
			});
			
			if ( $('.video_link').length > 0 ) {
				$('.video_link').each(function(){
					$(this).on('click', function(e){
						e.preventDefault();
						$.magnificPopup.open({
						    items: {
						      src: $(this).prop('href')
						    },
						    type: 'iframe'
						});
					});
				});
			}
			
		}
		else {
			$('#search_result table').DataTable({
				"searching"	: false,
				"paging" 	: false,
				// "info"		: false,
				"columns": [
				    { "orderable": false },
				    { "orderable": false },
				    null,
				    null,
				    null,
				    { "orderable": false },
				    { "orderable": false }
				]
			});
			
			if ( $(".show_freemium").length > 0 ) {
				$('.show_freemium').each(function(){
					$(this).on('click', function(e){
						$('#freemium_modal .modal-body').html("Please Upgrade to Premium to access this feature. Thanks!");
						$('#freemium_modal').modal('show');
					});
				});
			}
			
			if ( $('.extract_videos').length > 0 ) {
				$('.extract_videos').each(function(){
					$(this).on('click', function(e){
						e.preventDefault();
						var target_id   = $('#channel_targets').val();
						var playlist_id = $(this).parent().parent().data('upsid');
						var videos      = $(this).parent().parent().data('videos');
						var channel_title = $(this).parent().parent().data('channel-title');
						
						$('#extract_videos').data('target-id', target_id);
						$('#extract_videos').data('playlist-id', playlist_id);
						$('#extract_videos').data('videos', videos);
						$('#extract_videos').data('channel-title', channel_title);
						
						$('#extract_videos_new').data('playlist-id', playlist_id);
						$('#extract_videos_new').data('videos', videos);
						$('#extract_videos_new').data('channel-title', channel_title);
						
						$('#extract_videos').prop('data-target-id', target_id);
						$('#extract_videos').prop('data-playlist-id', playlist_id);
						$('#extract_videos').prop('data-videos', videos);
						$('#extract_videos').prop('data-channel-title', channel_title);
						
						$('#extract_videos_new').prop('data-playlist-id', playlist_id);
						$('#extract_videos_new').prop('data-videos', videos);
						$('#extract_videos_new').prop('data-channel-title', channel_title);
						
						$('#channel_modal').modal('show');
					});
				});
			}
			
			if ( $('.video_link').length > 0 ) {
				$('.video_link').each(function(){
					$(this).on('click', function(e){
						e.preventDefault();
						var video_id = $(this).data('video-id');
						
						$.magnificPopup.open({
						    items: {
						      src: "https://www.youtube.com/watch?v="+video_id
						    },
						    type: 'iframe'
						});
					});
				});
			}
			
		}
		check_checkbox();
	}
	
	function extract_videos ( target_id, playlist_id, videos, channel_title ) {
		// console.log(channel_title);
		$('#channel_modal').modal('hide');
		$('#confirmation_modal').modal('show');
		// show_loader ( 'Saving to Target...', true, 100 );
		
		$('#channel_targets option:selected').remove();

		var request = $.ajax({
			url: $("#extract_videos").data('action'),
			type: "POST",
			crossDomain: true,
			data: { 
				target_id 	  : target_id,
				playlist_id   : playlist_id,
				videos		  : videos,
				channel_title : channel_title
			},
			dataType: "json"
		});

		/*
request.done(function( msg ) {
			console.log(msg);
			if ( parseInt( msg['videos'] ) > 0 ) {
				show_loader ( msg['videos'] + ' Videos Saved!', false, 100 );
				$('p#notice-content').removeClass('text-left');
				$('p#notice-content').addClass('text-center');
				$('p#notice-content').text( msg['videos'] + ' Videos Saved!' );
				$('#search_modal').modal('show');
			}
			else {
				show_loader ('No Monetized Videos!', false, 100 );
				$('p#notice-content').removeClass('text-left');
				$('p#notice-content').addClass('text-center');
				$('p#notice-content').text( 'No Monetized Videos!' );
				$('#search_modal').modal('show');
			}
		});

		request.fail(function( jqXHR, textStatus ) {
			// alert( "Request failed: " + textStatus );
			$('p#notice-content').removeClass('text-left');
			$('p#notice-content').addClass('text-center');
			$('p#notice-content').text( "Request failed: " + textStatus + ". Please try again later." );
			$('#search_modal').modal('show');
		});
*/
	}
	
	function extract_videos_new ( target_name, playlist_id, videos, channel_title ) {
		console.log(target_name);
		show_loader ( 'Checking...', true, 100 );
		
		var request = $.ajax({
			url: settings_url+'dashboard/dashboard_ajax/check_target_name',
			type: "POST",
			crossDomain: true,
			data: { 
				target_name   : target_name
			}
		});

		request.done(function(msg) {
			if ( msg ) {
				show_loader ( 'Target Name Valid!', false, 100 );
				$('#channel_modal').modal('hide');
				$('#confirmation_modal').modal('show');
				$.ajax({
					url: $("#extract_videos_new").data('action'),
					type: "POST",
					crossDomain: true,
					data: { 
						target_name   : target_name,
						playlist_id   : playlist_id,
						videos		  : videos,
						channel_title : channel_title
					},
					dataType: "json"
				});
			}
			else {
				show_loader ( 'Target Name Exist!', false, 3000 );
			}
		});

		/*
request.done(function( msg ) {
			console.log(msg);
			if ( msg['valid'] ) {
				if ( parseInt( msg['videos'] ) > 0 ) {
					show_loader ( msg['videos'] + ' Videos Saved!', false, 100 );
					$('p#notice-content').removeClass('text-left');
					$('p#notice-content').addClass('text-center');
					$('p#notice-content').text( msg['videos'] + ' Videos Saved!' );
					$('#search_modal').modal('show');
				}
				else {
					show_loader ('No Monetized Videos!', false, 100 );
					$('p#notice-content').removeClass('text-left');
					$('p#notice-content').addClass('text-center');
					$('p#notice-content').text( 'No Monetized Videos!' );
					$('#search_modal').modal('show');
				}
			}
			else {
				show_loader ('Duplicate Name!', false, 100 );
				$('p#notice-content').removeClass('text-left');
				$('p#notice-content').addClass('text-center');
				$('p#notice-content').text( 'Duplicate Target Name' );
				$('#search_modal').modal('show');
			}
		});

		request.fail(function( jqXHR, textStatus ) {
			show_loader ('', false, 100 );
			// alert( "Request failed: " + textStatus );
			$('p#notice-content').removeClass('text-left');
			$('p#notice-content').addClass('text-center');
			$('p#notice-content').text( "Request failed. Please try again later." );
			$('#search_modal').modal('show');
		});
*/
	}
	
	function show_loader ( msg, trigger, speed ) {
		//- Show loader
		if ( trigger ) {
			$('.main_loader').show();
			$('.main_loader .loader').fadeIn(speed);
			$('.main_loader .loader').html( msg );
		}
		else {
			$('.main_loader .loader').html( msg );
			$('.main_loader .loader').fadeOut(speed,function(){
				$('.main_loader').hide();
			});
		}
	}
	
	// init_datatable();
	
	function SelectText(element) {
	    var doc = document
	        , text = doc.getElementById(element)
	        , range, selection
	    ;    
	    if (doc.body.createTextRange) {
	        range = document.body.createTextRange();
	        range.moveToElementText(text);
	        range.select();
	    } else if (window.getSelection) {
	        selection = window.getSelection();        
	        range = document.createRange();
	        range.selectNodeContents(text);
	        selection.removeAllRanges();
	        selection.addRange(range);
	    }
	}

	$( document ).ajaxComplete(function( event, xhr, settings ) {
	  	// console.log( event );
	  	// console.log( xhr );
	  	// console.log( settings );
	  	if ( settings.url == settings_url+"dashboard/dashboard_ajax/video_search" ||
	  		 settings.url == settings_url+"dashboard/dashboard_ajax/channel_search" ) {
		  	if ( ajax_done ) {
		  		if ( next_page && next_page != '' ) {
			  		if ( has_videos && settings.url == settings_url+"dashboard/dashboard_ajax/channel_search" ||
			  			 has_ads && settings.url == settings_url+"dashboard/dashboard_ajax/video_search" ) {
			  			ajax_results++;
			  		}
			  			
			  		if ( !cancelled ) {
				  		if ( ajax_results < max_results ) {
				  			search();
				  		}
				  		else { // Search Complete
				  			//next_page = '';
					  		$('#resume-search-button').hide();
							cancel_search();
				  		}
			  		}
			  		else {
			  			//- Show resume search
			  			if ( ajax_results < max_results ) {
			  				$('#resume-search-button').show();
			  			}
			  			else if ( ajax_results == max_results ) {
				  			//next_page = '';
					  		$('#resume-search-button').hide();
			  			}
						cancel_search();
			  		}
			  	}
			  	else {
					$('#resume-search-button').hide();
				  	cancel_search();
			  	}
		  	}
	  	}
	});

	/*
$(document).on('click','#keyword_search',function(){
			
		keyword_search( $.trim( $('#keyword').val() ) );
			
	});
*/
	
	/*
$(document).on('submit','#keyword_form',function(){
			
		keyword_search( $.trim( $('#keyword').val() ) );
			
	});
*/

	$(document).on('click','#monetize_check',function(){

		$("#search_result").html("Checking....");
			
	});

	function Post () {
		var _url = $("#url").val();
		//alert(encodeURIComponent(_url));
		
		

		// $.ajax({
		// 	type: "POST",
		// 	url: "http://localhost/myverrol/butong.php",
		// 	data: { url: encodeURI(_url)},
		// 	cache: false
		// })
		// .done(function( html ) {

		// 	console.log(html);

		// 	if(parseInt(html) == 1) {
		// 		$("#search_result").html("Has Ads.");
		// 	}
		// 	else {
		// 		$("#search_result").html("No Ads.");
		// 	}
		// });
	}
	
});

$.fn.OneClickSelect = function () {
  return $(this).on('click', function () {

    // In here, "this" is the element

    var range, selection;

    // non-IE browsers account for 60% of users, this means 60% of the time,
    // the two conditions are evaluated since you check for IE first. 

    // Instead, reverse the conditions to get it right on the first check 60% of the time.

    if (window.getSelection) {
      selection = window.getSelection();
      range = document.createRange();
      range.selectNodeContents(this);
      selection.removeAllRanges();
      selection.addRange(range);
    } else if (document.body.createTextRange) {
      range = document.body.createTextRange();
      range.moveToElementText(this);
      range.select();
    }
  });
};
