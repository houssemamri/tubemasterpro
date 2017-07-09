$(document).ready(function(){

var max_videos = 20000;
var max_tgv	   = 2000;

myValidate();
$('#file_name, #video_ads, .target_checkbox, #mobile_bid_mdifier, #language, #countries, #display_url, #destination_url').change(myValidate);	
$('#video_ads').hide();
$('#video_ads_list').css({'margin-top':'10px', 'height':'220px', 'width':'520px', 'padding':'0px', 'list-style' : 'none', 'overflow-y' : 'auto', 'background-color' : '#eee' });

	loadvideoads();
	
	campaigns_init();

	$('.target_checkbox').change(function(){
    	
        	
	            var add = parseInt($(this).parents('label').data('count')) * ((this.checked) ? 1 : -1);
	            var total = parseInt($("#total_target_vid font").html()) + add;
	            var checked = $('.target_checkbox:checked').length;
	            var left = ( checked < 10 ) ? ( 10 - checked ) : 0;
	            
	            // if(total >= 500){
	            // 	 $('#total_target_vid font').css('color', 'red');
	            // 	 $('#btnExportCSV').attr("disabled",true);
	            // 	 $('#total_target_vid').show();
	            // }
	            // else if(total == 0) {
	            // 	$('#total_target_vid').hide();
	            // }
	            // else{
	            // 	$('#total_target_vid font').css('color', '#449d44');
	            // 	$('#btnExportCSV').attr("disabled",false);
	            // 	$('#total_target_vid').show();
	            // }

	         	if(total == 0) {
	            	$('#total_target_vid').hide();
	            	$('#target_list').css('border', '1px solid #843534');
	            }
	            else{
	            	$('#target_list').css('border', '1px solid #3c763d');
	            	$('#total_target_vid font').css('color', '#449d44');
	            	//$('#btnExportCSV').attr("disabled",false);
	            	
	            	if ( total < max_videos ) {
		            	$('.target_checkbox:not(:checked)').prop('disabled',false);
		            	
			            /*
if ( left > 0 ) {
				            $('.target_checkbox:not(:checked)').prop('disabled',false);
			            }
			            
			            if ( left < 10 && left > 5 ) {
			            	$('#total_targets font').css('color', '#449d44');
			            }
			            else if ( left < 5 && left > 0 ) {
			            	$('#total_targets font').css('color', '#ffbb00');
			            }
			            else if ( left == 0 ) {
			            	$('#total_targets font').css('color', '#843534');
			            	$('.target_checkbox:not(:checked)').prop('disabled',true);
			            }
			            
			            $('#total_targets font').text(left);
*/
	            	}
	            	else {
	            		$('#total_target_vid font').css('color', '#843534');
		            	if ( total > max_videos ) {
		            		$('#target_limit_modal .modal-body').html("we can't add this list of videos as it will take you over your campaign limit of 20,000 videos. WE'D LOVE to to let you do billions of videos, but Google won't.");
			            	$(this).prop('checked',false);
			            	total -= add;
							$('#total_target_vid font').css('color', '#449d44');
		            	}
		            	else {
		            		$('#target_limit_modal .modal-body').html("You're allowed a maximum of 20,000 videos in any one YouTube campaign. You've hit that limit with your selected video lists at this point.");
		            		$('.target_checkbox:not(:checked)').prop('disabled',true);
		            	}
		            	
		            	$('#target_limit_modal').modal('show');
	            	}
	            	
	            	myValidate();
	            	$('#total_target_vid').show();
	            }


	            $("#total_target_vid font").html(total);

				//console.log(total);
    	
	}) 

	$('#countries').click(function(){
		var loc = $('#countries').val();
		var count = loc.length;
		$('#country_count').html(count);
	})

	$('#language').click(function(){
		var loc = $('#language').val();
		var count = loc.length;
		$('#language_count').html(count);
	})
	
	//Number only input
	$("#mobile_bid_mdifier").keypress(function(e){
		var key = e.keyCode ? e.keyCode : e.which;
		if ( isNaN( String.fromCharCode(key) ) ) return false;
  	});

	
	//setselected();

	var tog = true;

	$("#toggle_option").click(function(){

  		$(".added_options").toggle(function(){

  			if(tog){

  				$("#toggle_option").html('Hide Options');

  				tog = false;

  			}else{

  				$("#toggle_option").html('Show Options');

  				tog = true;

  			}

  		});

	});



	$("#add_video_ad").click(function(){

		var video_ads_url = $("#add_video_ads").val();
		var video_id      = validateYouTubeUrl(video_ads_url);
		if(!video_id) {
			//$('#add_video_ad').removeClass('btn-success').addClass('gray');  
			$('#error-msg span').html('Video Ad link is Invalid.');
			ShowError(true);

			return;
		}
		else {
			//$('#add_video_ad').removeClass('gray').addClass('btn-success');
			var exists = false;
			$('#video_ads option').each(function(){
				if ( $(this).val() ==  video_id ) {
					$('#error-msg span').html('Video Ad already exists.');
					ShowError(true);
					exists = true;
					return;
				}
			});
			
			if ( exists ) return;
		}

		$.ajax({

			type: "POST",

			url: $(this).data("action") + '/insert_page',

			data: { url: encodeURI(video_ads_url), id: video_id },

			cache: false

		})

		.done(function( html ) {
			
			//$("#video_ads").prependTo($("#video_ads"));
			//$("#video_ads").html(html);

			$("#add_video_ads").val("");

			vidIdCollect = [];
			

			var option = $(html);
			//var vidOptions = $("#video_ads option");
			
			if(option != null) {
				option.attr('selected',true);
				option.prependTo($("#video_ads"));
				vidIdCollect.push(option);
				getVideoTitle();
			}

			/*if(vidOptions.length > 0) {
				for(i=0;i<vidOptions.length;i++){
					vidIdCollect.push(vidOptions[i]);
				}
				getVideoTitle();
			}*/
			/*
			var option = $(html);
			
			vidIdCollect = [];
			
			
			$("#video_ads").prependTo($("#video_ads"));
			vidIdCollect.push(option);
			//$("#video_ads").prepend("<option value='"+html+"'>"+html+"</option>");

			$("#add_video_ads").val("");
			
			getVideoTitle();

			console.log(html);
*/
			

		});

  		

	});

	$('#add_video_ads').next('span').children('button').addClass('gray').removeClass('btn-success');

	$('#add_video_ads').bind('keyup change', function () {
		console.log('asdasd' + $(this).next('span').children('button').hasClass('btn-success'));
		if($(this).val() == "") {
			if(!$(this).next('span').children('button').hasClass('gray')) {
				$(this).next('span').children('button').addClass('gray').removeClass('btn-success');
			}
		}
		else {
			if($(this).next('span').children('button').hasClass('gray')) {
				$(this).next('span').children('button').removeClass('gray').addClass('btn-success');
			}
		}
	});

	$('#saveCampaign').bind('click', function(){
		show_loader( 'Saving', true, 400 );
		var theButton = $(this);

		var filename = $('#file_name').val();

		var video_ads = $('#video_ads').val();
		var video_ads_option = $('#video_ads option:selected');

		var disp_url = $('#display_url').val();

		var dest_url = $('#destination_url').val();

		var budget = $('#budget').val();

		var max_cpv = $('#max_cpv').val();

		var start_date = $('#start_date').data('date-value');
		start_date = $.datepicker.formatDate('yy-mm-dd', new Date(start_date));
		
		var end_date = $('#end_date').data('date-value');
		if(end_date==""){
			end_date = "#N/A";
		}else{
		  	end_date = $.datepicker.formatDate('yy-mm-dd', new Date(end_date));
		}

		var delivery_method = $('#delivery_method').val();

		var mobile = $('#mobile_bid_mdifier').val();

		mobile = $('#mbm-type option:selected').val() + mobile + '%';
		
		mobile = (parseInt($('#mbm-type option:selected').val()) == 0) ? '0%' : mobile;

		//Values in Array

		var lang = $('#language').val();

		var loc = $('#countries').val();

		var age = $('.age_checkbox:checked');

		var gender = $('.gender_checkbox:checked');


		//console.log(lang);
		var targettr = $('#targeting_targets');
		var target_ads = $('#targeting_ads');
		var target_tg = $('#targeting_targettinggroups');
		
		var target_video_id = [];


		var date_today_format 	= $.datepicker.formatDate('yy-mm-dd', new Date());
		var date_today 			= new Date(date_today_format).getTime()/1000;
		var start_date_db 		= new Date(start_date).getTime()/1000;
		if(date_today > start_date_db){
			$('#error-msg span').html('Error: Start date must not set to the past');
			ShowError(true);
			show_loader ( 'ERROR', false, 100 );
			return false;
		}
/*
		if(Validation()) {
			show_loader( 'Error', false, 400 );
			ShowError(true);
			return false;
		}
*/
		
		var targets = [];
		var target_lists = '';
		$('.target_checkbox:checked').each(function(){
			targets.push($(this).val());
		});
		var ages = [];
		var age_list = '';
		$('.age_checkbox:checked').each(function(){
			ages.push($(this).val());
		});
		var genders = [];
		var gender_list = '';
		$('.gender_checkbox:checked').each(function(){
			genders.push($(this).val());
		});
		target_lists = targets.join();
		gender_list = genders.join();
		age_list = ages.join();
		var country_list = (loc != '') ? loc.join() : '';
		var language_list = (lang != '') ? lang.join() : '';
		var video_ads_list = (video_ads && video_ads != '') ? video_ads.join() : '';
		var dataToSend = {
			name 			: filename,
			video_ads		: video_ads_list,
			display_url 	: disp_url,
			destination_url	: dest_url,
			daily_budget	: budget,
			max_cpv			: max_cpv,
			start_date		: start_date,
			end_date		: end_date,
			delivery_method : delivery_method,
			mbm_sign		: $('#mbm-type').val(),
			mbm_value		: $('#mobile_bid_mdifier').val(),
			age				: age_list,
			gender			: gender_list,
			language		: language_list,
			countries		: country_list,
			target_lists	: target_lists
		};
		
		//console.log(dataToSend);
		
		$.ajax({
		  url: theButton.data("action") + '/save_campaign',
		  type:"POST",
		  data: {
			  data		: dataToSend,
			  id		: $('#campaign_list').val()
		  },
		  dataType  : 'json'
		}).done(function(data){
			if ( data.valid && data.method == 'add' ) {
				var option = '<option value="'+data.id+'">'+filename+'</option>';
				$('#campaign_list').append(option);
				$('#campaign_list').val(data.id);
				$('#copy_campaign_btn').show();
				$('#delete_campaign_btn').show();
			}
			show_loader( 'Saved', false, 400 );
			ShowSuccess( 'Campaign Saved!' );
			myValidate();
		});
		
	});

	$('#btnExportCSV').bind('click', function(){
		var theButton = $(this);

		var filename = $('#file_name').val();

		var video_ads = $('#video_ads').val();
		var video_ads_option = $('#video_ads option:selected');

		var disp_url = $('#display_url').val();

		var dest_url = $('#destination_url').val();

		var budget = $('#budget').val();

		var max_cpv = $('#max_cpv').val();

		var start_date = $('#start_date').data('date-value');
		start_date = $.datepicker.formatDate('yy-mm-dd', new Date(start_date));
		
		var end_date = $('#end_date').data('date-value');
		if(end_date==""){
			end_date = "#N/A";
		}else{
		  	end_date = $.datepicker.formatDate('yy-mm-dd', new Date(end_date));
		}

		var delivery_method = $('#delivery_method').val();

		var mobile = $('#mobile_bid_mdifier').val();

		mobile = $('#mbm-type option:selected').val() + mobile + '%';
		
		mobile = (parseInt($('#mbm-type option:selected').val()) == 0) ? '0%' : mobile;

		//Values in Array

		var lang = $('#language').val();

		var loc = $('#countries').val();

		var age = $('.age_checkbox:checked');

		var gender = $('.gender_checkbox:checked');


		//console.log(lang);
		var targettr = $('#targeting_targets');
		var target_ads = $('#targeting_ads');
		var target_tg = $('#targeting_targettinggroups');
		
		var target_video_id = [];

		var date_today_format 	= $.datepicker.formatDate('yy-mm-dd', new Date());
		var date_today 			= new Date(date_today_format).getTime()/1000;
		var start_date_db 		= new Date(start_date).getTime()/1000;
		if(date_today > start_date_db){
			$('#error-msg span').html('Error: Start date must not set to the past');
			ShowError(true);
			return false;
		}

		if(Validation()) {
			ShowError(true);
			return false;
		}

		var targetgroups = [];
		var targetgroupslist = '';
		
		$('.target_checkbox:checked').each(function(y, val){
			targetgroups.push($(val).parent('label').text().replace(/\s+\(.+?\)/g, ''));
		});
		targetgroupslist = targetgroups.join();
		
		//Ads Loop

	    var targeting_group = filename+'_TG';
		var group_index = 0;
		var num_vids = 0;
		
		$('.target_checkbox:checked').each(function(y, val){
			//console.log(val);
			//var targeting_group = $(val).parent('label').text().replace(/\s+\(.+?\)/g, '');
			
            //Target Loop
            var ytdata = $(val).data('ytdata');
            //console.log($(this).html());
            $.each(ytdata,function(key,value){
	            //console.log(value['link_url']);
	            num_vids++;
        
				if ( ( num_vids % max_tgv ) === 0 ) {
					console.log(num_vids);
					group_index++;
				}
				
	            var target_details_content = "<tr class=\"table-data\"> <td>Add</td> <td>Youtube video</td> <td>Enabled</td> <td>https://www.youtube.com/watch?v="+value['ytid']+"</td> <td>"+targeting_group+""+group_index+"</td> <td>#N/A</td> <td colspan=\"50\"></td> </tr>";

				$(targettr).after(target_details_content);
            });
        });
        
        console.log(group_index);
        
        for (var i=0;i<=group_index;i++) {
        	
		
			for(var j=0;j<video_ads.length;j++){
	
				if(video_ads[j]!=null){	
					//console.log($(video_ads_option[i]).data('title'));			
					var target_ads_content = "<tr class=\"table-data\"> <td>Add</td> <td>Enabled</td> <td>"+$(video_ads_option[j]).data('title')+"</td> <td>"+video_ads[j]+"</td> <td> default</td> <td>#N/A</td> <td> #N/A </td> <td>#N/A</td> <td>"+disp_url+"</td> <td>"+dest_url+"</td> <td>#N/A</td> <td>in-stream</td> <td>#N/A</td> <td>"+targeting_group+""+i+"</td> <td>"+filename+"</td> <td colspan=\"41\"></td></tr>";
	
					$(target_ads).after(target_ads_content);
	
					}		
	
			}
			
			//Gender Loop
			for(var k=0;k<gender.length;k++){
	
				if(gender[k]!=null){
	
					var target_g_content = "<tr class=\"table-data\"> <td>Add</td> <td>Gender</td> <td>Enabled</td> <td> "+$(gender[k]).val()+"</td> <td>"+targeting_group+""+i+"</td> <td align= 'center'> #N/A </td> <td colspan=\"50\"></td> </tr>";
	
					$(targettr).after(target_g_content);
	
				}		
	
			}
			
			//Age Loop
			for(var l=0;l<age.length;l++){
	
				if(age[l]!=null){
	
						var target_a_content = "<tr class=\"table-data\"> <td>Add</td> <td>Age</td> <td>Enabled</td> <td> "+$(age[l]).val()+"</td> <td>"+targeting_group+""+i+"</td> <td align= 'center'> #N/A </td> <td colspan=\"50\"> </td> </tr>";
	
						$(targettr).after(target_a_content);
	
				}		
	
			}
			
			//Targeting Group Loop
			var target_tg_content = "<tr class=\"table-data\"> <td>Add</td> <td>Enabled</td> <td>"+targeting_group+""+i+"</td> <td>"+filename+"</td> <td>"+max_cpv+"</td> <td colspan=\"50\"></td></tr>";
	
			$(target_tg).after(target_tg_content);
        }
		
				
		var targetsettr = $('#targeting_settings');
		//Location Loop
		for(var i=0;i<loc.length;i++){

			if(loc[i]!=null){



				var target_loc_content = "<tr class=\"table-data\"> <td>Add</td> <td>Location</td> <td>"+loc[i]+"</td> <td> "+filename+"</td> <td colspan=\"52\"> </td> </tr>";

				$(targetsettr).after(target_loc_content);

			}		

		}

		//Language Loop
		for(var i=0;i<lang.length;i++){

			if(lang[i]!=null){



				var target_lang_content = "<tr class=\"table-data\"> <td>Add</td> <td>Language</td> <td>"+lang[i]+"</td> <td> "+filename+"</td> <td colspan=\"52\"> </td> </tr>";

				$(targetsettr).after(target_lang_content);

			}		

		}



		



		$('#campaign_name_value').html(filename);

		$('#delivery_mode_value').html(delivery_method);

  		$('#start_date_value').html(start_date);
  		
  		$('#end_date_value').html(end_date);

   		$('#mobile_bid_modifier_value').html(mobile);
   		
   		$('#campaign_budget').html(budget);



		/*
$('#disp_url_value').html(disp_url);

  		$('#dest_url_value').html(dest_url);

  		$('#targeting_group_value').html();

 		$('#campaign_name2_value').html(filename);
 		
 		$('#campaign_name3_value').html(filename);

  		$('#max_cpv_value').html(max_cpv);
*/
		
		
		exportTableToCSV.apply(this, [$('#csv_table>table'), filename+'.csv']);
	});



	function exportTableToCSV($table, filename) {

        var $rows = $table.find('tr.table-data:has(td)'),



            // Temporary delimiter characters unlikely to be typed by keyboard

            // This is to avoid accidentally splitting the actual contents

            tmpColDelim = String.fromCharCode(11), // vertical tab character

            tmpRowDelim = String.fromCharCode(0), // null character



            // actual delimiter characters for CSV format

            colDelim = '\t',

            rowDelim = '\r\n',



            // Grab text from table into CSV formatted string
            csv = $rows.map(function (i, row) {

                var $row = $(row),

                    $cols = $row.find('td');

                    // var temp = [];

                    // $.each($cols, function (i,k){
                    // 	if($.trim($(k).text()) != "") {
                    // 		temp.push(k);
                    // 	}
                    // 	console.log($.trim($(k).text()));
                    // });

                    // $cols = temp;


                return $cols.map(function (j, col) {

                    var $col = $(col),

                        text = $col.text();

                    return text.trim();//text.replace('"', '""'); // escape double quotes



                }).get().join(tmpColDelim);



            }).get().join(tmpRowDelim)

                .split(tmpRowDelim).join(rowDelim)

                .split(tmpColDelim).join(colDelim),
            // Data URI

            csvData = 'data:application/csv;charset=utf-8,' + $.trim(encodeURIComponent(csv));



        $(this)

            .attr({

            'download': filename,

                'href': csvData,

                'target': '_blank'

        });
        
        $.ajax({

			type: "POST",

			url: $(this).data("action") + '/save_logs',

			data: { data: filename},

			cache: false

		}).done(function(){
			//console.log('tet');
		});

        ClearForm();

    }

    function ClearForm() {
    	$('#file_name').val('');
    	$('#video_ads option').attr('selected', false);
    	$('#video_ads_list li.vid-ads-item-selected').removeClass('vid-ads-item-selected');
    	$('#video_ads_list li').children('label').removeClass('glyphicon-ok');
    	$('.target_checkbox:checked').prop('checked', false);
    	$('#total_target_vid font').html('0');
    	$('#total_target_vid').hide();
    	setTimeout(location.reload(true), 400);
    }

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



	function loadvideoads(){

		var video_ads_url = $("#add_video_ads").val();

		$.ajax({

			type: "POST",

			url: $("#add_video_ad").data("action") + '/get_video_ads',

			data: { url: encodeURI(video_ads_url)},

			cache: false

		})

		.done(function( html ) {

			$("#video_ads").html(html);

			// $("#add_video_ads").val("");
			vidIdCollect = [];
			
			var vidOptions = $("#video_ads option");
			
			if(vidOptions.length > 0) {
				for(i=0;i<vidOptions.length;i++){
					vidIdCollect.push(vidOptions[i]);
				}
				getVideoTitle();
			}
			//console.log(html);

		});

	}
	
	var vidIdCollect = [];
	
	function getVideoTitle(){
		//Get Video Ad Details
		var option = vidIdCollect.pop();
		var vidId = $(option).val();
		//console.log(vidId);
		$.ajax({
		  url: $("#add_video_ad").data("action") + '/get_yt_key',
		  type:"GET",
		}).done(function( data ) {
			$.ajax({
			  url: 'https://www.googleapis.com/youtube/v3/videos?id='+vidId+'&part=snippet,statistics&key='+data,
			  type:"GET",
			  crossDomain: true,
			  dataType: 'json'
			}).done(function( data ) {
				//console.log(data);
			      if ( data.items.length > 0 ) {
				      $(option).attr('id', vidId);
				      AddVideoAdsUrl(vidId, $(option).html(), data.items[0]['snippet']['title'], data.items[0]['snippet']['thumbnails']['default']['url'], $(option).attr('selected'));
		
				      $(option).attr('data-title', data.items[0]['snippet']['title']);
				      if(vidIdCollect.length > 0) {
					      getVideoTitle();
				      }
			      }
		      
			}).fail(function(jqXHR, textStatus) {
				$('#campaign_error_modal').modal('show');
			});
		}).fail(function(jqXHR, textStatus) {
			/*
if(option != null) $(option).remove();

			if(vidIdCollect.length > 0) {
				getVideoTitle();
		    }
*/
		});	
	}

	$('#campaign-error-btn').click(function(){
		location.reload(true);
	});

	function setselected(){

		/*
$("#countries option").each(function(i){

			$(this).attr("selected","selected");

		});
*/



		// $("#language option").each(function(i){

		// 	$(this).attr("selected","selected");

		// });

	}
	
	
	function AddVideoAdsUrl (vidId, vidUrl, vidTitle, thumbnail, isSelected) {
        //var vidUrl = $('#add_video_ads').val();
        var videoAdsList = $('#video_ads_list');

        if(vidUrl != "") {
            var li = $('<li>');
            var button = $('<button class="btn btn-danger" type="button">x</button>');

            li.attr('data-option-id', vidId);
            li.addClass('video_ads_item');
            
            if(isSelected) li.addClass('vid-ads-item-selected');
            
            var selected = (isSelected) ? "glyphicon-ok" : "";

            li.html('<label class="glyphicon ' + selected + '" style="width: 20px;height: 20px;border: 2px solid #333;display: inline-block; vertical-align:middle; "></label><img src="' + thumbnail + '" style="margin-left:14px;display:inline-block" width="68" /><span class="glyphicon list-indent " style="width:70%; display:inline-block; font-size: 1em;">' + vidUrl + '</span>');

            button.bind('click', function () {
            	$('#' + $(this).parents('li').data('option-id') ).attr('selected', false);
                var thisli = $(this).parents('li');
                
                var vid_id = $(this).parents('li').data('option-id');
                //console.log(vid_id);
                
	                $.ajax({
	
						type: "POST",
			
						url: $("#add_video_ad").data("action") + '/remove_page',
			
						data: { vid_id: vid_id},
			
						cache: false
			
					})
	
					.done(function( html ) {
						$('#' + vid_id).remove();
						thisli.remove();
					});
              
            });

            
            button.appendTo(li);

            li.prependTo(videoAdsList);
            li.bind('click', function () {
                if($(this).hasClass('vid-ads-item-selected')) {
                	$('#' + $(this).data('option-id') ).attr('selected', false);
                	$(this).children('label').removeClass('glyphicon-ok');
                	$(this).children('label').addClass('list-indent');
                    $(this).removeClass('vid-ads-item-selected');
                }
                else {
                	$('#' + $(this).data('option-id') ).attr('selected', true);
                	$(this).children('label').addClass('glyphicon-ok');
                	$(this).children('label').removeClass('list-indent');
                    $(this).addClass('vid-ads-item-selected');
                }
                
                myValidate();
            });
        }
      }

      function GetVideoAdsUrl () {
        var urlList = [];

        $('#video_ads_list li.vid-ads-item-selected span').each(function(i, val){
            urlList.push($(val).html());
        });

        return urlList;
      }

    
      //error msg container
      $('#error-focus').bind('blur', function () {
      	ShowError(false);
      });

      //$('#btnExportCSV').css({padding:'12px 24px'});

      CreateMultipleCheckbox('language', 'language_count');
      CreateMultipleCheckbox('countries', 'country_count');

	$('#start_date').attr('data-date-value', $('#start_date').val());

	$('#start_date').datepicker({ 
		dateFormat: 'dd M yy',
		minDate: '0',
		onSelect: function(selected) {

			if(selected != $(this).data('date-value')){

				CheckDates('#start_date', selected);

				var minDate = new Date(Date.parse(selected));
	            minDate.setDate(new Date(selected).getDate() + 1);
	            
	            $( "#end_date" ).datepicker( "option", "minDate", minDate);

		    	//$("#end_date").datepicker("option","minDate", selected); //moment(selected).add(1, 'days')

		    	//console.log(end_date.data('date-value') + " f " + selected + " f " + moment(selected).add(1, 'days').format("DD MMM YYYY"));
		    	
		    	if($("#end_date").data('date-value') == selected) {
		    		
					CheckDates("#end_date", moment(selected).add(1, 'days').format("DD MMM YYYY") );
				}
			}
			else {

				CheckDates('#start_date', selected);
			}
	    }
	 });

	CheckDates('#start_date', $('#start_date').val());

	$('#end_date').attr('data-date-value', $('#end_date').val());

		

	$('#end_date').datepicker({ 
		dateFormat: 'dd M yy',
		minDate: '1',
		onSelect: function(selected) {

			CheckDates($('#end_date'), selected);
			//CheckDates($('#end_date'), $('#end_date').data('date-value'));
			var end_date = $('#end_date');
	    } 
	});

	CheckDates('#end_date', $('#end_date').val());

	$('.age_checkbox').each(function (i, v) {
		$(this).bind('click', function () { myValidate(); });
	});

	$('.gender_checkbox').each(function (i, v) {
		$(this).bind('click', function () { myValidate() });
	});

});

