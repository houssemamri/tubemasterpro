$(document).ready(function(){
	//localStorage.setItem('run-tour-1','yes');
	//localStorage.setItem('step-tour-1','0');
	//$('#tour-modal').modal('show');
	var keyword_form_interval;
	var channel_form_interval;
	var video_ad_interval;
    var submit_trigger = false;
    var current_url = $(location).attr('href');

    // get the key
    var runtour = localStorage.getItem('run-tour-1');
    //console.log(runtour);
    
    // - run if key has value 'no' (set inside the 'onFinish' callback function)
 	if ( runtour ) {
	    if( runtour == 'no'){
			$('#tour-modal').modal('hide');//simply hide the trigger
		}
		else {
			/*
if ( current_url.split('/')[4] && current_url.split('/')[4].split('?')[0] != 'keyword_search' ) {
				window.location.href = 'http://www.tubemasterpro.com/dashboard';
			}
			else {
				reset_tour();
				pre_tour();
			}
*/
			pre_tour();
		}
	}
	else {
		reset_tour();
		pre_tour();
	}
	
	function goto_main () {
		reset_tour();
		window.location.href = 'http://www.tubemasterpro.com/dashboard';
	}
	
	function reset_tour () {
		$.ajax({
			url: 'http://www.tubemasterpro.com/dashboard/dashboard_ajax/reset_tour',
			type: "POST"
		});

		localStorage.setItem('run-tour-1','yes');
		localStorage.setItem('step-tour-1','0');
	}
	
	function pre_tour () {
		var steptour = localStorage.getItem('step-tour-1');
		//console.log(steptour);
		//console.log(runtour);
		if ( !runtour ) {
			localStorage.setItem('run-tour-1','yes');
			runtour = localStorage.getItem('run-tour-1');
		}

		if ( !steptour ) {
			localStorage.setItem('step-tour-1','0');
			steptour = localStorage.getItem('step-tour-1');
		}

		if ( steptour == '0' ) {
			$('#tour-modal').modal('show');
			$('#skiptour').on('click',function(e){
				e.preventDefault();
				localStorage.setItem('run-tour-1','no');
				$('#tour-modal').modal('hide');
			});
			init_tour(1);
		}
		else {
			var step = 1;
			switch ( steptour ) {
				case "2":
					if ( current_url.split('/')[4] && current_url.split('/')[4].split('?')[0] != 'video_search' ) {
						goto_main();
					}
					$('#cancel-search').prop('disabled',true);
					$('#search_notice_modal').on('hide.bs.modal', function (e) {
						init_tour(3);
						$('body').powerTour('run',0);
						//$('body').powerTour('update', '#step-three');
						//$('body').powerTour('navigation', ["goto", 3]);
					})
				break;
				
				case "4":
					if ( current_url.split('/')[4] && current_url.split('/')[4].split('?')[0] != 'keyword_search' ) {
						goto_main();
					}
					init_tour(5);
					clearInterval( keyword_form_interval );
                    keyword_form_interval = setInterval(function(){
                    	if ($('#keywords_table').length > 0) {
                    		if ( $('#keywords_table tbody tr').length > 0 ) {
                            	clearInterval( keyword_form_interval );
                        		var tr = $('#keywords_table tbody tr').first();
                        		//console.log(tr.html());
                        		$('#video_tour_tr').prop('class',tr.prop('class'));
                        		$('#video_tour_tr td:nth-child(1)').text(tr.find('td:nth-child(1)').text());
                        		$('#video_tour_tr td:nth-child(2)').append(tr.find('td:nth-child(2)').html());
                        		$('#video_tour_tr td:nth-child(3)').append(tr.find('td:nth-child(3)').html());
                        		
                        		tr.empty();
                        		tr.append($('#video_tour_tr').html());

                        		$('#hook-five a.channel_key_search').on('click', function(e){
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
										url: 'http://www.tubemasterpro.com/dashboard/dashboard_ajax/save_channel_keyword_search',
										type: "POST",
										data: {
											main   : $('#keyword_input').val(),
											page   : page,
											entries: entries
										},
									});

									request.done(function(msg){
										window.location.href = 'http://www.tubemasterpro.com/dashboard/channel_search?keyword='+keyword;
									});
								});

                        		$('#video_tour_tr').empty();
								$('body').powerTour('run',0);
                        		$('body').powerTour('update', '#step-five');
								$('body').powerTour('navigation', ["goto", 5]);
                        	}
                    	}
                    }, 1000);
				break;
				
				case "5":
					if ( current_url.split('/')[4] && current_url.split('/')[4].split('?')[0] != 'channel_search' ) {
						goto_main();
					}
					$('#cancel-search').prop('disabled',true);
					init_tour(6);
					clearInterval( channel_form_interval );
                    channel_form_interval = setInterval(function(){
                    	if ($('#search_result table tbody tr').length > 0) {
                    		if ( !$('.main_loader').is(":visible") ) {
                    			console.log('visible');
                            	clearInterval( channel_form_interval );
                            	var tr = $('#search_result table tbody tr').first();
                            	tr.find('td:nth-child(7)').remove();
                            	tr.append($('#channel_tour_tr td:nth-child(1)'));
                            	$('#channel_tour_tr').empty();
                            	
								$('a.extract_videos').each(function(){
									$(this).attr('disabled', 'disabled');
								});
								
								$('body').powerTour('run',0);
                            	$('body').powerTour('update', '#step-six');
								$('body').powerTour('navigation', ["goto", 6]);
							}
                    	}
                    }, 1000);
				break;

				case "8":
					// init_tour(9);
					// $('body').powerTour('run',0);
     //            	$('body').powerTour('update', '#step-nine');
					// $('body').powerTour('navigation', ["goto", 9]);
					
					if ( current_url.split('/')[4] && current_url.split('/')[4].split('?')[0] != 'target_list' ) {
						goto_main();
					}
					
					if ( $('.pending_target').length > 0 ) {
						init_tour(9);
						$('body').powerTour('run',0);
	                	$('body').powerTour('update', '#step-nine');
						$('body').powerTour('navigation', ["goto", 9]);
					}
					else {
						init_tour(10);
						$('body').powerTour('run',0);
	                	$('body').powerTour('update', '#step-ten');
						$('body').powerTour('navigation', ["goto", 10]);
					}
				break;

				case "11":
				
					if ( current_url.split('/')[4] && current_url.split('/')[4].split('?')[0] != 'adwords_export' ) {
						goto_main();
					}
					
					init_tour(12);
					$('body').powerTour('run',0);
                	$('body').powerTour('update', '#step-twelve');
					$('body').powerTour('navigation', ["goto", 12]);
				break;
			
			}
			//$('body').powerTour('run',0);
			//$('body').powerTour('navigation', ["goto", parseInt(steptour)]);
		}
	}

	function init_tour ( step ) {
		$('body').powerTour({
	        tours : [
	            {
	                trigger            : '#starttour',
	                startWith          : step,
	                easyCancel         : false,
	                escKeyCancel       : false,
	                scrollHorizontal   : false,
	                keyboardNavigation : false,
	                loopTour           : false,
	                onStartTour        : function(ui){
						var steptour = localStorage.getItem('step-tour-1');
	                	if ( steptour == '0' ) {
		                	localStorage.setItem('run-tour-1','yes');
		                	localStorage.setItem('step-tour-1','0');
	                	}
	                },
	                onEndTour          : function(ui){
	                	$.ajax({
							url: 'http://www.tubemasterpro.com/dashboard/dashboard_ajax/update_tour',
							type: "POST",
							data: {
								status : 1
							}
						});
	                	localStorage.setItem('run-tour-1','no');
	                	localStorage.setItem('step-tour-1','0');
	                	if ( $('#btnExportCSV').length > 0 ) {
	                		$('#btnExportCSV').removeAttr('disabled');
	                	}
	                },	
	                onProgress         : function(ui){ },
	                steps : [
	                    {
	                        hookTo          : '#hook-one',
	                        content         : '#step-one',
	                        width           : 370,
	                        position        : 'bm',
	                        offsetY         : 20,
	                        offsetX         : 0,
	                        fxIn            : 'flipInX',
	                        fxOut           : 'bounceOutLeft',
	                        showStepDelay   : 0,
	                        center          : 'step',
	                        scrollSpeed     : 400,
	                        scrollEasing    : 'swing',
	                        scrollDelay     : 0,
	                        timer           : false,
	                        highlight       : true,
	                        keepHighlighted : false,
	                        onShowStep      : function(ui){
	                        	$('#tour-modal').modal('hide');
	                            localStorage.setItem('step-tour-1','1');
	                            
	                            $('#keyword_input').focus();
	                            
	                            $('#keyword_form').on('submit', function(){
									clearInterval( keyword_form_interval );
	                                keyword_form_interval = setInterval(function(){
	                                	if ($('#keywords_table').length > 0) {
	                                		if ( $('#keywords_table tbody tr').length > 0 ) {
			                                	clearInterval( keyword_form_interval );
		                                		var tr = $('#keywords_table tbody tr').first();
		                                		//console.log(tr.html());
		                                		$('#video_tour_tr').prop('class',tr.prop('class'));
		                                		$('#video_tour_tr td:nth-child(1)').text(tr.find('td:nth-child(1)').text());
		                                		$('#video_tour_tr td:nth-child(2)').append(tr.find('td:nth-child(2)').html());
		                                		$('#video_tour_tr td:nth-child(3)').append(tr.find('td:nth-child(3)').html());
		                                		
		                                		tr.empty();
		                                		tr.append($('#video_tour_tr').html());
		                                		$('#video_tour_tr').empty();
		                                		setTimeout(function(){$('body').powerTour('update', '#step-two');},400);
												$('body').powerTour('navigation', ["goto", 2]);
		                                	}
	                                	}
	                                }, 1000);
	                            });
	                        },
	                        onHideStep      : function(ui){ }
	                    },
	                    {
	                        hookTo          : '#hook-two',
	                        content         : '#step-two',
	                        width           : 365,
	                        position        : 'lm',
	                        offsetY         : 0,
	                        offsetX         : 0,
	                        fxIn            : 'bounceIn',
	                        fxOut           : 'flipOutY',
	                        showStepDelay   : 400,
	                        center          : 'step',
	                        scrollSpeed     : 400,
	                        scrollEasing    : 'swing',
	                        scrollDelay     : 1000,
	                        timer           : false,
	                        highlight       : true,
	                        keepHighlighted : false,
	                        onShowStep      : function(ui){
	                            localStorage.setItem('step-tour-1','2');
	                            $('.video_key_search').on('click', function(e){
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
										url: 'http://www.tubemasterpro.com/dashboard/dashboard_ajax/save_video_keyword_search',
										type: "POST",
										data: {
											main   : $('#keyword_input').val(),
											page   : page,
											entries: entries
										},
									});

									request.done(function(msg){
										window.location.href = 'http://www.tubemasterpro.com/dashboard/video_search?keyword='+keyword;
									});
								});
	                            //setInterval(function(){ $('body').powerTour('navigation', ["goto", 3]); }, 1000);
	                        },
	                        onHideStep      : function(ui){ }
	                    },
	                    {
	                        hookTo          : '#hook-three',
	                        content         : '#step-three',
	                        width           : 365,
	                        position        : 'lm',
	                        offsetY         : 0,
	                        offsetX         : 0,
	                        fxIn            : 'bounceIn',
	                        fxOut           : 'bounceOutLeft',
	                        showStepDelay   : 400,
	                        center          : 'step',
	                        scrollSpeed     : 400,
	                        scrollEasing    : 'swing',
	                        scrollDelay     : 1000,
	                        timer           : false,
	                        highlight       : true,
	                        keepHighlighted : false,
	                        onShowStep      : function(ui){
	                            localStorage.setItem('step-tour-1','3');
	                            $('#search_form').on('submit', function(e){
	                            	//console.log('submit');
	                            	e.preventDefault();
		                            e.stopImmediatePropagation();
		                            return false;
	                            });
	                            $('#save-target-button').on('click',function(){
	                            	$('body').powerTour('update', '#step-four');
		                            $('body').powerTour('navigation', ["goto", 4]);
	                            });
	                        },
	                        onHideStep      : function(ui){ }
	                    },
	                    {
	                        hookTo          : '#hook-four',
	                        content         : '#step-four',
	                        width           : 365,
	                        position        : 'bl',
	                        offsetY         : 20,
	                        offsetX         : 0,
	                        fxIn            : 'bounceIn',
	                        fxOut           : 'bounceOutRight',
	                        showStepDelay   : 400,
	                        center          : 'step',
	                        scrollSpeed     : 400,
	                        scrollEasing    : 'swing',
	                        scrollDelay     : 1000,
	                        timer           : false,
	                        highlight       : true,
	                        keepHighlighted : false,
	                        onShowStep      : function(ui){
	                            localStorage.setItem('step-tour-1','4');
	                        },
	                        onHideStep      : function(ui){ }
	                    },
	                    {
	                        hookTo          : '#hook-five',
	                        content         : '#step-five',
	                        width           : 365,
	                        position        : 'lm',
	                        offsetY         : 0,
	                        offsetX         : 30,
	                        fxIn            : 'bounceIn',
	                        fxOut           : 'bounceOutLeft',
	                        showStepDelay   : 400,
	                        center          : 'step',
	                        scrollSpeed     : 400,
	                        scrollEasing    : 'swing',
	                        scrollDelay     : 1000,
	                        timer           : false,
	                        highlight       : true,
	                        keepHighlighted : false,
	                        onShowStep      : function(ui){
	                            localStorage.setItem('step-tour-1','5');
	                        },
	                        onHideStep      : function(ui){ }
	                    },
	                    {
	                        hookTo          : '#hook-six',
	                        content         : '#step-six',
	                        width           : 365,
	                        position        : 'lm',
	                        offsetY         : 20,
	                        offsetX         : 0,
	                        fxIn            : 'bounceIn',
	                        fxOut           : 'bounceOutLeft',
	                        showStepDelay   : 400,
	                        center          : 'step',
	                        scrollSpeed     : 400,
	                        scrollEasing    : 'swing',
	                        scrollDelay     : 1000,
	                        timer           : false,
	                        highlight       : true,
	                        keepHighlighted : false,
	                        onShowStep      : function(ui){
	                            localStorage.setItem('step-tour-1','6');
								$('.extract_videos').removeAttr('disabled');
								$('#channel_modal').on('show.bs.modal', function (e) {
									$('#extract_videos').attr('disabled','disabled');
									$(this).find('.modal-footer button').attr('disabled','disabled');
									$('body').powerTour('update', '#step-seven');
									$('body').powerTour('navigation', ["goto", 7]);
								});
	                        },
	                        onHideStep      : function(ui){ }
	                    },
	                    {
	                        hookTo          : '#hook-seven',
	                        content         : '#step-seven',
	                        width           : 365,
	                        position        : 'rm',
	                        offsetY         : -30,
	                        offsetX         : 20,
	                        fxIn            : 'bounceIn',
	                        fxOut           : 'bounceOutRight',
	                        showStepDelay   : 400,
	                        center          : 'step',
	                        scrollSpeed     : 400,
	                        scrollEasing    : 'swing',
	                        scrollDelay     : 1000,
	                        timer           : false,
	                        highlight       : false,
	                        keepHighlighted : false,
	                        onShowStep      : function(ui){
	                            localStorage.setItem('step-tour-1','7');
	                            $('#confirmation-ok').on('click', function(){
									$('body').powerTour('update', '#step-eight');
									$('body').powerTour('navigation', ["goto", 8]);
								});
	                        },
	                        onHideStep      : function(ui){ }
	                    },
	                    {
	                        hookTo          : '#hook-eight',
	                        content         : '#step-eight',
	                        width           : 233,
	                        position        : 'rm',
	                        offsetY         : 0,
	                        offsetX         : 0,
	                        fxIn            : 'bounceIn',
	                        fxOut           : 'bounceOutRight',
	                        showStepDelay   : 400,
	                        center          : 'step',
	                        scrollSpeed     : 400,
	                        scrollEasing    : 'swing',
	                        scrollDelay     : 1000,
	                        timer           : false,
	                        highlight       : true,
	                        keepHighlighted : false,
	                        onShowStep      : function(ui){
	                            localStorage.setItem('step-tour-1','8');
	                        },
	                        onHideStep      : function(ui){ }
	                    },
	                    {
	                        hookTo          : '#hook-nine',
	                        content         : '#step-nine',
	                        width           : 365,
	                        position        : 'bl',
	                        offsetY         : 20,
	                        offsetX         : 0,
	                        fxIn            : 'bounceIn',
	                        fxOut           : 'bounceOutDown',
	                        showStepDelay   : 400,
	                        center          : 'step',
	                        scrollSpeed     : 400,
	                        scrollEasing    : 'swing',
	                        scrollDelay     : 1000,
	                        timer           : false,
	                        highlight       : true,
	                        keepHighlighted : false,
	                        onShowStep      : function(ui){
	                            localStorage.setItem('step-tour-1','9');
	                            $('#hook-nine').fadeTo('fast', 1);
	                            $('#hook-nine .delete_list').attr('disabled', 'disabled');
	                        },
	                        onHideStep      : function(ui){ }
	                    },
	                    {
	                        hookTo          : '#hook-ten',
	                        content         : '#step-ten',
	                        width           : 365,
	                        position        : 'bm',
	                        offsetY         : 0,
	                        offsetX         : -50,
	                        fxIn            : 'bounceIn',
	                        fxOut           : 'bounceOutDown',
	                        showStepDelay   : 400,
	                        center          : 'step',
	                        scrollSpeed     : 400,
	                        scrollEasing    : 'swing',
	                        scrollDelay     : 1000,
	                        timer           : false,
	                        highlight       : true,
	                        keepHighlighted : false,
	                        onShowStep      : function(ui){
	                            localStorage.setItem('step-tour-1','10');
	                            $('#hook-ten .form-group:nth-child(2) button').attr('disabled','disabled');
	                            $('#hook-ten .form-group:nth-child(4) button').attr('disabled','disabled');
	                            $('#hook-ten .form-group:nth-child(5) button').attr('disabled','disabled');
	                        },
	                        onHideStep      : function(ui){ }
	                    },
	                    {
	                        hookTo          : '#hook-eleven',
	                        content         : '#step-eleven',
	                        width           : 233,
	                        position        : 'rm',
	                        offsetY         : 0,
	                        offsetX         : 0,
	                        fxIn            : 'bounceIn',
	                        fxOut           : 'bounceOutRight',
	                        showStepDelay   : 400,
	                        center          : 'step',
	                        scrollSpeed     : 400,
	                        scrollEasing    : 'swing',
	                        scrollDelay     : 1000,
	                        timer           : false,
	                        highlight       : true,
	                        keepHighlighted : false,
	                        onShowStep      : function(ui){
	                            localStorage.setItem('step-tour-1','11');
								$('body').powerTour('update', '#step-eleven');
	                        },
	                        onHideStep      : function(ui){ }
	                    },
	                    {
	                        hookTo          : '#hook-twelve',
	                        content         : '#step-twelve',
	                        width           : 365,
	                        position        : 'lm',
	                        offsetY         : 0,
	                        offsetX         : 0,
	                        fxIn            : 'bounceIn',
	                        fxOut           : 'bounceOutRight',
	                        showStepDelay   : 400,
	                        center          : 'step',
	                        scrollSpeed     : 400,
	                        scrollEasing    : 'swing',
	                        scrollDelay     : 1000,
	                        timer           : false,
	                        highlight       : true,
	                        keepHighlighted : false,
	                        onShowStep      : function(ui){
	                            localStorage.setItem('step-tour-1','12');
	                            $('#step-twelve footer a').attr('disabled','disabled');
	                            $('#file_name').focus();
	                            $('#file_name').on('keyup change', function(){
	                            	if ( $(this).val() != '' ) {
	                            		$('#step-twelve footer a').removeAttr('disabled');
	                            	}
	                            	else {
	                            		$('#step-twelve footer a').attr('disabled','disabled');
	                            	}
	                            });
	                        },
	                        onHideStep      : function(ui){ }
	                    },
	                    {
	                        hookTo          : '#hook-thirteen',
	                        content         : '#step-thirteen',
	                        width           : 365,
	                        position        : 'lm',
	                        offsetY         : 0,
	                        offsetX         : 20,
	                        fxIn            : 'bounceIn',
	                        fxOut           : 'bounceOutRight',
	                        showStepDelay   : 400,
	                        center          : 'step',
	                        scrollSpeed     : 400,
	                        scrollEasing    : 'swing',
	                        scrollDelay     : 1000,
	                        timer           : false,
	                        highlight       : true,
	                        keepHighlighted : false,
	                        onShowStep      : function(ui){
	                            localStorage.setItem('step-tour-1','13');
	                            $('#add_video_ads').focus();
	                            $('#add_video_ads').on('keyup change', function(){
	                            	if ( $(this).val() != '' ) {
	                            		var video_ads_list = $('#video_ads_list li').length;
	                            		clearInterval( video_ad_interval );
					                    video_ad_interval = setInterval(function(){
					                    	if ( $('#video_ads_list li').length > video_ads_list ) {
					                    		console.log('test');
					                    		clearInterval( video_ad_interval );
												$('body').powerTour('update', '#step-fourteen');
												$('body').powerTour('navigation', ["goto", 14]);
					                    	}
					                    }, 1000);
	                            	}
	                            	else {
	                            		$('#step-thirteen footer a').attr('disabled','disabled');
	                            	}
	                            });
	                        },
	                        onHideStep      : function(ui){ }
	                    },
	                    {
	                        hookTo          : '#hook-fourteen',
	                        content         : '#step-fourteen',
	                        width           : 365,
	                        position        : 'lm',
	                        offsetY         : 0,
	                        offsetX         : 0,
	                        fxIn            : 'bounceIn',
	                        fxOut           : 'bounceOutRight',
	                        showStepDelay   : 400,
	                        center          : 'step',
	                        scrollSpeed     : 400,
	                        scrollEasing    : 'swing',
	                        scrollDelay     : 1000,
	                        timer           : false,
	                        highlight       : true,
	                        keepHighlighted : false,
	                        onShowStep      : function(ui){
	                            localStorage.setItem('step-tour-1','14');
	                            $('#step-fourteen footer a').attr('disabled','disabled');
	                            $('#display_url').focus();
	                            $('#display_url').on('keyup change', function(){
	                            	if ( $(this).val() != '' ) {
	                            		$('#step-fourteen footer a').removeAttr('disabled');
	                            	}
	                            	else {
	                            		$('#step-fourteen footer a').attr('disabled','disabled');
	                            	}
	                            });
	                        },
	                        onHideStep      : function(ui){ }
	                    },
	                    {
	                        hookTo          : '#hook-fifteen',
	                        content         : '#step-fifteen',
	                        width           : 365,
	                        position        : 'lm',
	                        offsetY         : 0,
	                        offsetX         : 0,
	                        fxIn            : 'bounceIn',
	                        fxOut           : 'bounceOutRight',
	                        showStepDelay   : 400,
	                        center          : 'step',
	                        scrollSpeed     : 400,
	                        scrollEasing    : 'swing',
	                        scrollDelay     : 1000,
	                        timer           : false,
	                        highlight       : true,
	                        keepHighlighted : false,
	                        onShowStep      : function(ui){
	                            localStorage.setItem('step-tour-1','15');
	                            $('#destination_url').focus();
	                            if ( $('#destination_url').val() != '' ) {
                            		$('#step-fifteen footer a').removeAttr('disabled');
                            	}
                            	else {
                            		$('#step-fifteen footer a').attr('disabled','disabled');
                            	}

	                            $('#destination_url').on('keyup change', function(){
	                            	if ( $(this).val() != '' ) {
	                            		$('#step-fifteen footer a').removeAttr('disabled');
	                            	}
	                            	else {
	                            		$('#step-fifteen footer a').attr('disabled','disabled');
	                            	}
	                            });
	                        },
	                        onHideStep      : function(ui){ }
	                    },
	                    {
	                        hookTo          : '#hook-sixteen',
	                        content         : '#step-sixteen',
	                        width           : 365,
	                        position        : 'tm',
	                        offsetY         : 0,
	                        offsetX         : 20,
	                        fxIn            : 'bounceIn',
	                        fxOut           : 'bounceOutRight',
	                        showStepDelay   : 400,
	                        center          : 'step',
	                        scrollSpeed     : 400,
	                        scrollEasing    : 'swing',
	                        scrollDelay     : 1000,
	                        timer           : false,
	                        highlight       : true,
	                        keepHighlighted : false,
	                        onShowStep      : function(ui){
	                            localStorage.setItem('step-tour-1','16');
	                            $('#hook-sixteen').css({
	                            	"background-color" 	: "white",
	                            	"border-radius"		: "5px"
	                            });

	                            $('#step-sixteen footer a').attr('disabled','disabled');
                        		$('.target_checkbox').each(function(){
                        			$(this).on('change',function(){
                        				if ( $('.target_checkbox:checked').length > 0 ) {
                        					//var num_vids = parseInt( $('#total_target_vid font').text() );
                        					//if ( num_vids <= 500 ) {
                        						$('#step-sixteen footer a').removeAttr('disabled');
                        					//}
                        					// else {
                        					// 	$('#step-sixteen footer a').attr('disabled','disabled');
                        					// }
                        				}
                        				else {
                        					$('#step-sixteen footer a').attr('disabled','disabled');
                        				}
                        			});
                        		});
	                        },
	                        onHideStep      : function(ui){ }
	                    },
	                    {
	                        hookTo          : '#options',
	                        content         : '#step-seventeen',
	                        width           : 365,
	                        position        : 'tl',
	                        offsetY         : 20,
	                        offsetX         : 0,
	                        fxIn            : 'bounceIn',
	                        fxOut           : 'bounceOutRight',
	                        showStepDelay   : 400,
	                        center          : 'step',
	                        scrollSpeed     : 400,
	                        scrollEasing    : 'swing',
	                        scrollDelay     : 1000,
	                        timer           : false,
	                        highlight       : true,
	                        keepHighlighted : false,
	                        onShowStep      : function(ui){
	                            localStorage.setItem('step-tour-1','17');
	                            $('#options #toggle_option').attr('disabled', 'disabled');
	                        },
	                        onHideStep      : function(ui){ }
	                    },
	                    {
	                        hookTo          : '#hook-eightteen',
	                        content         : '#step-eightteen',
	                        width           : 365,
	                        position        : 'tl',
	                        offsetY         : 20,
	                        offsetX         : 0,
	                        fxIn            : 'zoomInDown',
	                        fxOut           : 'hinge',
	                        showStepDelay   : 400,
	                        center          : 'step',
	                        scrollSpeed     : 400,
	                        scrollEasing    : 'swing',
	                        scrollDelay     : 1000,
	                        timer           : false,
	                        highlight       : true,
	                        keepHighlighted : false,
	                        onShowStep      : function(ui){
	                            localStorage.setItem('step-tour-1','18');
	                            $('#btnExportCSV').attr('disabled', 'disabled');
	                        },
	                        onHideStep      : function(ui){ }
	                    }
	                ],
	                stepDefaults : [
	                    {
	                        width           : 300,
	                        position        : 'tr',
	                        offsetY         : 0,
	                        offsetX         : 0,
	                        fxIn            : 'fadeIn',
	                        fxOut           : 'fadeOut',
	                        showStepDelay   : 0,
	                        center          : 'step',
	                        scrollSpeed     : 400,
	                        scrollEasing    : 'swing',
	                        scrollDelay     : 0,
	                        timer           : '00:00',
	                        highlight       : true,
	                        keepHighlighted : false,
	                        onShowStep      : function(ui){ },
	                        onHideStep      : function(ui){ }
	                    }
	                ]
	            }
	        ]
	    });
	}
});