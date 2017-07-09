var fireBaseURL = 'https://radiant-inferno-7406.firebaseio.com/';
$(document).ready(function(){
	
	if (!room) {
		$('#loading-spinner').removeClass('spinner-hidden');
		var data = {
			type		: 'get_live_rooms',
			db_id    	: userId,
			user_type 	: userType
		};
		$.ajax({
            type: "POST",
            url: baseurl+"room-ajax.php",
            data: data,
            dataType: 'html',
            success: function(data) {
				$('#loading-spinner').addClass('spinner-hidden');
            	if (data) {
	            	$('#stream-list').html(data);
	            	$('.feed-item').on('click', function(e){
						e.preventDefault();
						var thisData = $(this).data();
						if (e.target.id == "end-room") {
							//end_room($('#end-room'),thisData.stream_id);
						}
						else {
							var data = {
								type	: 'check_room',
								room	: thisData.slug
							};
							$.ajax({
					            type: "POST",
					            url: baseurl+"room-ajax.php",
					            data: data,
					            success: function(data) {
					                console.log(data);
					                if (data) {
										window.location.href = baseurl+thisData.slug;
					                }
					            }
					        });
						}
					});
					
					$('#stream-list .create-channel').on('click', function(e){
						e.preventDefault();
						$('#modal').show();
					});
            	}
            }
        });
	}
	else {
		if (token) {
			initializeSession(token);
	    	
			var data = {
				type		: 'update_room',
				room    	: roomSlug,
				token	 	: token
			};
			$.ajax({
	            type: "POST",
	            url: baseurl+"room-ajax.php",
	            data: data,
	            dataType: 'json',
	            success: function(data) {
	                
					var myFirebaseRef = new Firebase(fireBaseURL+"data/room/"+data.msg.session_id);
					myFirebaseRef.once("value", function(snapshot) {
					var newPost = snapshot.val();
					var roomExist = false;
					if (!newPost) {
			                myFirebaseRef.set({
					    		"GuestFavorite" 	: 0,
					    		"TrainerFavorite" 	: 0,
					    		"is_private"		: false,
					    		"trainer_fav"		: {},
					    		"guest_fav"			: {},
					    		"allow_guest_client": data.msg.allow_guest_client,
					    		"online"			: {},
					    		"owner_id"			: data.msg.trainer_id,
					    		"room_name"			: data.msg.name,
					    		"views"				: 0
					    	});
						}
					});
	            }
	        });
			
		}
		else {
			window.location.href= baseurl;
		}
	}
	
	if (!room) {
	
		$(document.body).click( function(e) {
		    $('.userNavToggle').removeClass('open');
			$('.nav-user-navigation-dropdown').hide();
		});
	
		//- Get Rooms
		$('#live').on('click',function(e){
			e.preventDefault();
			$(this).parent().siblings().find('a').removeClass('selected');
			$(this).parent().siblings().css('background-color', 'transparent');
			$(this).addClass('selected');
			$(this).parent().css('background-color', '#27AE60');
			$('#loading-spinner').removeClass('spinner-hidden');
			$('#scheduled-list').html('');
			$('#archive-list').html('');
			var data = {
				type		: 'get_live_rooms',
				db_id    	: userId,
				user_type 	: userType
			};
			$.ajax({
	            type: "POST",
	            url: baseurl+"room-ajax.php",
	            data: data,
	            dataType: 'html',
	            success: function(data) {
					$('#loading-spinner').addClass('spinner-hidden');
	            	if (data) {
		            	$('#stream-list').html(data);
		            	$('.feed-item').on('click', function(e){
							e.preventDefault();
							var thisData = $(this).data();
							if (e.target.id == "end-room") {
								//end_room($('#end-room'),thisData.stream_id);
							}
							else {
								var data = {
									type	: 'check_room',
									room	: thisData.slug
								};
								$.ajax({
						            type: "POST",
						            url: baseurl+"room-ajax.php",
						            data: data,
						            success: function(data) {
						                //console.log(data);
						                if (data) {
											window.location.href = baseurl+thisData.slug;
						                }
						            }
						        });
							}
						});
						
						
		            	$('#stream-list .create-channel').on('click', function(e){
							e.preventDefault();
							$('#modal').show();
						});
	            	}
	            }
	        });
		});
		
		$('#scheduled').on('click',function(e){
			e.preventDefault();
			$(this).parent().siblings().find('a').removeClass('selected');
			$(this).parent().siblings().css('background-color', 'transparent');
			$(this).addClass('selected');
			$(this).parent().css('background-color', '#27AE60');
			$('#loading-spinner').removeClass('spinner-hidden');
			$('#stream-list').html('');
			$('#archive-list').html('');
			var data = {
				type		: 'get_scheduled_rooms',
				db_id    	: userId,
				user_type 	: userType
			};
			$.ajax({
	            type: "POST",
	            url: baseurl+"room-ajax.php",
	            data: data,
	            dataType: 'html',
	            success: function(data) {
					$('#loading-spinner').addClass('spinner-hidden');
	            	if (data) {
		            	$('#scheduled-list').html(data);
		            	
		            	$('#scheduled-list > li').each(function(key,val){
			            	var schedTimeEl = $(val).find('p.scheduled-time');
			            	var schedTime   = moment.unix($(schedTimeEl).data('timestamp')).calendar();
			            	$(schedTimeEl).text(schedTime);
		            	});
		            	
		            	
		            	$('#scheduled-list .create-channel').on('click', function(e){
							e.preventDefault();
							$('#modal').show();
						});
	            	}
	            }
	        });
		});
		
		$('#archive').on('click',function(e){
			e.preventDefault();
			$(this).parent().siblings().find('a').removeClass('selected');
			$(this).parent().siblings().css('background-color', 'transparent');
			$(this).addClass('selected');
			$(this).parent().css('background-color', '#27AE60');
			$('#loading-spinner').removeClass('spinner-hidden');
			$('#stream-list').html('');
			$('#scheduled-list').html('');
			var data = {
				type		: 'get_archive_rooms',
				db_id    	: userId,
				user_type 	: userType
			};
			$.ajax({
	            type: "POST",
	            url: baseurl+"room-ajax.php",
	            data: data,
	            dataType: 'html',
	            success: function(data) {
					$('#loading-spinner').addClass('spinner-hidden');
					//console.log(data);
	            	if (data) {
		            	$('#archive-list').html(data);
		            	$('#archive-list .create-channel').on('click', function(e){
							e.preventDefault();
							$('#modal').show();
						});
		            	/*
	$('.feed-item').on('click', function(e){
							e.preventDefault();
							var thisData = $(this).data();
							if (e.target.id == "end-room") {
								end_room($('#end-room'),thisData.stream_id);
							}
							else {
								//window.location.href = baseurl+"room.php?room="+thisData.stream_id;
								var data = {
									type	: 'check_room',
									room	: thisData.slug
								};
								$.ajax({
						            type: "POST",
						            url: baseurl+"room-ajax.php",
						            data: data,
						            success: function(data) {
						                //console.log(data);
						                if (data) {
											window.location.href = baseurl+thisData.slug;
						                }
						            }
						        });
							}
						});
	*/
	            	}
	            }
	        });
		});
		
		$('.create-channel').on('click', function(e){
			e.preventDefault();
			$('#modal').show();
		});
		
		$('#modal .close').on('click', function(e){
			e.preventDefault();
			$('#modal').hide();
		});
		
		$('#rename-input').on('keyup', function(e){
			e.preventDefault();
			var msg = $.trim($(this).val());
	        $('#room-char-count').html(msg.length);
	        
	        if (msg != '' && msg.length > 0) {
		        $('#start-now-stream').removeClass('disabled');
	        }
	        else {
		        /*
if ( !$('#aw-guest-client').hasClass('disabled') ) $('#aw-guest-client').addClass('disabled');
		        if ( !$('#aw-twitter-list').hasClass('disabled') ) $('#aw-twitter-list').addClass('disabled');
		        if ( !$('#create-time').hasClass('disabled') ) $('#create-time').addClass('disabled');
*/
		        if ( !$('#start-now-stream').hasClass('disabled') ) $('#start-now-stream').addClass('disabled');
	        }
		}).keypress(function(e){
			var nameRegex = /^[a-z0-9\-\s]+$/i;
			var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
			
			if (nameRegex.test(str)) {
				return true;
			}
			e.preventDefault();
			return false;
		});
		
		$('#banner-input:file').on('change', function(){
			//console.log(readImage($(this)[0].files[0]));
			if ($(this).val() != '') {
				readImage($(this)[0].files[0]);
			}
		});
		
		$('#label-type1').on('click', function(e){
			e.preventDefault();
			$('#banner-type-2').hide();
			$('#banner-type-1').show();
			if ( !$(this).hasClass('selected') ) {
				$(this).addClass('selected');
				$(this).prev().prop('selected', true);
				$('#label-type2').removeClass('selected');
			}
			else {
				$(this).prev().prop('selected', false);
				$(this).removeClass('selected');
			}
			
			/*
if ( $('.item-create-category.selected').length > 0 ) {
				$('#aw-guest-client').removeClass('disabled');
			}
			else {
		        if ( !$('#aw-guest-client').hasClass('disabled') ) $('#aw-guest-client').addClass('disabled');
		        if ( !$('#aw-twitter-list').hasClass('disabled') ) $('#aw-twitter-list').addClass('disabled');
		        if ( !$('#create-time').hasClass('disabled') ) $('#create-time').addClass('disabled');
		        if ( !$('#start-now-stream').hasClass('disabled') ) $('#start-now-stream').addClass('disabled');
			}
*/
		});
		
		$('#label-type2').on('click', function(e){
			e.preventDefault();
			$('#banner-type-1').hide();
			$('#banner-type-2').show();
			if ( !$(this).hasClass('selected') ) {
				$(this).addClass('selected');
				$(this).prev().prop('selected', true);
				$('#label-type1').removeClass('selected');
			}
			else {
				$(this).prev().prop('selected', false);
				$(this).removeClass('selected');
			}
			
			/*
if ( $('.item-create-category.selected').length > 0 ) {
				$('#aw-guest-client').removeClass('disabled');
			}
			else {
		        if ( !$('#aw-guest-client').hasClass('disabled') ) $('#aw-guest-client').addClass('disabled');
		        if ( !$('#aw-twitter-list').hasClass('disabled') ) $('#aw-twitter-list').addClass('disabled');
		        if ( !$('#create-time').hasClass('disabled') ) $('#create-time').addClass('disabled');
		        if ( !$('#start-now-stream').hasClass('disabled') ) $('#start-now-stream').addClass('disabled');
			}
*/
		});
		
		$('#label-guest-client1').on('click', function(e){
			e.preventDefault();
			
			if ( !$(this).hasClass('selected') ) {
				$(this).addClass('selected');
				$(this).prev().prop('selected', true);
				$('#label-guest-client2').removeClass('selected');
			}
			else {
				$(this).prev().prop('selected', false);
				$(this).removeClass('selected');
			}
			
			/*
if ( $('.item-guest-client.selected').length > 0 ) {
				$('#aw-twitter-list').removeClass('disabled');
			}
			else {
		        if ( !$('#aw-twitter-list').hasClass('disabled') ) $('#aw-twitter-list').addClass('disabled');
		        if ( !$('#create-time').hasClass('disabled') ) $('#create-time').addClass('disabled');
		        if ( !$('#start-now-stream').hasClass('disabled') ) $('#start-now-stream').addClass('disabled');
			}
*/
		});
		
		$('#label-guest-client2').on('click', function(e){
			e.preventDefault();
			
			if ( !$(this).hasClass('selected') ) {
				$(this).addClass('selected');
				$(this).prev().prop('selected', true);
				$('#label-guest-client1').removeClass('selected');
			}
			else {
				$(this).prev().prop('selected', false);
				$(this).removeClass('selected');
			}
			
			/*
if ( $('.item-guest-client.selected').length > 0 ) {
				$('#aw-twitter-list').removeClass('disabled');
			}
			else {
		        if ( !$('#aw-twitter-list').hasClass('disabled') ) $('#aw-twitter-list').addClass('disabled');
		        if ( !$('#create-time').hasClass('disabled') ) $('#create-time').addClass('disabled');
		        if ( !$('#start-now-stream').hasClass('disabled') ) $('#start-now-stream').addClass('disabled');
			}
*/
		});
		
		$('#label-twitter-list1').on('click', function(e){
			e.preventDefault();
			$('#aw-twitter-list-create').hide();
			$('#aw-twitter-list-dropdown').show();
			if ( !$(this).hasClass('selected') ) {
				$(this).addClass('selected');
				$(this).prev().prop('selected', true);
				$('#label-twitter-list2').removeClass('selected');
			}
			else {
				$(this).prev().prop('selected', false);
				$(this).removeClass('selected');
			}
			
			/*
if ( $('.item-twitter-list.selected').length > 0 ) {
				//$('#start-now-stream').removeClass('disabled');
				$('#create-time').removeClass('disabled');
			}
			else {
		        if ( !$('#create-time').hasClass('disabled') ) $('#create-time').addClass('disabled');
		        if ( !$('#start-now-stream').hasClass('disabled') ) $('#start-now-stream').addClass('disabled');
			}
*/
		});
		
		$('#label-twitter-list2').on('click', function(e){
			e.preventDefault();
			$('#aw-twitter-list-dropdown').hide();
			$('#aw-twitter-list-create').show();
			if ( !$(this).hasClass('selected') ) {
				$(this).addClass('selected');
				$(this).prev().prop('selected', true);
				$('#label-twitter-list1').removeClass('selected');
			}
			else {
				$(this).prev().prop('selected', false);
				$(this).removeClass('selected');
			}
			
			/*
if ( $('.item-twitter-list.selected').length > 0 ) {
				//$('#start-now-stream').removeClass('disabled');
				$('#create-time').removeClass('disabled');
			}
			else {
		        if ( !$('#create-time').hasClass('disabled') ) $('#create-time').addClass('disabled');
		        if ( !$('#start-now-stream').hasClass('disabled') ) $('#start-now-stream').addClass('disabled');
			}
*/
		});
		
		$('#item-create-now').on('click', function(e){
			e.preventDefault();
			$('#create-time .scheduling-container').addClass('hidden');
			if ( !$(this).hasClass('selected') ) {
				$(this).addClass('selected');
				$(this).prev().prop('selected', true);
				$('#item-create-schedule').removeClass('selected');
			}
			else {
				$(this).prev().prop('selected', false);
				$(this).removeClass('selected');
			}
			
			/*
if ( $('.item-create-time.selected').length > 0 ) {
				$('#start-now-stream').removeClass('disabled');
			}
			else {
		        if ( !$('#start-now-stream').hasClass('disabled') ) $('#start-now-stream').addClass('disabled');
			}
*/
		});
		
		$('#item-create-schedule').on('click', function(e){
			e.stopPropagation();
			e.preventDefault();
			$('#create-time .scheduling-container').removeClass('hidden');
			if ( !$(this).hasClass('selected') ) {
				$(this).addClass('selected');
				$(this).prev().prop('selected', true);
				$('#item-create-now').removeClass('selected');
			}
			else {
				$(this).prev().prop('selected', false);
				$(this).removeClass('selected');
			}
			
			var month = moment().month();
			var year  = moment().year();
			var date  = moment().date();
			var hour  = moment().format("hh");
			var minute= moment().format("mm");
			var tomoz = date+1;
			var year_month 	= year+'-'+month;
			var days  		= moment(year_month, "YYYY-MM").daysInMonth();
			var year_text 	= moment().format("YYYY");
			var month_text 	= moment().format("MMM");
			var month_digit	= moment().format("MM");
			var date_digit	= moment().format("DD");
			var time_text   = moment().format("a");
			var num_days 	= 0;
			
			$('#create-time ul.days-container').html('');
			
			for (var i=0; i<days; i++) {
				if ( i >= tomoz ) {
					var day_z = ($.trim(i).length === 1) ? '0' + i : i;
					/*
if ( i == date ) {
						var day_html  = '<li id="days-'+num_days+'" class="days-select selected" data-date="'+year_text+'-'+month_digit+'-'+date_digit+'" data-day-id="'+day_z+' '+month_text+'">';
						day_html     += '<div>';
						day_html     += '<input id="day" type="radio" name="day" value="'+day_z+' '+month_text+'">';
						day_html     += '<label for="day"><small>Today</small></label>';
						day_html     += '</div></li>';
					}
					else 
*/if ( i == tomoz ) {
						var day_html  = '<li id="days-'+num_days+'" class="days-select" data-date="'+year_text+'-'+month_digit+'-'+day_z+'" data-day-id="'+day_z+' '+month_text+'">';
						day_html     += '<div>';
						day_html     += '<input id="day" type="radio" name="day" value="'+day_z+' '+month_text+'">';
						day_html     += '<label for="day"><small>Tomorrow</small></label>';
						day_html     += '</div></li>';
					}
					else {
						var day_html  = '<li id="days-'+num_days+'" class="days-select" data-date="'+year_text+'-'+month_digit+'-'+day_z+'" data-day-id="'+day_z+' '+month_text+'">';
						day_html     += '<div>';
						day_html     += '<input id="day" type="radio" name="day" value="'+day_z+' '+month_text+'">';
						day_html     += '<label for="day"><small>'+day_z+' '+month_text+'</small></label>';
						day_html     += '</div></li>';
					}
					num_days++;
				}
				
				$('#create-time ul.days-container').append(day_html);
			}
			
			$('#hours').val(hour);
			$('#minutes').val(minute);
			
			if ( $('#am').val() == time_text ) {
				$('#am').prop('checked',true);
			}
			else {
				$('#pm').prop('checked',true);
			}
			
			$('#create-time ul.days-container .days-select').on('click', function(e){
				e.stopPropagation();
				e.preventDefault();
				$(this).addClass('selected').siblings().removeClass('selected');
				$(this).find('input').prop('checked',true).siblings().prop('checked',false);
				//console.log($('#hours').val()+':'+$('#minutes').val()+' '+$('.am-pm-selector input[name="am-pm"]:checked').val());
				//console.log($('#create-time ul.days-container .days-select.selected').find('input[name="day"]').val());
			});
			
			/*
if ( $('.item-create-time.selected').length > 0 ) {
				$('#start-now-stream').removeClass('disabled');
			}
			else {
		        if ( !$('#start-now-stream').hasClass('disabled') ) $('#start-now-stream').addClass('disabled');
			}
*/
		});
		
		$('#hours').on('keyup', function(){
			var thisVal = parseInt($(this).val());
			if (thisVal > 12) {
				$(this).val(12);
			}
		});
		
		$('#minutes').on('keyup', function(){
			var thisVal = parseInt($(this).val());
			if (thisVal > 59) {
				$(this).val(59);
			}
		});
		
		$('.btn-social').on('click', function(e){
			e.preventDefault();
			var pixel = $(this).data('pixel');
			$('#pixels-modal .modal-title').text('Enter '+pixel.toUpperCase()+' pixel');
			$('#pixels-modal .modal-body #'+pixel+'-pixel').show().siblings().hide();
			if ( $('#pixels-modal .modal-body #'+pixel+'-pixel').val() != '' ) {
				$('#save-pixel').removeClass('disabled');
			}
			$('#pixels-modal').data('pixel',pixel);
			$('#modal').css('z-index', '1040');
			$('#pixels-modal').modal('show');
		});
		
		$('#save-pixel').on('click', function(e){
			var pixel = $('#pixels-modal').data('pixel');
			$('.btn-'+pixel).removeClass('pixel-empty');
			$('#remove-'+pixel+'-pixel').show();
			if (!$('#save-pixel').hasClass('disabled')) $('#save-pixel').addClass('disabled');
			$('#pixels-modal').modal('hide');
		});
		
		$('#cancel-pixel').on('click', function(e){
			var pixel = $('#pixels-modal').data('pixel');
			
			if ( $('.btn-'+pixel).hasClass('pixel-empty') ) {
				$('.btn-'+pixel).addClass('pixel-empty');
				$('#remove-'+pixel+'-pixel').hide();
				$('#'+pixel+'-pixel').val('');
			}
			if (!$('#save-pixel').hasClass('disabled')) $('#save-pixel').addClass('disabled');
			$('#pixels-modal').modal('hide');
		});
		
		$('#facebook-pixel, #twitter-pixel, #google-pixel').keyup(function(){
			if ($(this).val() != '') {
				$('#save-pixel').removeClass('disabled');
			}
			else {
				if (!$('#save-pixel').hasClass('disabled')) $('#save-pixel').addClass('disabled');
			}
		});
		
		$('#remove-facebook-pixel, #remove-twitter-pixel, #remove-google-pixel').on('click', function(e){
			e.preventDefault();
			var pixel = $(this).data('pixel');
			$('.btn-'+pixel).addClass('pixel-empty');
			$('#remove-'+pixel+'-pixel').hide();
			$('#'+pixel+'-pixel').val('');
		});
		
		$('#start-now-stream').on('click', function(e){
			e.preventDefault();
			
			$('#loading-screen h1').text('Creating Cast...');
			$('#loading-screen').fadeIn();
			
			var thisButton = $(this);
			var bannerType = $('.item-create-category.selected').prev().val();
			var twitterList= $('.item-twitter-list.selected').prev().val();
			var schedule   = $('.item-create-time.selected').prev().val();
			thisButton.addClass('disabled');
			
			var data = new FormData();
			data.append('type','create_room');
			data.append('name',$.trim($('#rename-input').val()));
			data.append('banner',$('#banner-input')[0].files[0]);
			data.append('banner_type',parseInt(bannerType));
			data.append('guest_client',$('.item-guest-client.selected').prev().val());
			data.append('trainer_id',userId);
			data.append('fb_pixel',$('#facebook-pixel').val());
			data.append('tt_pixel',$('#twitter-pixel').val());
			data.append('ad_pixel',$('#google-pixel').val());
			
			if ( parseInt(bannerType) == 1 ) {
				data.append('mc_api',$.trim($('#banner-mc-api').val()));
				data.append('mc_list',$.trim($('#banner-mc-list').val()));
			}
			else if ( parseInt(bannerType) == 2 ) {
				data.append('banner_url',$.trim($('#banner-url').val()));
			}
			
			data.append('twitter_list',twitterList);
			
			if ( twitterList == 0 ) {
				data.append('twitter_list_name',$.trim($('#twitter-list-new').val()));
			}
			else {
				data.append('twitter_list_id',$('#twitter-lists').val());
			}
			
			if ( schedule == 'schedule' ) {
				var date      = $('#create-time ul.days-container .days-select.selected').data('date');
				var time_text = $('#hours').val()+':'+$('#minutes').val()+' '+$('.am-pm-selector input[name="am-pm"]:checked').val();
				var time      = moment(time_text, ["h:mm A"]).format('HH:mm:ss ZZ');
				var date_time = date+' '+time;
				var date_unix = moment(date_time).utc().unix();
				data.append('schedule_time',date_time);
				data.append('schedule_time_timestamp',date_unix);
				//console.log(moment.unix(date_unix).format('YYYY-MM-DD HH:mm:ss ZZ'));
			}
			/*
	console.log(data);
			console.log($('#fb-pixel textarea').val());
			console.log($('#tt-pixel textarea').val());
			console.log($('#ad-pixel textarea').val());
	*/
			$.ajax({
	            type: "POST",
	            url: baseurl+"room-ajax.php",
	            data: data,
	            dataType: 'json',
			    cache: false,
			    contentType: false,
			    processData: false,
	            success: function(data) {
	                thisButton.removeClass('disabled');
	                //initializeSession(data);
	                console.log(data);
	                if (data.success && !data.is_sched) {
	                	window.location.href = baseurl+data.msg;
	                }
	                else if ( data.success && data.is_sched ) {
		                location.reload(true);
	                }
	                else {
						$('#loading-screen').fadeOut();
		                alert(data.msg);
		                thisButton.removeClass('disabled');
	                }
	            }
	        });
		});
		
		$('.userNavToggle').on('click', function(e){
			e.stopPropagation();
			e.preventDefault();
			//$('.popover').popover('show')
			if ( $(this).hasClass('open') ) {
				$(this).removeClass('open');
				$('.nav-user-navigation-dropdown').hide();
			}
			else {
				$(this).addClass('open');
				$('.nav-user-navigation-dropdown').show();
			}
		});
		
		
		$('.fileinput').on('clear.bs.fileinput', function(){
			if ( !$('#aw-banner-type').hasClass('disabled') ) $('#aw-banner-type').addClass('disabled');
		});
		
		
		
		$('#twitter-lists').selectize({
		    create: true,
		    sortField: 'text'
		});
	
	}
	else {
		init_event();
		
		$('#end-room').on('click', function(e){
			e.stopPropagation();
			e.preventDefault();
			end_room();
		});
		
		$('.header-logo').on('click', function(e){
			e.preventDefault();
			end_room();
		});
		
		$('#email-share').on('click', function(e){
			e.preventDefault();
			var popUpHTML = '<div id="email-modal" tabindex="-1" role="dialog" aria-labelledby="email-modal-label">' +
								  '<div class="modal-dialog" role="document">' +
								    '<div class="modal-content">' +
								      '<div class="modal-header text-center">' +
								      	'<button style="position:static;" type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
								        '<h4 class="modal-title" id="myModalLabel">Email this Cast!</h4>' +
								      '</div>' +
								      '<div class="modal-body">' +
								      	'<form>' +
								      		'<div class="form-group">' +
									  			'From: <input placeholder="Enter multiple emails separated by comma" type="text" class="form-control" id="cast-email-from"><br/>' +
									  			'To: <input placeholder="Enter multiple emails separated by comma" type="text" class="form-control" id="cast-emails">' +
									  		'</div>' +
									  	'</form>' +
									  	'<p id="email-error" class="text-danger" style="display:none;">You have entered an invalid email.</p>' +
								      '</div>' +
								      '<div class="modal-footer">' +
								        '<button id="email-cast-send" type="button" class="btn btn-success">SEND INVITE</button>' +
								      '</div>' +
								    '</div>' +
								  '</div>' +
								'</div>';
				$('#modal').html(popUpHTML);
				$('#modal').modal('show');
				
			var validFromEmail = false;
			var validToEmail   = false;
			$('#email-cast-send').removeClass('btn-success');
			$('#email-cast-send').addClass('disabled');
			$('#email-cast-send').addClass('btn-default');
			
			$('#cast-emails').on('keyup', function(e){
				e.preventDefault();
				if ($(this).val() != '') {
					
					if (validFromEmail) {
						$('#email-cast-send').removeClass('btn-default');
						$('#email-cast-send').removeClass('disabled');
						$('#email-cast-send').addClass('btn-success');
					}
					else {
						$('#email-cast-send').removeClass('btn-success');
						$('#email-cast-send').addClass('disabled');
						$('#email-cast-send').addClass('btn-default');
					}
				}
				else {
					$('#email-cast-send').removeClass('btn-success');
					$('#email-cast-send').addClass('disabled');
					$('#email-cast-send').addClass('btn-default');
				}
			});
			
			$('#cast-email-from').on('keyup', function(e){
				var email = $.trim($(this).val());
		    	if ( email != '' ) {
						
		    		var pattern = new RegExp(/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i);
		    		
		    		if ( pattern.test(email) ) {
		    			validFromEmail = true;
						$(this).css('border-color', '#468847');
		    		}
		    		else {
		    			validFromEmail = false;
			    		$(this).css('border-color', 'red');
		    		}
		    		
		    		
	    			if (validToEmail) {
		    			$('#email-cast-send').removeClass('btn-default');
						$('#email-cast-send').removeClass('disabled');
						$('#email-cast-send').addClass('btn-success');
	    			}
	    			else {
		    			$('#email-cast-send').removeClass('btn-success');
						$('#email-cast-send').addClass('disabled');
						$('#email-cast-send').addClass('btn-default');
	    			}
		    		
		    	}
			});
			
			$('#email-cast-send').on('click', function(e){
				e.preventDefault();
				var emails = $.trim($('#cast-emails').val());
		    	if ( emails != '' ) {
					$('#email-cast-send').removeClass('btn-success');
					$('#email-cast-send').addClass('disabled');
					$('#email-cast-send').addClass('btn-default');
						
		    		var pattern = new RegExp(/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i);
		    		
		    		var emailArray = emails.split(',');
		    		var has_errors = false;
					
					$.each(emailArray, function(key, val){
						var email = $.trim(val);
						if ( !pattern.test(email) ) {
							has_errors = true;
							return false;
						}
					});
					
					if ( has_errors ) {
						$('#cast-emails').css('border-color', 'red');
						$('#email-error').show();
						validToEmail = true;
					}
					else {
						validToEmail = false;
						$('#cast-emails').css('border-color', '#468847');
						var request = $.ajax({
					         type: 'POST',
					         url: baseurl+"room-ajax.php",
					         data: { 
					             'type'  	: 'email_invites',
					             'slug'		: roomSlug,
					             'from'		: $('#cast-email-from').val(),
					             'emails'   : $('#cast-emails').val()
					         }
					     });
						 
						 request.done(function( msg ) {
							 $('#email-error').hide();
							 $('#email-modal .modal-body').addClass('text-center');
							 $('#email-modal .modal-body').html('<h1>Invites sent!</h1>');
							 $('#email-modal .modal-footer').html('<button data-dismiss="modal" type="button" class="btn btn-success">Okay</button>');
						 });
						 
						request.fail(function( jqXHR, textStatus ) {
							console.log(jqXHR);
							 $('#email-error').hide();
							alert( "Request failed: " + textStatus );
						});
					}
				}
				else {
				
				}
			});
			
			
		});
		
		$('.nav-tabs li a').on('click', function (e) {
		  e.preventDefault();
		  $(this).tab('show');
		  //console.log('show');
		});
		
		$('.nav-tabs li a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
			//e.target // newly activated tab
			//e.relatedTarget // previous active tab
			//console.log($(e.target).data('tab'));
			if ($(e.target).data('tab') == 'mentions') {
				$('#chat-mentions-extended').html('');
				$('#chat-messages-extended').hide();
				$('#chat-mentions-extended').show();
				$('#chat-messages-extended li').each(function(key, value){
					if ( $(value).data('type') == 'message' ) {
						if ( $(value).find("p").text().indexOf('@'+userName) >= 0 ) {
							$(value).clone().appendTo('#chat-mentions-extended');
						}
					}
				});
				
				$('#chat-mentions-extended li').each(function(){
					$(this).find('.favorite-container').remove();
					$(this).find('.kick-icon').remove();
				});
				
				$('#chat-mentions-extended li .reply-msg').on('click', function(e){
					var recepient = $(this).data('username');
					var subject   = '@'+recepient+' ';
					var chat 	  = $('#chat-input');
					var charCount = $('#char-count-chat');
					
					chat.val(subject);
					chat.focus();
					
					var msg 	  = chat.val();
					charCount.html(msg.length + "/500");
				});
							
				$(".chat-constrainer").scrollTop($("#chat-mentions-extended").height());
				
				//- Mentions Count
				$('#mentions-count').removeClass('bounceIn');
				$('#mentions-count').addClass('bounceOut');
				mentionsCount = 0;
			}
			else {
				$('#chat-mentions-extended').hide();
				$('#chat-messages-extended').show();
				$(".chat-constrainer").scrollTop($("#chat-messages-extended").height());
			}
		});
		
		$('#banner-prompt').on('click', function(e){
			e.preventDefault();
			var thisBanner = $(this).data('banner_type');
			if ( thisBanner == 'popup' ) {
				var bannerUrl 	= $(this).data('url');
				var regex 		= /(https?:\/\/\S+)/;
				var match 		= bannerUrl.match(regex);
				
				if (match) {
					window.open(bannerUrl, '_blank');
				}
				else {
					window.open('//'+bannerUrl, '_blank');
				}
			}
			else {
				var popUpHTML = '<div id="incoming-modal" tabindex="-1" role="dialog" aria-labelledby="incoming-modal-label">' +
								  '<div class="modal-dialog modal-sm" role="document">' +
								    '<div class="modal-content">' +
								      '<div class="modal-header">' +
								      	'<button style="position:static;" type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
								        '<h4 class="modal-title" id="myModalLabel">Enter Your Email</h4>' +
								      '</div>' +
								      '<div class="modal-body">' +
								      	'<form>' +
								      		'<div class="form-group">' +
									  			'<input placeholder="Email" type="text" class="form-control" id="mc-email">' +
									  		'</div>' +
									  	'</form>' +
								      '</div>' +
								      '<div class="modal-footer">' +
								        '<button id="mc-submit-btn" type="button" class="btn btn-success">Submit</button>' +
								      '</div>' +
								    '</div>' +
								  '</div>' +
								'</div>';
				$('#modal').html(popUpHTML);
				$('#modal').modal('show');
		
				$('#mc-submit-btn').attr('disabled','disabled');
				$('#mc-submit-btn').removeClass('btn-color');
				$('#mc-submit-btn').addClass('btn-default disabled');
				
				$('#mc-name').on('keyup', function(event){
					var name = $.trim($(this).val());
			        
			        if ( name != '' && name.length > 1 ) {
			        	$('#mc-name').css('border-color', '#468847');
				        nameValid = true;
			        }
			        else {
			        	$('#mc-name').css('border-color', 'red');
				        nameValid = false;
			        }
			        
			        if ( emailValid && nameValid ) {
						$('#mc-submit-btn').removeClass('btn-default disabled');
						$('#mc-submit-btn').addClass('btn-success');
						$('#mc-submit-btn').removeAttr('disabled');
					}
					else {
						$('#mc-submit-btn').addClass('btn-default disabled');
						$('#mc-submit-btn').removeClass('btn-success');
						$('#mc-submit-btn').attr('disabled','disabled');
					}
			    }).keypress(function(event){
					var regex = new RegExp("^[a-zA-Z. ]*$");
				    var key  = String.fromCharCode(!event.charCode ? event.which : event.charCode);
				    var code = ( !event.charCode ) ? event.which : event.charCode;
				    console.log(code);
			        if ( !regex.test(key) ) {
			        	//event.preventDefault();
			        	if ( code != 8 && code != 0 ) {
				        	return false;
			        	}
			        }
			    });
			    $('#mc-email, #mc-name').focus(function(e){
				    e.preventDefault();
			    });
			    
			    $('#mc-email').on('keyup', function(event){
			    	var emailAddress = $.trim($(this).val());
			    	if ( emailAddress != '' ) {
			    		var pattern = new RegExp(/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i);
			
						if ( pattern.test(emailAddress) ) {
							
							$('#mc-email').css('border-color', '#468847');
							emailValid = true;
						}
						else {
							$('#mc-email').css('border-color', 'red');
							emailValid = false;
						}
						
						if ( emailValid ) {
							$('#mc-submit-btn').removeClass('btn-default disabled');
							$('#mc-submit-btn').addClass('btn-success');
							$('#mc-submit-btn').removeAttr('disabled');
						}
						else {
							$('#mc-submit-btn').addClass('btn-default disabled');
							$('#mc-submit-btn').removeClass('btn-success');
							$('#mc-submit-btn').attr('disabled','disabled');
						}
					}
					else {
						$('#mc-email').css('border-color', 'red');
						emailValid = false;
						$('#mc-submit-btn').addClass('btn-default disabled');
						$('#mc-submit-btn').removeClass('btn-success');
						$('#mc-submit-btn').attr('disabled','disabled');
					}
			    });
			    
			    $('#mc-submit-btn').on('click', function(e){
			    	e.preventDefault();
			    	$('#mc-submit-btn').removeClass('btn-success');
					$('#mc-submit-btn').addClass('btn-default disabled');
					$('#mc-submit-btn').attr('disabled','disabled');
				
			    	//var name = $.trim($('#mc-name').val());
			    	var email= $.trim($('#mc-email').val());
			    	//show_invites();
			    	
			    	console.log(room);
					var request = $.ajax({
				         type: 'POST',
				         dataType: 'json',
				         url: baseurl+"room-ajax.php",
				         data: { 
				             'type'  	: 'mailchimp_subscribe',
				             'room'		: roomSlug,
				             'email'    : email
				         }
				     });
					 
					 request.done(function( msg ) {
					 	console.log(msg);
					 	if (msg.status == 'subscribed') {
						 	$('#modal').modal('hide');
					 	}
					 	else {
						 	alert(msg.title);
					 	}
				 		/*
			if ( !msg.data.response.success.error ) {
					        var href = $('#to-checkout').prop('href') + '?token=' + msg.data.response.gb_token;
					        $('#to-checkout').prop('href', href);
			     			show_invites();
			 			}
			 			else {
			 				alert("Email already added on our list, please try again.");
			 			}
			*/
					 });
					 
					request.fail(function( jqXHR, textStatus ) {
						console.log(jqXHR);
						alert( "Request failed: " + textStatus );
					});
			    });
			}
		});
	
		var nameValid = emailValid = false;
		
		$('.tt-uname').on('click', function(e){
			e.preventDefault();
			window.open($(this).data('tt_prof'), '_blank');
		});
		
		$('.tt-title').on('click', function(e){
			e.preventDefault();
			window.open($(this).data('tt_prof'), '_blank');
		});
	}
	
	function init_event () {
		guestClickEvent();
		hostClickEvent();
		hostMouseEvent();
		joinClickEvent();
		lockClickEvent();
		kickClickEvent();
	}
	
	function hostClickEvent() {
		$('#caller-0-wrapper').on('click', function(e){
			e.preventDefault();
			if ( $(this).data('id') != userId ) {
				trainerFavCount++;
				
				var updateRef = new Firebase(fireBaseURL+"data/room/"+room);
				
				updateRef.child('trainer_fav').push().set({
					"connection_id" : session.connection.connectionId,
					"user_name"		: userName,
					"thumb"			: profilePic
				});
								
				updateRef.update({
					"TrainerFavorite" : trainerFavCount
				});
			}
			//$('#caller-0-wrapper .feel-counter').text(trainerFavCount);
		});
	}
	
	function guestClickEvent() {
		$('#caller-1-wrapper').on('click', function(e){
			e.preventDefault();
			if ( $(this).data('id') != userId ) {
				guestFavCount++;
				
				var updateRef = new Firebase(fireBaseURL+"data/room/"+room);
				
				updateRef.child('guest_fav').push().set({
					"connection_id" : session.connection.connectionId,
					"user_name"		: userName,
					"thumb"			: profilePic
				});
				
				updateRef.update({
					"GuestFavorite" : guestFavCount
				});
			}
			//$('#caller-1-wrapper .feel-counter').text(guestFavCount);
		});
	}
	
	function hostMouseEvent() {
		$('#caller-1-wrapper').on('mouseenter', function(e){
			e.preventDefault();
			if (userType == 'Trainer') {
				if (!$(this).hasClass('is_calling')) {
					$(this).find('#kick-guest').fadeIn('fast');
					$(this).find('#kick-guest').removeClass('animated bounceOut');
					$(this).find('#kick-guest').addClass('animated bounceIn');
				}
			}
		});
		
		$('#caller-1-wrapper').on('mouseleave', function(e){
			e.preventDefault();
			if (userType == 'Trainer') {
				
				if (!$(this).hasClass('is_calling')) {
					$(this).find('#kick-guest').removeClass('animated bounceIn');
					$(this).find('#kick-guest').addClass('animated bounceOut');
					$(this).find('#kick-guest').fadeOut('fast');
				}
				else {
				}
			}
		});
	}
	
	function kickClickEvent() {
		$('.call-out').on('click', function(e){
			e.stopPropagation();
			e.preventDefault();
			var connId    = $(this).data('id');
			var popUpHTML = '<div id="incoming-modal" tabindex="-1" role="dialog" aria-labelledby="incoming-modal-label">' +
							  '<div class="modal-dialog modal-sm" role="document">' +
							    '<div class="modal-content">' +
							      '<div class="modal-header">' +
							        '<h4 class="modal-title" id="myModalLabel">Message</h4>' +
							      '</div>' +
							      '<div class="modal-body">' +
							      	'<p>Are you sure you want to kick your Guest?</p>' +
							      '</div>' +
							      '<div class="modal-footer">' +
							        '<button id="kick-guest" data-id="'+connId+'" type="button" class="btn btn-success">Yes</button>' +
							        '<button type="button" class="btn btn-danger" data-dismiss="modal">No</button>' +
							      '</div>' +
							    '</div>' +
							  '</div>' +
							'</div>';
			$('#modal').html(popUpHTML);
			$('#modal').modal('show');
			
			$('#kick-guest').on('click', function(e){
				e.preventDefault();
				session.signal(
			    {
			      data: 'kick|mbox|'+$(this).data('id'),
			      type: "kick_user"
			    },
				    function(error,data) {
				      if (error) {
				        console.log("signal error ("
				                     + error.code
				                     + "): " + error.message);
				      } else {
				      	console.log(data);
					  	$('#modal').modal('hide');
				        //createMessage(userName, msg);
				      }
				    }
			    );
			});
		});
	}
	
	function lockClickEvent () {
		$('.lock-seat').on('click', function(e){
			e.preventDefault();
			
			var allow = $(this).find('span').data('lock');
			var lock_text = (parseInt(allow)) ? 'Unlocking' : 'Locking';
			
			var popUpHTML = '<div id="incoming-modal" tabindex="-1" data-backdrop="static" data-keyboard="false" role="dialog" aria-labelledby="incoming-modal-label">' +
									  '<div class="modal-dialog modal-sm" role="document">' +
									    '<div class="modal-content">' +
									      '<div class="modal-header">' +
									        '<h4 class="modal-title" id="myModalLabel">Message</h4>' +
									      '</div>' +
									      '<div class="modal-body">' +
									      	'<p>'+lock_text+' seat.. Please wait...</p>' +
									      '</div>' +
									      '<div class="modal-footer">' +
									      '</div>' +
									    '</div>' +
									  '</div>' +
									'</div>';
					$('#modal').html(popUpHTML);
					$('#modal').modal('show');
			
			
			var request = $.ajax({
		         type: 'POST',
		         url: baseurl+"room-ajax.php",
		         data: { 
		             'type'  	: 'allow_guest',
		             'room'		: roomSlug,
		             'allow'    : allow
		         }
		     });
		     
			 request.done(function( msg ) {
			 	if (msg) {
				 	session.signal(
				    {
				      data: allow,
				      type: "allow_guest"
				    },
					    function(error,data) {
					      if (error) {
					        console.log("signal error ("
					                     + error.code
					                     + "): " + error.message);
					      } else {
					      	$('#modal').modal('hide');
					      	if (data.data == '1') {
					      		$('#lock-btn').html('Lock Seat <span data-lock="0" class="lock-icon inline-icon"></span>');
					      		$('#lock-btn').css('background-color', 'red');
					      	}
					      	else {
					      		$('#lock-btn').html('Unlock Seat <span data-lock="1" class="unlock-icon inline-icon"></span>');
					      		$('#lock-btn').css('background-color', '#27ae60');
					      	}
					      }
					    }
				    );
			 	}
			 });
			 
			request.fail(function( jqXHR, textStatus ) {
				console.log(jqXHR);
				alert( "Request failed: " + textStatus );
			});
			
		});
	}

	function end_room () {
		//console.log(userType);
		if (userType == 'Client') {
			var popMsg = 'Are you sure you want to leave this cast?';
		}
		else {
			var popMsg = 'Are you sure you want to end this cast?';
		}
		var popUpHTML = '<div id="incoming-modal" tabindex="-1" role="dialog" aria-labelledby="incoming-modal-label">' +
							  '<div class="modal-dialog modal-sm" role="document">' +
							    '<div class="modal-content">' +
							      '<div class="modal-header">' +
							      	'<button style="position:static;" type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
							        '<h4 class="modal-title text-center" id="myModalLabel">Message</h4>' +
							      '</div>' +
							      '<div class="modal-body">' +
							      	'<p>'+popMsg+'</p>' +
							      '</div>' +
							      '<div class="modal-footer">' +
							        '<button type="button" class="btn btn-danger" data-dismiss="modal">No</button>' +
							        '<button id="leave-room" type="button" class="btn btn-success">Yes</button>' +
							      '</div>' +
							    '</div>' +
							  '</div>' +
							'</div>';
		$('#modal').html(popUpHTML);
		$('#modal').modal('show');
		
		$('#leave-room').on('click', function(e){
			e.preventDefault();
			
			if (userType == 'Client') {
				$('#loading-screen h1').text('Leaving cast...');
				$('#loading-screen').fadeIn();
				disconnect();
			}
			else {
				var data = {
					type	: 'end_room',
					room	: roomSlug
				};
				$.ajax({
		            type: "POST",
		            url: baseurl+"room-ajax.php",
		            data: data,
		            success: function(data) {
		            	$('#modal').modal('hide');
		                if (data) {
							$('#loading-screen h1').text('Ending cast...');
							$('#loading-screen').fadeIn();
							disconnect();
			                //window.location.href = baseurl+"room.php";
		                }
		            }
		        });
			}
		});
	}
	
	function readImage(file) {
  
	    var reader = new FileReader();
	    var image  = new Image();
	  
	    reader.readAsDataURL(file);  
	    reader.onload = function(_file) {
	        image.src    = _file.target.result;              // url.createObjectURL(file);
	        image.onload = function() {
	            var w = this.width,
	                h = this.height,
	                t = file.type,                           // ext only: // file.type.split('/')[1],
	                n = file.name,
	                s = ~~(file.size/1024) +'KB';
	                
	            if (w > 350 || h > 50) {
	            	if ( w > 350 ) {
	            		$('#image-error-modal .modal-body p').html('Your image needs to be 300x50 - use <a href="//canva.com" target="_blank">Canva.com</a> to create one quickly!');
	            	}
	            	else if ( h > 50 ) {
	            		$('#image-error-modal .modal-body p').html('Your image needs to be 300x50 - use <a href="//canva.com" target="_blank">Canva.com</a> to create one quickly!');
	            	}
	            	$('#modal').css('z-index', '1040');
		            $('#image-error-modal').modal('show');
					$('.fileinput').fileinput('clear');
	            }
	            else {
		            if ( s > 200000 ) {
			            $('#image-error-modal .modal-body p').html('Sorry, your file is too large. it should not exceed 200KB.');
						$('#modal').css('z-index', '1040');
						$('#image-error-modal').modal('show');
						$('.fileinput').fileinput('clear');
		            }
		            else if (t.split('/')[1] != 'jpg' && t.split('/')[1] != 'jpeg') {
			            $('#image-error-modal .modal-body p').html('Sorry, only JPG or JPEG.');
						$('#modal').css('z-index', '1040');
						$('#image-error-modal').modal('show');
						$('.fileinput').fileinput('clear');
		            }
		            else {
						$('#aw-banner-type').removeClass('disabled');
		            }
	            }
	            //$('#uploadPreview').append('<img src="'+ this.src +'"> '+w+'x'+h+' '+s+' '+t+' '+n+'<br>');
	        };
	        image.onerror= function() {
	            $('#image-error-modal .modal-body p').text('Your image needs to be 300x500 - use <a href="//canva.com" target="_blank">Canva.com</a> to create one quickly!');
	        	$('#image-error-modal').modal('show');
				$('.fileinput').fileinput('clear');
				if ( !$('#aw-banner-type').hasClass('disabled') ) $('#aw-banner-type').addClass('disabled');
	            //alert('Invalid file type: '+ file.type);
	        };      
	    };
	}
});
	    var connectionCount = 0;
	    var roomCount   	= 0;
	    var defaultCount	= null;
		var trainerFavCount = 0;
		var guestFavCount 	= 0;
		var totalViewCount	= 0;
		var recordCount     = 60;
		var recordInt;
		
		function joinClickEvent () {
			$('.call-in').on('click', function(e){
				e.preventDefault();
				var popUpHTML = '<div id="incoming-modal" tabindex="-1" data-backdrop="static" data-keyboard="false" role="dialog" aria-labelledby="incoming-modal-label">' +
										  '<div class="modal-dialog modal-sm" role="document">' +
										    '<div class="modal-content">' +
										      '<div class="modal-header">' +
										        '<h4 class="modal-title" id="myModalLabel">Message</h4>' +
										      '</div>' +
										      '<div class="modal-body">' +
										      	'<p>Joining Stream.. Please wait...</p>' +
										      '</div>' +
										      '<div class="modal-footer">' +
										      '</div>' +
										    '</div>' +
										  '</div>' +
										'</div>';
						//$('#modal').html(popUpHTML);
						//$('#modal').modal('show');
				var userDataJSON = $.parseJSON(userData);
				session.signal(
			    {
			    	//to: $('#stream-caller-0').data('id'),
			      data: {"text" : "join", "user" : userDataJSON, "host" : $('#stream-caller-0').data('id'), "requesting" : session.connection.connectionId},
			      type: "joinMessage"
			    },
				    function(error,data) {
				      if (error) {
				        console.log("signal error ("
				                     + error.code
				                     + "): " + error.message);
				      } else {
				      	console.log(data);
				        //createMessage(userName, msg);
				      }
				    }
			    );
			    var mobileConnId = $('#stream-caller-0').data('id');
			    session.signal(
			    {
			      data: ""+userDataJSON.name+"|"+session.connection.connectionId+"|"+mobileConnId.connectionId,
			      type: "allow_user_join"
			    },
				    function(error,data) {
				      if (error) {
				        console.log("signal error ("
				                     + error.code
				                     + "): " + error.message);
				      } else {
				      		
				        //createMessage(userName, msg);
				      }
				    }
			    );
			});
		}
		
		function Mute (subscriber, button) {
			var volume = 100;
			
			if($(button).hasClass('mute')) {
				$(button).removeClass('mute');
			}
			else {
				$(button).addClass('mute');
				volume = 0;
			}
			
			subscriber.setAudioVolume(volume);
		}
		
		//Code to stop publishing to a session
	    function disconnect() {
	        session.disconnect();
	    }
	    
	    function connectPublishers () {
			$('#loading-screen').fadeOut();
        	var myFirebaseRef = new Firebase(fireBaseURL+"data/room/"+sessionId);
        	var online   = myFirebaseRef.child("online");
        	var userData = session.connection.data.split("|");
        	console.log(userData);
        	if ( userData[0].toLowerCase() != 'client' ) {
	            var publisher = OT.initPublisher(userData[0].toLowerCase(), {
	                insertMode: 'append',
	                width:      '100%',
	                height:     '100%',
	                name:       userData[1],
	                /*
frameRate: 	'7',
	                resolution:	'320x240'
*/
	            });
	            session.publish(publisher);
	            
	            if ( userData[0].toLowerCase() == 'trainer' ) {
					clearInterval(recordInt);
		            var recordInt = setInterval(function(){
		            	if (recordCount > 0) {
			            	$('#record-room').html(recordCount);
		            	}
		            	else {
							clearInterval(recordInt);
		            		$('#record-room').removeClass('disabled');
			            	$('#record-room').html('<span class="blip not-recording"></span>Record');
			            	$('#record-room').on('click', function(e){
			            		e.preventDefault();
			            		if ( !$('#record-room span').hasClass('recording') ) {
					            	var data = {
										type		: 'start_archive',
										user_id  	: $(this).data('trainer_id'),
										slug		: roomSlug
									};
									
				                	$.ajax({
							            type: "POST",
							            url: baseurl+"room-ajax.php",
							            data: data,
							            success: function(data) {
							                $('#record-room').html('<span class="blip recording"></span>Recording');
							            }
							        });
			            		}
			            	});
		            	}
		            	recordCount--;
		            }, 1000);
	            }
            }
            //console.log(session.connection.connectionId);
            //console.log($.parseJSON(session.connection.data).name);
        	online.on("value", function(snapshot) {
        		//console.log(snapshot.val());
        		if ( snapshot.val() === null ) {
            		online.push().set({
						"connection_id" : session.connection.connectionId,
						"name"			: userData[1],
						"thumb"			: userData[2],
						"user_id"		: userData[3],
						"user_type"		: userData[0]
					});
        		}
        		else {
            		var uExist = false;
					$.each(snapshot.val(), function(key,val){
						//console.log(val.user_id + ' == ' + userData[0]);
						if (val.user_id == userData[3]) {
							uExist = true;
						}
					});
					if ( !uExist ) {
						online.push().set({
							"connection_id" : session.connection.connectionId,
							"name"			: userData[1],
							"thumb"			: userData[2],
							"user_id"		: userData[3],
							"user_type"		: userData[0]
						});
					}
        		}
        		
			}, function (errorObject) {
			    console.log("The read failed: " + errorObject.code);
			});
			
			var videoId = 0;
			var twitterData;
			var twitterName = '';
			
			if ( userData[0].toLowerCase() == "trainer" ) {
				if ( userData[10] == 'mobile' ) {
					twitterName = userData[9];
				}
				else {
					twitterData = $.parseJSON( userData[9]);
					twitterName = twitterData.screen_name;
				}
			}
			else {
				if ( userData[6] == 'mobile' ) {
					twitterName = userData[5];
				}
				else {
					twitterData = $.parseJSON( userData[5]);
					twitterName = twitterData.screen_name;
				}
			}
			
			if ( userData[0].toLowerCase() == "guest" ) {
				videoId = 1;
			}
			
			
        
			//console.log(userData);
			if ( userData[0].toLowerCase() != "client" ) {
				
				$('#sidebar-profile-photo').prop('src',userData[2]);
				$('#caller-'+videoId+' .tt-title').text(' '+twitterName);
				$('#caller-'+videoId+' .tt-title').data('tt_prof','http://www.twitter.com/'+twitterName);
				$('#caller-'+videoId+' .tt-uname').text('@'+twitterName);
				$('#caller-'+videoId+' .tt-uname').data('tt_prof','http://www.twitter.com/'+twitterName);
				$('#caller-'+videoId+' .image').html('<img src="'+userData[2]+'">');
				$('#stream-waiting-'+videoId+' .caller-avatar').css('background-image','url('+userData[2]+')');
				$('#caller-'+videoId+'-wrapper').data('id',userData[3]);
				$('#caller-'+videoId+'-wrapper').data('uname',twitterName);
			}
			/*
$('#caller-'+videoId+'-wrapper .mute-audio').on('click', function(e){
	        	e.stopPropagation();
	        	e.preventDefault();
	        	console.log('MUTE!');
	        	Mute(publisher, $(this));
	        });
*/
			
			var fireBaseMsg = new Firebase(fireBaseURL+"data/messages/"+room);
			fireBaseMsg.push().set({
				"room_id"   : room,
				"name"      : twitterName,
				"message"   : "Joined the conversation",
				"user_fav"  : {},
				"pic"		: userData[2],
				"type"		: userData[0]
			});
	    }
		
	    function initializeSession(token) {
	    	$(window).on("beforeunload", function() { 
			    disconnect();
			});
	
	    	var roomAdded = new Firebase(fireBaseURL+"data/room");
	        roomAdded.once("value", function(snapshot) {
	        	if (snapshot.val()) {
					defaultCount = Object.keys(snapshot.val()).length;
			  	}
			  //console.log("initial data loaded!", Object.keys(snapshot.val()).length === roomCount);
			});
			
			//- Get room fields
			var roomValue = new Firebase(fireBaseURL+"data/room/"+room);
	        roomValue.once("value", function(snapshot) {
			  var newPost = snapshot.val();
			  if (newPost) {
				  trainerFavCount 	= (newPost.TrainerFavorite) ? newPost.TrainerFavorite : 0;
				  guestFavCount 	= (newPost.GuestFavorite) ? newPost.GuestFavorite : 0;
				  totalViewCount	= (newPost.views) ? newPost.views : 0;
				  console.log(newPost.views);
				  
				  $('#caller-0-wrapper .feel-counter').text(trainerFavCount);
				  $('#caller-1-wrapper .feel-counter').text(guestFavCount);
				  $('#total-viewers-count').text(totalViewCount);
			  }
			});
			
		    roomValue.child('trainer_fav').orderByKey().limitToLast(1).on("child_added", function(snapshot, prevChildKey) {
				var newPost = snapshot.val();
				
				$('#caller-0 .caller-feels').append('<img style="width:30px;height:30px;border-radius:50%;position:absolute;right:0;" class="animated zoomIn user-profile-image" src="'+newPost.thumb+'">');
				$('#caller-0 .caller-feels .user-profile-image').one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function(e){
					$(this).fadeOut('fast',function(){
						$(this).remove();
					});
				});
			});
			
			roomValue.on("child_changed", function(snapshot, prevChildKey) {
			  	var newPost = snapshot.val();
			  	
			  	if (newPost) {
				  	switch ( snapshot.key() ) {
					  	case "TrainerFavorite":
					  		trainerFavCount = newPost;
					  		$('#caller-0-wrapper .feel-counter').text(trainerFavCount);
					  	break;
					  	
					  	case "GuestFavorite":
					  		guestFavCount = newPost;
					  		$('#caller-1-wrapper .feel-counter').text(guestFavCount);
					  	break;
					  	
					  	case "views":
					  		totalViewCount = newPost;
					  		//console.log(newPost);
					  		$('#total-viewers-count').text(totalViewCount);
					  	break;
				  	}
			  	}
			});
		
		    // Initialize Session Object
		    session = OT.initSession(apiKey, sessionId);
		
		    // Subscribe to a newly created stream
		
		    session.on('streamCreated', function(event) {
		    	var videoId  = 0;
                var userData = event.stream.connection.data.split('|');//$.parseJSON(event.stream.connection.data);
		        var subscriber = session.subscribe(event.stream, userData[0].toLowerCase(), {
		            insertMode: 'append',
		            width:      '100%',
		            height:     '100%'
		        });
		        /*
console.log(event.stream.connection);
		        //console.log(userData);
		        console.log(event);
*/
		        //var userData = subscriber.stream.connection.data.split("|");
		        
		        if ( userData[0].toLowerCase() == "guest" ) {
			        videoId = 1;
		        }
		        else if ( userData[0].toLowerCase() == "trainer" ) {
					$('#stream-caller-'+videoId).data('id',event.stream.connection.connectionId);
					var clientData = session.connection.data.split('|');
					var clientTwitterName = '';
					var clientTwitterId	  = '';
					var clientTwitterData;
					if ( clientData[6] == 'mobile' ) {
						clientTwitterName = clientData[5];
					}
					else {
						clientTwitterData = $.parseJSON( clientData[5]);
						clientTwitterName = clientTwitterData.screen_name;
						clientTwitterId   = clientTwitterData.id;
					}
					$('#stream-caller-0').data('id',event.stream.connection);
					session.signal(
				    {
				    	to: event.stream.connection,
				      data: {"text" : "listJoin", "to" : event.stream.connection.connectionId, "name": clientTwitterName, "userId" : clientTwitterId},
				      type: "listJoin"
				    },
					    function(error,data) {
					      if (error) {
					        console.log("signal error ("
					                     + error.code
					                     + "): " + error.message);
					      } else {
					      	//console.log(data);
					        //createMessage(userName, msg);
					      }
					    }
				    );
		        }
		        
		        var twitterData;
		        var twitterName = '';
				
				if ( userData[0].toLowerCase() == "trainer" ) {
					if ( userData[10] == 'mobile' ) {
						twitterName = userData[9];
					}
					else {
						twitterData = $.parseJSON( userData[9]);
						twitterName = twitterData.screen_name;
					}
				}
				else {
					if ( userData[6] == 'mobile' ) {
						twitterName = userData[5];
					}
					else {
						twitterData = $.parseJSON( userData[5]);
						twitterName = twitterData.screen_name;
					}
				}
		        
				$('#caller-'+videoId+' .tt-title').text(' '+userData[1]);
				$('#caller-'+videoId+' .tt-title').data('tt_prof','http://www.twitter.com/'+twitterName);
				$('#caller-'+videoId+' .tt-uname').text('@'+twitterName);
				$('#caller-'+videoId+' .tt-uname').data('tt_prof','http://www.twitter.com/'+twitterName);
				$('#caller-'+videoId+' .image').html('<img src="'+userData[2]+'">');
				$('#stream-waiting-'+videoId+' .caller-avatar').css('background-image','url('+userData[2]+')');
				$('#caller-'+videoId+'-wrapper').data('id',userData[3]);
				$('#caller-'+videoId+'-wrapper').data('uname',twitterName);
		
		        SpeakerDetection(subscriber, function() {
		          //console.log('started talking');
		        }, function() {
		          //console.log('stopped talking');
		        });
		        
		        $('#caller-'+videoId+'-wrapper .mute-audio').on('click', function(e){
		        	e.stopPropagation();
		        	e.preventDefault();
		        	Mute(subscriber, $(this).find('span'));
		        });
				
		    }); 
		   
		    session.on({
		        connectionCreated: function (event) {
		            connectionCount++;
		            var viewCount = ( connectionCount < 1 ) ? 0 : connectionCount - 1;
		            $('#viewer-count').text(viewCount);
		            //console.log(connectionCount + ' connections.');
		            var userData = event.connection.data.split('|');
		            
		            if (userData[0].toLowerCase() != 'trainer') {
			            var data = {
							type		: 'add_attendees',
							slug		: roomSlug,
							user_id		: userData[3]
						};
						
	                	$.ajax({
				            type: "POST",
				            url: baseurl+"room-ajax.php",
				            data: data,
				            success: function(data) {
				                console.log(data);
				            }
				        });
		            }
		            
		            if (userData[0].toLowerCase() == 'trainer') {
	                	var data = {
							type		: 'set_room_active',
							room		: room
						};
						
	                	$.ajax({
				            type: "POST",
				            url: baseurl+"room-ajax.php",
				            data: data,
				            success: function(data) {
				                //console.log(data);
				            }
				        });
		            }
		            else if ( userData[0].toLowerCase() == 'client' ) {
		            	//totalViewCount++;
		            	//console.log(totalViewCount);
						var totalViewRef = new Firebase(fireBaseURL+"data/room/"+room);
						
						totalViewRef.once("value", function(snapshot) {
						  var newPost = snapshot.val();
						  if (newPost) {
							totalViewCount = (newPost.views) ? newPost.views : 0;
							totalViewCount++;
							totalViewRef.update({
								"views" : totalViewCount
							});
						  }
						});
						
						var twitterData = $.parseJSON(userData[5]);
						
						//console.log(twitterData.name);
						//$('#total-viewers-count').text(totalViewCount);
						var bubbleAvatar = '<li class="viewer animated bounceIn" id="viewer-'+userData[3]+'" data-info="" data-hasqtip="'+userData[3]+'">' +
		            					   '<img style="border-color:;" class="user-profile-image" src="'+userData[2]+'"></li>';
		            	$('#user-list').append(bubbleAvatar);
		            	
		            	if (userId != userData[3]) {
		            		var is_following = (twitterData.following) ? '<p class="follows-you">Follows You</p>' : '<a class="button button-follow" data-user_id="'+userData[3]+'"></a>';
		            	}
		            	else {
			            	var is_following = '';
		            	}
		            	
		            	var qtipContent = '<div class="profile-card profile-card-data" data-user_id="'+userData[3]+'">' +
										    '<div class="user-card-qtip container">' +
												'<div class="user-card-header" id="user-info">' +
													'<div class="profile-pic" style="background-image: url('+userData[2]+');"></div>' +
													'<div class="profile-info">' +
														'<h4 class="blank-profile" data-username="'+twitterData.screen_name+'">'+twitterData.screen_name+'</h4>' +
														'<p class="blank-profile username" data-username="'+twitterData.screen_name+'">' +
															'@'+twitterData.screen_name+
														'</p>' +
													'</div>' +
												'</div>' +
												'<p class="bio">'+twitterData.name+'</p>' +
												'<div class="social-links">' +
													'<a class="twitter-social social-link" href="http://www.twitter.com/'+twitterData.screen_name+'" target="_blank"></a>' +
												'</div>' +
												'<div class="activity" data-user_id="'+userData[1]+'">' +
													'<div class="action">' +
														'<div class="follow-container">' +
															is_following +
														'</div>' +
													'</div>' +
												'<div class="stats">' +
													'<ul>' +
														'<li>' +
															'<a href="http://www.twitter.com/'+twitterData.screen_name+'" target="_blank">'+
																'<span class="count">'+twitterData.followers_count+'</span>' +
																'Followers'+
															'</a>' +
														'</li>' +
														'<li>' +
															'<a href="http://www.twitter.com/'+twitterData.screen_name+'" target="_blank">' +
																'<span class="count">'+twitterData.friends_count+'</span>' +
																'Following'+
															'</a>' +
														'</li>' +
													'</ul>' +
												'</div>' +
											'</div>';
		            	
		            	$('#viewer-'+userData[3]).qtip({ // Grab some elements to apply the tooltip to
						    content: {
						        text: qtipContent
						    },
						    position: {
						        my: 'top center',  // Position my top left...
						        at: 'bottom center'
						    },
						    show: {
						        solo: true
						    },
						    hide: {
						        fixed: true
						    }
						});
		            	
		            }
		            else if ( userData[0].toLowerCase() == 'guest' ) {
						$('#placeholder-wrapper-cell').hide();
						$('#caller-1-wrapper').show();
						$('#caller-1-wrapper .call-out').data('id',event.connection.connectionId);
					}
		            	
		            	
		        },
		        connectionDestroyed: function (event) {
                	var userData = event.connection.data.split('|');//$.parseJSON(event.connection.data);
                	var data = {
						type		: 'leave_room',
						user_id  	: userData[3],
						room		: roomSlug
					};
					
					//console.log(data);
					if ( userData[0].toLowerCase() != 'client' ) {
						
						if ( userData[0].toLowerCase() == 'guest' ) {
							$('#caller-1-wrapper').hide();
							$('#placeholder-wrapper-cell').show();
						}
					
	                	/*
$.ajax({
				            type: "POST",
				            url: baseurl+"room-ajax.php",
				            data: data,
				            success: function(data) {
				                console.log(data);
				                if (data) {
									$('#loading-screen h1').text('Disconnected from stream...');
				                	$('#loading-screen').fadeIn();
									window.location.href = baseurl;
				                }
				            }
				        });
*/
				        
				        
						$('#caller-1-wrapper').hide();
						$('#incoming-guest').hide();
						$('#placeholder-wrapper-cell').show();
			        }
			        
			        $('#viewer-'+userData[3]).remove();
                	
		            connectionCount--;
		            var viewCount = ( connectionCount < 1 ) ? 0 : connectionCount - 1;
		            $('#viewer-count').text(viewCount);
		            //console.log(connectionCount + ' connections.');
		        },
		        sessionDisconnected: function sessionDisconnectHandler(event) {
		            // The event is defined by the SessionDisconnectEvent class
		            console.log('Disconnected from the session.');
		            //document.getElementById('disconnectBtn').style.display = 'none';
		            /*
if (event.reason == 'networkDisconnected') {
		                alert('Your network connection terminated.')
		            }
*/
                	/*
var data = {
						type		: 'leave_room',
						user_id  	: userId,
						room		: roomSlug,
						total_views : totalViewCount
					};
					
                	$.ajax({
			            type: "POST",
			            url: baseurl+"room-ajax.php",
			            data: data,
			            success: function(data) {
			                //console.log(data);
			                //if (data) {
								window.location.href= baseurl+"room.php";
			                //}
			            }
			        });
*/
		        }
		      });     
		
		
		    var SpeakerDetection = function(subscriber, startTalking, stopTalking) {
		      var activity = null;
		      subscriber.on('audioLevelUpdated', function(event) {
		        var now = Date.now();
		        if (event.audioLevel > 0.2) {
		          if (!activity) {
		            activity = {timestamp: now, talking: false};
		          } else if (activity.talking) {
		            activity.timestamp = now;
		          } else if (now- activity.timestamp > 1000) {
		            // detected audio activity for more than 1s
		            // for the first time.
		            activity.talking = true;
		            if (typeof(startTalking) === 'function') {
		              startTalking();
		            }
		          }
		        } else if (activity && now - activity.timestamp > 3000) {
		          // detected low audio activity for more than 3s
		          if (activity.talking) {
		            if (typeof(stopTalking) === 'function') {
		              stopTalking();
		            }
		          }
		          activity = null;
		        }
		      });
		    };
		
		    // Handler for sessionDisconnected event
		    session.on('sessionDisconnected', function(event) {
		        console.log('You were disconnected from the session.', event.reason);
		    });
		
		    // Connect to the Session
		    session.connect(token, function(error) {
		        // If the connection is successful, initialize a publisher and publish to the session
		        if (!error) {
		        	connectPublishers();
		        }
		        else {
		            console.log('There was an error connecting to the session:', error.code, error.message);
		        }
		
		    });
		    
		    session.on("signal:joinMessage", function(event) {
				$('#placeholder-wrapper-cell').hide();
				$('#caller-1-wrapper').addClass('is_calling animated bounceIn');
				$('#caller-1-wrapper .stream-caller-ui').hide();
				$('#caller-1-wrapper .stream-caller').hide();
		    	if (session.connection.connectionId == event.data.requesting) {
			    	$('#caller-1-wrapper .stream-waiting h1').text('Calling in');
					$('#incoming-guest').show();
					$('#incoming-host').hide();
					$('#incoming-client').show();
					$('#cancel-call').on('click', function(e){
						e.preventDefault();
						console.log(event);
						session.signal(
					    {
					    	to: $('#stream-caller-0').data('id'),
					      data: {"text" : "cancelCall", "userId" : userId, "connectionId" : event.from.connectionId},
					      type: "cancelCall"
					    },
						    function(error,data) {
						      if (error) {
						        console.log("signal error ("
						                     + error.code
						                     + "): " + error.message);
						      } else {
						      	//console.log(data);
						        //createMessage(userName, msg);
								$('#caller-1-wrapper').hide();
								$('#incoming-guest').hide();
								$('#placeholder-wrapper-cell').show();
						      }
						    }
					    );
					});
		    	}
		    	else {
					$('#caller-1-wrapper .stream-waiting h1').text(event.data.user.name+' is calling in');
		    	}
		    	
				$('#caller-1-wrapper').show();
				$('#caller-1-wrapper .stream-waiting h1').show();
				$('#accept-incoming').data('id', event.data.user.db_id);
				
		    	if (session.connection.connectionId == event.data.host.connectionId) {
					$('#incoming-guest').addClass('is_calling animated bounceIn');
					$('#incoming-guest').show();
					$('#incoming-client').hide();
					$('#incoming-host').show();
					$('#decline-incoming').on('click', function(e){
						e.preventDefault();
						session.signal(
					    {
					    	to: event.from,
					      data: {"text" : "decline", "userId" : userId, "connectionId" : event.from.connectionId, "user" : event.data.user},
					      type: "declineMessage"
					    },
						    function(error,data) {
						      if (error) {
						        console.log("signal error ("
						                     + error.code
						                     + "): " + error.message);
						      } else {
						      	//console.log(data);
						        //createMessage(userName, msg);
								$('#caller-1-wrapper').hide();
								$('#incoming-guest').hide();
								$('#placeholder-wrapper-cell').show();
						      }
						    }
					    );
					});
					
					$('#accept-incoming').on('click', function(e){
						e.preventDefault();
						var userId = $(this).data('id');
						/* console.log(event); */
						session.signal(
					    {
					    	to: event.from,
					      data: {"text" : "accept", "userId" : userId, "connectionId" : event.from.connectionId, "user" : event.data.user},
					      type: "acceptMessage"
					    },
						    function(error,data) {
						      if (error) {
						        console.log("signal error ("
						                     + error.code
						                     + "): " + error.message);
						      } else {
						      	//console.log(data);
								//$('#caller-1-wrapper .stream-waiting h1').text('waiting for '+event.data.user.name+' to connect.');
								$('#caller-1-wrapper .stream-waiting h1').hide();
								$('#caller-1-wrapper .stream-caller-ui').show();
								$('#caller-1-wrapper .stream-caller').show();
								//$('#caller-1-wrapper .stream-waiting').hide();
								$('#incoming-guest').hide();
						        //createMessage(userName, msg);
						        
								
								$('#caller-1 .tt-title').text(' '+event.data.user.name);
								$('#caller-1 .tt-title').data('tt_prof','http://www.twitter.com/'+event.data.user.name);
								$('#caller-1 .tt-uname').text('@'+event.data.user.name);
								$('#caller-1 .tt-uname').data('tt_prof','http://www.twitter.com/'+event.data.user.name);
								$('#caller-1 .image').html('<img src="'+event.data.user.pic+'">');
								$('#stream-waiting-1 .caller-avatar').css('background-image','url('+event.data.user.pic+')');
						      }
						    }
					    );
					});
				}
				
				$('#caller-1-wrapper .stream-waiting .caller-avatar').css('background-image' ,'url('+event.data.user.pic+')');
				$('#caller-1-wrapper .stream-waiting .caller-avatar').css('background-repeat' ,'no-repeat');
				/*
var popUpHTML = '<div id="incoming-modal" tabindex="-1" role="dialog" aria-labelledby="incoming-modal-label">' +
								  '<div class="modal-dialog modal-sm" role="document">' +
								    '<div class="modal-content">' +
								      '<div class="modal-header">' +
								        '<h4 class="modal-title" id="myModalLabel">Message</h4>' +
								      '</div>' +
								      '<div class="modal-body">' +
								      	'<p>'+event.data.user.name+' wants to join.</p>' +
								      '</div>' +
								      '<div class="modal-footer">' +
								        '<button id="accept-incoming" data-id="'+event.data.user.db_id+'" type="button" class="btn btn-success">Accept</button>' +
								        '<button id="decline-incoming" type="button" class="btn btn-danger">Decline</button>' +
								      '</div>' +
								    '</div>' +
								  '</div>' +
								'</div>';
				$('#modal').html(popUpHTML);
				$('#modal').modal('show');
				
				
				
			  //console.log("Signal sent from connection " + event.from.id);
			  // Process the event.data property, if there is any data.
			  });
*/
			});
			
			session.on("signal:acceptMessage", function(event) {
				
				if ( event.data.connectionId == session.connection.connectionId ) {
					//$('#loading-screen h1').text('Joining stream...');
					//$('#loading-screen').fadeIn();
					var data = {
						type		: 'update_room_guest',
						session_id  : room,
						room		: roomSlug,
						guest    	: event.data.user
					};
					//console.log(event);
			    	$.ajax({
			            type: "POST",
			            url: baseurl+"room-ajax.php",
			            data: data,
			            success: function(data) {
			            	//console.log(data);
			                if (data) {
								disconnect();
								session.connect(data, function(error) {
									if (!error) {
							        	connectPublishers();
							        }
							        else {
							            console.log('There was an error connecting to the session:', error.code, error.message);
							        }
								});
			                	/*
var pubOptions = {
			                		publishAudio:true,
			                		publishVideo:true,
			                		insertMode: 'append',
						            width:      '100%',
						            height:     '100%'
			                	};
								// Replace replacementElementId with the ID of the DOM element to replace:
								var publisher = OT.initPublisher('guest', pubOptions);
					            session.publish(publisher);
				                //window.location.href = baseurl+roomSlug;
				                $('#caller-1-wrapper .stream-waiting h1').hide();
								$('#caller-1-wrapper .stream-caller-ui').show();
								$('#caller-1-wrapper .stream-caller').show();
								$('#incoming-guest').hide();
				                $('#sidebar-profile-photo').prop('src',event.data.user.pic);
								$('#caller-1 .tt-title').text(' '+event.data.user.twitter_data.name);
								$('#caller-1 .tt-title').data('tt_prof','http://www.twitter.com/'+event.data.user.twitter_data.screen_name);
								$('#caller-1 .tt-uname').text('@'+event.data.user.twitter_data.screen_name);
								$('#caller-1 .tt-uname').data('tt_prof','http://www.twitter.com/'+event.data.user.twitter_data.screen_name);
								$('#caller-1 .image').html('<img src="'+event.data.user.pic+'">');
								$('#stream-waiting-1 .caller-avatar').css('background-image','url('+event.data.user.pic+')');
								$('#caller-1-wrapper').data('id',event.data.user.db_id);
								$('#caller-1-wrapper').data('uname',event.data.user.twitter_data.screen_name);
*/
			                }
			                else {
			                	$('#loading-screen').fadeOut();
			                }
			            }
			        });
			    }
			  //console.log("Signal sent from connection " + event.from.id);
			  // Process the event.data property, if there is any data.
			});
			
			session.on("signal:declineMessage", function(event) {
				//console.log(event);
				if ( event.data.connectionId == session.connection.connectionId ) {
					var popUpHTML = '<div id="incoming-modal" tabindex="-1" role="dialog" aria-labelledby="incoming-modal-label">' +
									  '<div class="modal-dialog modal-sm" role="document">' +
									    '<div class="modal-content">' +
									      '<div class="modal-header">' +
									        '<h4 class="modal-title" id="myModalLabel">Message</h4>' +
									      '</div>' +
									      '<div class="modal-body">' +
									      	'<p>Your request to join has been declined.</p>' +
									      '</div>' +
									      '<div class="modal-footer">' +
									        '<button type="button" class="btn btn-default" data-dismiss="modal">Ok</button>' +
									      '</div>' +
									    '</div>' +
									  '</div>' +
									'</div>';
					$('#modal').html(popUpHTML);
					$('#modal').modal('show');
					
					$('#caller-1-wrapper').hide();
					$('#incoming-guest').hide();
					$('#placeholder-wrapper-cell').show();
			    }
			  //console.log("Signal sent from connection " + event.from.id);
			  // Process the event.data property, if there is any data.
			});
			
			session.on("signal:listJoin", function(event) {
				var data = {
					type		: 'twitter_list_join',
					room		: roomSlug,
					user_name	: event.data.name,
					user_id		: event.data.userId
				};
				//console.log(data);
		    	$.ajax({
		            type: "POST",
		            url: baseurl+"room-ajax.php",
		            data: data,
		            success: function(data) {
		                console.log(data);
		            }
		        });
			});
			
			session.on("signal:allow_request_user", function(event) {
				console.log(event);
				var connectionData = event.data.split('|');
				
				if ( connectionData[0] == "Allow" ) {
					if ( connectionData[1] == session.connection.connectionId ) {
						var connectionUserData = session.connection.data.split('|');
						$('#loading-screen h1').text('Joining stream...');
						$('#loading-screen').fadeIn();
						var data = {
							type		: 'update_room_guest',
							room		: roomSlug,
							guest_id	: connectionUserData[3]
						};
						
				    	$.ajax({
				            type: "POST",
				            url: baseurl+"room-ajax.php",
				            data: data,
				            success: function(data) {
				            	console.log(data);
				                if (data) {
					                window.location.href = baseurl+roomSlug;
				                }
				                else {
				                	$('#loading-screen').fadeOut();
				                }
				            }
				        });
					}
				}
				else {
					$('#modal').modal('hide');
				}
			});
			
			session.on("signal:allow_guest", function(event) {
				if (event.from.connectionId != session.connection.connectionId) {
					//console.log(event);
					if (event.data == '1') {
						var lock_html = '<div class="stream-cell open-state" id="placeholder-cell">' +
							            	'<div class="open-seat-wrapper">' +   
												'<div class="open-seat-container">' +
													'<button class="caller-button call-in">' +
														'Join <span class="join-icon inline-icon"></span>' +
													'</button>' +
												'</div>' +
											'</div>' +
										'</div>';
					}
					else {
						var lock_html = '<div class="locked-seat-cell locked-host-state" id="placeholder-cell">' +
								        	'<div class="locked-seat-cell">' +
												'<div class="locked-icon">' +
													'<span class="icon inline-icon" style="width:100%;"></span>' +
												'</div>' +
											'</div>' +
										'</div>';
					}
					$('#placeholder-wrapper-cell').html(lock_html);
					
					joinClickEvent();
				}
			});
			
			session.on("signal:cancelCall", function(event) {
				if (event.from.connectionId != session.connection.connectionId) {
					$('#caller-1-wrapper').hide();
					$('#incoming-guest').hide();
					$('#placeholder-wrapper-cell').show();
				}
			});
			
			
		}
		
		function test () {
			console.log('test');
		}

//- Custom Calendar
/*
var FC = $.fullCalendar; // a reference to FullCalendar's root namespace
var View = FC.View;      // the class that all views must inherit from
var CustomView;          // our subclass

CustomView = View.extend({ // make a subclass of View

    initialize: function() {
        // called once when the view is instantiated, when the user switches to the view.
        // initialize member variables or do other setup tasks.
    },

    render: function() {
        // responsible for displaying the skeleton of the view within the already-defined
        // this.el, a jQuery element.
        console.log(this.el);
    },

    setHeight: function(height, isAuto) {
        // responsible for adjusting the pixel-height of the view. if isAuto is true, the
        // view may be its natural height, and `height` becomes merely a suggestion.
    },

    renderEvents: function(events) {
        // reponsible for rendering the given Event Objects
    },

    destroyEvents: function() {
        // responsible for undoing everything in renderEvents
    },

    renderSelection: function(range) {
        // accepts a {start,end} object made of Moments, and must render the selection
    },

    destroySelection: function() {
        // responsible for undoing everything in renderSelection
    }

});

FC.views.custom = CustomView; // register our class with the view system
*/