function CheckDates (id, _date) {

	var date = moment(new Date(_date)).calendar();

	if(moment(new Date(_date)).isValid()) {
		//console.log(date +  _date +" <--");
		if(date.indexOf("Today") == 0) {
			$(id).val("( Today )");
			
			$(id).data('date-value', _date);			
		}
		else if (date.indexOf("Tomorrow") == 0) {
			$(id).val("( Tomorrow )");
			
			$(id).data('date-value', _date);			
		}
		else {
			$(id).data('date-value', _date);
			$(id).val(_date);
		}

	}
}

function Validation () {

	if($('#file_name').val().trim() == "" ) {//target list
		$('#error-msg span').html('Campaign name is Invalid.');
		return true;
	}
	else if($('#display_url').val().trim() == "") {//target list
		$('#error-msg span').html('Campaign Display URL is Invalid.');
		return true;
	}
	else if($('#destination_url').val().trim() == "") {//target list
		$('#error-msg span').html('Campaign Destination URL is Invalid.');
		return true;
	}
	else if($('#video_ads option:checked').length == 0) {
		$('#error-msg span').html('You must select at least one Video Ad link.');
		return true;
	}
	else if ($('.target_checkbox:checked').length == 0) {//target list
		$('#error-msg span').html('You must select at least one target.');
		return true;
	}
	else if($('#mobile_bid_mdifier').val().trim() == "" || !$.isNumeric($('#mobile_bid_mdifier').val().trim())) {
		$('#error-msg span').html('Mobile Bid Modifier is Invalid.');
		return true;
	}
	else if($('#language option:selected').length == 0) {
		$('#error-msg span').html('You must select at least one Language');
		return true;
	}
	else if($('#countries option:selected').length == 0) {
		$('#error-msg span').html('You must select at least one Country');
		return true;
	}
	else if($('.age_checkbox:checked').length == 0) {
		$('#error-msg span').html('You must select at least one Age');
		return true;
	}
	else if($('.gender_checkbox:checked').length == 0) {
		$('#error-msg span').html('You must select at least one Gender');
		return true;
	}

	return false;
}


function validateURL(url, method){
	var website_regex = '';
	switch (method) {
		case 'dest':
			website_regex = /^(http:\/\/|http:\/\/www\.|https:\/\/|https:\/\/www\.)[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,3}(:[0-9]{1,5})?(\/.*)?$/;
		break;
		
		case 'disp':
			website_regex = /^(www\.)[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,3}(:[0-9]{1,5})?(\/.*)?$/;
		break;
	}
	
	return (url.match(website_regex)) ? RegExp.$1 : false;
}

function ytVidId(url) {
    var p = /^(?:https?:\/\/)?(?:www\.)?youtube\.com\/watch\?(?=.*v=((\w|-){11}))(?:\S+)?$/;
/*     var p = /(?:https?:\/{2})?(?:w{3}\.)?youtu(?:be)?\.(?:com|be)(?:\/watch\?v=|\/)([^\s&]+)/; */
    return (url.match(p)) ? RegExp.$1 : false;
}

function ShowError(isShow) {
	var top = (isShow) ? '0px' : '-42px';

	if(isShow) {
		$('#error-focus').focus().animate({'opacity': '0'}, 500);
	}

	$('#error-msg').animate({'margin-top': top}, 500);
}

function ShowSuccess ( msg ) {
	window.scrollTo(0,0);
	$('#success-msg').text(msg);
	$('#success-msg').show();
	
	setTimeout(function(){
		$('#success-msg').fadeOut();
		$('#success-msg').text('');
	}, 3000);
}

function CreateMultipleCheckbox (selectId, checkbox_count_id) {
	var selectObj = $('#' + selectId);

	selectObj.hide();

	var optionCollect = selectObj.children('option');

	var div = $('<div id="list-'+ selectId +'" style="height:300px; overflow-y:auto; "></div>');
	var clearBtn = $('<a href="javascript:void(0)" data-list-id="list-'+ selectId +'"> Clear All </a>')

	div.insertAfter(selectObj);
	clearBtn.insertBefore(div);

	clearBtn.bind('click', function () {
		var id = $(this).data('list-id');
		var checkboxCollect = $('#' + id + ' input:checked');

		//$('#' + id + '_count').html();

		$.each(checkboxCollect, function (i, v){
			//console.log($(v).parents('label'));
			SetCheckbox($(v).parents('label'), true);
			//.trigger('click');
		});

		myValidate();
	});

	$.each(optionCollect, function (k, v) {
		$(v).attr('id', selectId + '_' + k);
		var checked = ($(v).is(":selected")) ? 'checked' : '';
		var checkbox = $('<div class="checkbox"> <label class="list-'+ selectId +'" data-option-id="' + selectId + '_' + k + '" data-count-id="' + checkbox_count_id + '"><input type="checkbox" name="language" ' + checked + '/> ' + $(v).val() + '</label></div>');

		checkbox.appendTo(div);
		checkbox.children('label').bind('click', function () {
			SetCheckbox($(this), false);
			myValidate();
		});
	});


	$('#' + checkbox_count_id).html($('#list-'+ selectId).find('input:checked').length);
	
}

function SetCheckbox (label, isClearAll) {
	var isChecked = $(label).find('input').is(":checked");
	
	if(isClearAll) {
		$(label).find('input').attr('checked', false);

		$('#' + $(label).data('option-id')).attr('selected', false);
	}
	else {
		$(label).find('input').attr('checked', isChecked);

		$('#' + $(label).data('option-id')).attr('selected', isChecked);
	}


	var count = $('#' + $(label).attr('class')).find('input:checked').length;
	//console.log(count + " " +$(label).attr('class'));
	if(count==0) {
		$('#' + $(label).data('count-id')).css({color:'red'});
	}
	else {
		$('#' + $(label).data('count-id')).css({color:'inherit'});
	}

	$('#' + $(label).data('count-id')).html(count);
}

function campaigns_init () {

	$('#campaign_list').on('change',function(){
		if ( $(this).val() != 0 ) {
			get_campaign( $(this).val(), false );
		}
		else {
			location.reload();
		}
	});
	
	$('#file_name').on('change',function(){
		var value = $.trim( $(this).val() );
		
		if ( value != '' ) {
			$('#campaign_list option').each(function(){
				if ( $(this).text().toLowerCase() == value.toLowerCase() )  {
					$(this).prop('selected',true);
					get_campaign( $(this).val(), false );
				}
			});
			$('#saveCampaign').attr("disabled", false);
			$('#saveCampaign').removeClass('btn-default');
			$('#saveCampaign').addClass('btn-success');
			
			$('#file_name').parent().removeClass('has-error');
			$('#file_name').parent().addClass('has-success');
		}
		else {
			$('#saveCampaign').attr("disabled", true);
			$('#saveCampaign').removeClass('btn-success');
			$('#saveCampaign').addClass('btn-default');
			
			$('#file_name').parent().removeClass('has-success');
			$('#file_name').parent().addClass('has-error');
		}
		
	}).keypress(function (e) {
	    var allowedChars = new RegExp("^[a-zA-Z0-9\- ]+$");
	    var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
	    //console.log(allowedChars.test(str));
	    if (allowedChars.test(str)) {
	        return true;
	    }
	    e.preventDefault();
	    return false;
	}).keyup(function() {
	    // the addition, which whill check the value after a keyup (triggered by Ctrl+V)
	    // We take the same regex as for allowedChars, but we add ^ after the first bracket : it means "all character BUT these"
	    var forbiddenChars = new RegExp("[^a-zA-Z0-9\- ]", 'g');
	    if (forbiddenChars.test($(this).val())) {
	        $(this).val($(this).val().replace(forbiddenChars, ''));
	    }
	    
	    var value = $.trim( $(this).val() );
		
		if ( value != '' ) {
			$('#saveCampaign').attr("disabled", false);
			$('#saveCampaign').removeClass('btn-default');
			$('#saveCampaign').addClass('btn-success');
			
			
			$('#file_name').parent().removeClass('has-error');
			$('#file_name').parent().addClass('has-success');
		}
		else {
			$('#saveCampaign').attr("disabled", true);
			$('#saveCampaign').removeClass('btn-success');
			$('#saveCampaign').addClass('btn-default');
			
			$('#file_name').parent().removeClass('has-success');
			$('#file_name').parent().addClass('has-error');
		}
		myValidate();
	});
	
	$('#copy_campaign_btn').on('click', function(){
		$('#campaign_modal .modal-body p').text('Are you sure you want to copy this Campaign "'+$('#file_name').val()+'"?');
		$('#campaign_modal').modal('show');
		
		$('#campaign-delete-btn').hide();
		$('#campaign-copy-btn').show();
	});
	
	$('#campaign-copy-btn').on('click',function(){
		var action = $('#copy_campaign_btn').data("action");
		$.ajax({
			url: action,
			type:"POST",
			data: {
				id : $('#campaign_list').val()
			},
			dataType: 'json'
		}).done(function(data){
			//console.log(data);
			$('#campaign_modal').modal('hide');
			get_campaign(data, true);
		}).fail(function(ddd,aaa){
			
			console.log(ddd,aaa);
		});
	});
	
	$('#delete_campaign_btn').on('click', function(){
		$('#campaign_modal .modal-body p').text('Are you sure you want to delete this Campaign "'+$('#file_name').val()+'"?');
		$('#campaign_modal').modal('show');
		
		$('#campaign-copy-btn').hide();
		$('#campaign-delete-btn').show();
	});
	
	
	$('#campaign-delete-btn').on('click',function(){
		var action = $('#delete_campaign_btn').data("action");
		$.ajax({
			url: action,
			type:"POST",
			data: {
				id : $('#campaign_list').val()
			},
			dataType: 'json'
		}).done(function(data){
			location.reload();
		});
	});

	$('#display_url').on('change',function(){
		var value = $.trim( $(this).val() );
		if ( validateURL(value,'disp') ) {
			$('#display_url').parent().removeClass('has-error');
			$('#display_url').parent().addClass('has-success');
		}
		else {
			$('#display_url').parent().removeClass('has-success');
			$('#display_url').parent().addClass('has-error');
		}
		
	}).keyup(function(e) {
	   var value = $.trim( $(this).val() );
	   if ( validateURL(value,'disp') ) {
			$('#display_url').parent().removeClass('has-error');
			$('#display_url').parent().addClass('has-success');
	   }else{
			$('#display_url').parent().removeClass('has-success');
			$('#display_url').parent().addClass('has-error');
	   }
	   myValidate();
	});

	$('#destination_url').on('change',function(){
		var value = $.trim( $(this).val() );
		if ( value != '' && validateURL(value,'dest') ) {
			$('#destination_url').parent().removeClass('has-error');
			$('#destination_url').parent().addClass('has-success');
		}
		else {
			$('#destination_url').parent().removeClass('has-success');
			$('#destination_url').parent().addClass('has-error');
		}
		
	}).keyup(function(e) {
		var value = $.trim( $(this).val() );
	   if(validateURL(value,'dest')){
			$('#destination_url').parent().removeClass('has-error');
			$('#destination_url').parent().addClass('has-success');
	   }else{
			$('#destination_url').parent().removeClass('has-success');
			$('#destination_url').parent().addClass('has-error');
	   }
	   myValidate();
	});	
}

function get_campaign ( id, copy ) {

	show_loader ( 'Please wait...', true, 400 );
	$.ajax({
		url: $("#add_video_ad").data("action") + '/get_campaign',
		type:"POST",
		data: {
			id : id
		},
		dataType: 'json'
	}).done(function(data){
		//console.log(data);
		show_loader ( 'Done!', false, 400 );
		
		$('#file_name').val(data.name);
		$('#display_url').val(data.display_url);
		$('#destination_url').val(data.destination_url);
		$('#budget').val(data.daily_budget);
		$('#max_cpv').val(data.max_cpv);
		
		var video_ads = data.video_ads.split(',');
		$('#video_ads_list li.video_ads_item').each(function(){
			if ( jQuery.inArray($(this).data('option-id'), video_ads) >= 0 ) {
				$('#' + $(this).data('option-id') ).attr('selected', true);
				if ( !$(this).hasClass('vid-ads-item-selected') ) {
					$(this).addClass('vid-ads-item-selected');
                	$(this).children('label').addClass('glyphicon-ok');
                	$(this).children('label').removeClass('list-indent');
				}
			}
			else {
				$('#' + $(this).data('option-id') ).prop('selected', false);
            	$(this).children('label').removeClass('glyphicon-ok');
            	$(this).children('label').addClass('list-indent');
				$(this).removeClass('vid-ads-item-selected');
			}
		});
		
		var target_lists = data.target_lists.split(',');
		var total_list = 0;
		$('.target_checkbox').each(function(){
			if ( jQuery.inArray($(this).val(), target_lists) >= 0 ) {
				$(this).prop('checked', true);
				total_list += parseInt($(this).parents('label').data('count'));
			}
			else {
				$(this).prop('checked', false);
			}
		});
		$("#total_target_vid font").html(total_list);
		if (total_list > 0) {
			$("#total_target_vid").show();
		}

		var start_date = $.datepicker.formatDate('dd M yy', new Date(data.start_date));


		$('#start_date').datepicker( "option", "minDate", new Date(data.start_date) );
		$('#start_date').datepicker('setDate',start_date);
		$('#start_date').data('date-value',start_date);
		if ( data.end_date != '#N/A' ) {
			var end_date = $.datepicker.formatDate('dd M yy', new Date(data.end_date));
			$('#end_date').data('date-value',end_date);
			$('#end_date').datepicker('setDate',end_date);
		}
		
		var lang_list = data.language.split(',');
		$('#language option').each(function(){
			var lang_id = $(this).prop('id');
			if ( jQuery.inArray($(this).val(), lang_list) >= 0 ) {
				$(this).attr('selected', true);
				$('.list-language').each(function(){
					if ( $(this).data('option-id') == lang_id ) {
						$(this).find('input[type="checkbox"]').attr('checked', true);
					}
				});
			}
			else {
				$(this).attr('selected', false);
				$('.list-language').each(function(){
					if ( $(this).data('option-id') == lang_id ) {
						$(this).find('input[type="checkbox"]').attr('checked', false);
					}
				});
			}
		});
		var country_list = data.countries.split(',');
		$('#countries option').each(function(){
			var country_id = $(this).prop('id');
			if ( jQuery.inArray($(this).val(), country_list) >= 0 ) {
				$(this).attr('selected', true);
				$('.list-countries').each(function(){
					if ( $(this).data('option-id') == country_id ) {
						$(this).find('input[type="checkbox"]').attr('checked', true);
					}
				});
			}
			else {
				$(this).attr('selected', false);
				$('.list-countries').each(function(){
					if ( $(this).data('option-id') == country_id ) {
						$(this).find('input[type="checkbox"]').attr('checked', false);
					}
				});
			}
		});
		
		$('#language_count').html($('#language').val().length);
		$('#country_count').html($('#countries').val().length);
		
		$('#mbm-type').val(data.mbm_sign);
		$('#mobile_bid_mdifier').val(data.mbm_value);
		
		var age_lists = data.age.split(',');
		$('.age_checkbox').each(function(){
			if ( jQuery.inArray($(this).val(), age_lists) >= 0 ) {
				$(this).prop('checked', true);
			}
			else {
				$(this).prop('checked', false);
			}
		});
		
		var gender_lists = data.gender.split(',');
		$('.gender_checkbox').each(function(){
			if ( jQuery.inArray($(this).val(), gender_lists) >= 0 ) {
				$(this).prop('checked', true);
			}
			else {
				$(this).prop('checked', false);
			}
		});
		
		$('#delivery_method').val(data.delivery_method);

		
		$('#copy_campaign_btn').show();
		$('#delete_campaign_btn').show();
		
		if ( copy ) {
			var option = '<option value="'+data.id+'">'+data.name+'</option>';
			$('#campaign_list').append(option);
			$('#campaign_list').val(data.id);
		}
		
		$('#saveCampaign').attr("disabled", false);
		$('#saveCampaign').removeClass('btn-default');
		$('#saveCampaign').addClass('btn-success');
		myValidate();
	});
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

function validateYouTubeUrl( url ) {  
    if (url != undefined || url != '') {        
        var regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=|\?v=)([^#\&\?]*).*/;
        var match = url.match(regExp);
        if (match && match[2].length == 11) {
            // Do anything for being valid
            // if need to change the url to embed url then use below line            
            //$('#videoObject').attr('src', 'https://www.youtube.com/embed/' + match[2] + '?autoplay=1&enablejsapi=1');
            return match[2];
        } else {
            return false;
            // Do anything for not being valid
        }
    }
}

function myValidate () {
	if($('#file_name').val().trim() != "" &&
		$('#display_url').val().trim() != "" &&
		$('#destination_url').val().trim() != "" && 
		validateURL($('#display_url').val().trim(), 'disp') != "" &&
		validateURL($('#destination_url').val().trim(), 'dest') &&
		$('.vid-ads-item-selected').length > 0 &&
		$('.target_checkbox:checked').length > 0 &&
		($('#mobile_bid_mdifier').val().trim() != "" || $.isNumeric($('#mobile_bid_mdifier').val().trim())) &&
		$('#language option:selected').length > 0 &&
		$('#countries option:selected').length > 0 &&
		$('.age_checkbox:checked').length > 0 &&
		$('.gender_checkbox:checked').length > 0) {
			$('#btnExportCSV').attr("disabled", false);
			$('#btnExportCSV').removeClass('btn-default');
			$('#btnExportCSV').addClass('btn-primary');
			
			/*
$('#saveCampaign').attr("disabled", false);
			$('#saveCampaign').removeClass('btn-default');
			$('#saveCampaign').addClass('btn-success');
*/
			
			//console.log("enabled");
		}else{
			$('#btnExportCSV').attr("disabled", true);
			$('#btnExportCSV').removeClass('btn-primary');
			$('#btnExportCSV').addClass('btn-default');
			
			
			/*
$('#saveCampaign').attr("disabled", true);
			$('#saveCampaign').removeClass('btn-success');
			$('#saveCampaign').addClass('btn-default');
*/
			//console.log("disabled");
		}
		
	
	if ( $('#video_ads_list li.vid-ads-item-selected').length > 0 ) {
		$('#video_ads_list').css('border', '1px solid #3c763d');
	}
	else {
		$('#video_ads_list').css('border', '1px solid #843534');
	}
	
	if ( $('.target_checkbox:checked').length > 0 ) {
		$('#target_list').css('border', '1px solid #3c763d');
	}
	else {
		$('#target_list').css('border', '1px solid #843534');
	}
	
	if ( $('#file_name').val() != '' ) {
		$('#saveCampaign').attr("disabled", false);
		$('#saveCampaign').removeClass('btn-default');
		$('#saveCampaign').addClass('btn-success');
		
		$('#file_name').parent().removeClass('has-error');
		$('#file_name').parent().addClass('has-success');
	}
	else {
		$('#saveCampaign').attr("disabled", true);
		$('#saveCampaign').removeClass('btn-success');
		$('#saveCampaign').addClass('btn-default');
		
		$('#file_name').parent().removeClass('has-success');
		$('#file_name').parent().addClass('has-error');
	}
	
	if ( $('#display_url').val() != '' && validateURL($('#display_url').val(), 'disp') ) {
		$('#display_url').parent().removeClass('has-error');
		$('#display_url').parent().addClass('has-success');
	}
	else {
		$('#display_url').parent().removeClass('has-success');
		$('#display_url').parent().addClass('has-error');
	}
	
	if ( $('#destination_url').val() != '' && validateURL($('#destination_url').val(), 'dest') ) {
		$('#destination_url').parent().removeClass('has-error');
		$('#destination_url').parent().addClass('has-success');
	}
	else {
		$('#destination_url').parent().removeClass('has-success');
		$('#destination_url').parent().addClass('has-error');
	}
	
}
