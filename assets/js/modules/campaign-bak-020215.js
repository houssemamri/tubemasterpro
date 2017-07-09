$(document).ready(function(){

myValidate();
$('#file_name, #video_ads, .target_checkbox, #mobile_bid_mdifier, #language, #countries, #display_url, #destination_url').change(myValidate);	
$('#video_ads').hide();
$('#video_ads_list').css({'margin-top':'10px', 'height':'220px', 'width':'520px', 'padding':'0px', 'list-style' : 'none', 'overflow-y' : 'auto', 'background-color' : '#eee' });

	loadvideoads();

	$('.target_checkbox').change(function(){
    	
        	
	            var add = parseInt($(this).parents('label').data('count')) * ((this.checked) ? 1 : -1);
	            var total = parseInt($("#total_target_vid font").html()) + add;
	            
	            if(total >= 500){
	            	 $('#total_target_vid font').css('color', 'red');
	            	 $('#btnExportCSV').attr("disabled",true);
	            	 $('#total_target_vid').show();
	            }
	            else if(total == 0) {
	            	$('#total_target_vid').hide();
	            }
	            else{
	            	$('#total_target_vid font').css('color', '#449d44');
	            	$('#btnExportCSV').attr("disabled",false);
	            	$('#total_target_vid').show();
	            }

	            $("#total_target_vid font").html(total);

				console.log(total);
    	
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

		if(!ytVidId(video_ads_url)) {
			//$('#add_video_ad').removeClass('btn-success').addClass('gray');  
			$('#error-msg span').html('Video Ad link is Invalid.');
			ShowError(true);

			return;
		}
		else {
			//$('#add_video_ad').removeClass('gray').addClass('btn-success');
		}

		$.ajax({

			type: "POST",

			url: $(this).data("action") + '/insert_page',

			data: { url: encodeURI(video_ads_url)},

			cache: false

		})

		.done(function( html ) {
			
			//$("#video_ads").prependTo($("#video_ads"));
			//$("#video_ads").html(html);

			$("#add_video_ads").val("");

			vidIdCollect = [];
			

			var option = $(html);
			var vidOptions = $("#video_ads option");
			
			if(option != null) {
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



	$('#btnExportCSV').bind('click', function(){

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


		if(Validation()) {
			ShowError(true);
			return false;
		}


		$('.target_checkbox:checked').each(function(y, val){
			console.log(val);
			var targeting_group ='';
			var targeting_group = $(val).parent('label').text().replace(/\s+\(.+?\)/g, '');
			//Targeting Group Loop
			var target_tg_content = "<tr> <td>Add</td> <td>Enabled</td> <td>"+targeting_group+"_"+y+"</td> <td>"+filename+"</td> <td>"+max_cpv+"</td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> </tr>";

					$(target_tg).after(target_tg_content);
			
		
			//Ads Loop
			for(i=0;i<video_ads.length;i++){

				if(video_ads[i]!=null){	
					console.log($(video_ads_option[i]).data('title'));			
					var target_ads_content = "<tr> <td>Add</td> <td>Enabled</td> <td>"+$(video_ads_option[i]).data('title')+"</td> <td>"+video_ads[i]+"</td> <td> default</td> <td>#N/A</td> <td> #N/A </td> <td>#N/A</td> <td>"+disp_url+"</td> <td>"+dest_url+"</td> <td>#N/A</td> <td>in-stream</td> <td>#N/A</td> <td>"+targeting_group+"_"+y+"</td> <td>"+filename+"</td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> </tr>";

					$(target_ads).after(target_ads_content);

					}		

			}
			
			
            //Gender Loop
			for(i=0;i<gender.length;i++){

				if(gender[i]!=null){

					var target_g_content = "<tr> <td>Add</td> <td>Gender</td> <td>Enabled</td> <td> "+$(gender[i]).val()+"</td> <td>"+targeting_group+"_"+y+"</td> <td align= 'center'> #N/A </td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td> </td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> </tr>";

					$(targettr).after(target_g_content);

				}		

			}


		//Age Loop
			for(i=0;i<age.length;i++){

				if(age[i]!=null){

						var target_a_content = "<tr> <td>Add</td> <td>Age</td> <td>Enabled</td> <td> "+$(age[i]).val()+"</td> <td>"+targeting_group+"_"+y+"</td> <td align= 'center'> #N/A </td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td> </td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> </tr>";

						$(targettr).after(target_a_content);

				}		

			}
            
            //Target Loop
            var ytdata = $(val).data('ytdata');
            console.log($(this).html());
            $.each(ytdata,function(key,value){
	            //console.log(value['link_url']);
	            var target_details_content = "<tr> <td>Add</td> <td>Youtube video</td> <td>Enabled</td> <td>"+value['link_url']+"</td> <td>"+targeting_group+"_"+y+"</td> <td>#N/A</td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td> </td> <td></td> <td></td> <td></td> <td> </td> <td></td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> </tr>";

				$(targettr).after(target_details_content);
            });
        });
		
				
		var targetsettr = $('#targeting_settings');
		//Location Loop
		for(i=0;i<loc.length;i++){

			if(loc[i]!=null){



				var target_loc_content = "<tr> <td>Add</td> <td>Location</td> <td>"+loc[i]+"</td> <td> "+filename+"</td> <td> </td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> </tr>";

				$(targetsettr).after(target_loc_content);

			}		

		}

		//Language Loop
		for(i=0;i<lang.length;i++){

			if(lang[i]!=null){



				var target_lang_content = "<tr> <td>Add</td> <td>Language</td> <td>"+lang[i]+"</td> <td> "+filename+"</td> <td> </td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> </tr>";

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



		 exportTableToCSV.apply(this, [$('#csv_table>table'), filename+'.txt']);

	});



	function exportTableToCSV($table, filename) {



        var $rows = $table.find('tr:has(td)'),



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



                return $cols.map(function (j, col) {

                    var $col = $(col),

                        text = $col.text();



                    return text.trim();//text.replace('"', '""'); // escape double quotes



                }).get().join(tmpColDelim);



            }).get().join(tmpRowDelim)

                .split(tmpRowDelim).join(rowDelim)

                .split(tmpColDelim).join(colDelim),



            // Data URI

            csvData = 'data:application/csv;charset=utf-8,' + encodeURIComponent(csv);



        $(this)

            .attr({

            'download': filename,

                'href': csvData,

                'target': '_blank'

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
    	setTimeout(location.reload(), 400);
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
		console.log(vidId);
		$.ajax({
		  url: "https://gdata.youtube.com/feeds/api/videos/"+vidId+"?v=2&alt=jsonc",
		  //type:"Post",
		  //url:$("#add_video_ad").data("action") + '/get_video_title',
		  //data:{vid_id:vidId},
		  beforeSend: function( xhr ) {
		    xhr.overrideMimeType( "text/plain; charset=x-user-defined" );
		  }
		})
		  .done(function( data ) {
		    if ( console && console.log ) {
		    
			  var _data = $.parseJSON(data);
		      
		      $(option).attr('id', vidId);
		      AddVideoAdsUrl(vidId, $(option).html(), _data.data['title'], _data.data.thumbnail['sqDefault']);

		      $(option).attr('data-title', _data.data['title']);
		      if(vidIdCollect.length > 0) {
			      getVideoTitle();

		      }
		    }
		});	
	}



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
	
	
	function AddVideoAdsUrl (vidId, vidUrl, vidTitle, thumbnail) {
        //var vidUrl = $('#add_video_ads').val();
        var videoAdsList = $('#video_ads_list');

        if(vidUrl != "") {
            var li = $('<li>');
            var button = $('<button class="btn btn-danger" type="button">x</button>');

            li.attr('data-option-id', vidId);
            li.addClass('video_ads_item');
            li.html('<label class="glyphicon" style="width: 20px;height: 20px;border: 2px solid #333;display: inline-block; vertical-align:middle; "></label><img src="' + thumbnail + '" style="margin-left:14px;display:inline-block" width="68" /><span class="glyphicon list-indent " style="width:70%; display:inline-block; font-size: 1em;">' + vidUrl + '</span>');

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

      $('#btnExportCSV').css({padding:'12px 24px'});

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
		console.log(date);
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

	if($('#file_name').val().trim() == "") {//target list
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

function ytVidId(url) {
    var p = /^(?:https?:\/\/)?(?:www\.)?youtube\.com\/watch\?(?=.*v=((\w|-){11}))(?:\S+)?$/;
/*     var p = /(?:https?:\/{2})?(?:w{3}\.)?youtu(?:be)?\.(?:com|be)(?:\/watch\?v=|\/)([^\s&]+)/; */
    return (url.match(p)) ? RegExp.$1 : false;
}

function ShowError(isShow) {
	var top = (isShow) ? '0px' : '-42px';

	if(isShow) {
		$('#error-focus').focus().animate({'opacity': '0'}, 500);;
	}

	$('#error-msg').animate({'margin-top': top}, 500);
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
	console.log(count + " " +$(label).attr('class'));
	if(count==0) {
		$('#' + $(label).data('count-id')).css({color:'red'});
	}
	else {
		$('#' + $(label).data('count-id')).css({color:'inherit'});
	}

	$('#' + $(label).data('count-id')).html(count);
}

function myValidate () {
	if($('#file_name').val().trim() != "" &&
		$('#display_url').val().trim() != "" &&
		$('#destination_url').val().trim() != "" &&
		$('.vid-ads-item-selected').length > 0 &&
		$('.target_checkbox:checked').length > 0 &&
		($('#mobile_bid_mdifier').val().trim() != "" || $.isNumeric($('#mobile_bid_mdifier').val().trim())) &&
		$('#language option:selected').length > 0 &&
		$('#countries option:selected').length > 0 &&
		$('.age_checkbox:checked').length > 0 &&
		$('.gender_checkbox:checked').length > 0) {
			$('#btnExportCSV').prop("disabled", false);
			$('#btnExportCSV').removeClass('btn-default');
			$('#btnExportCSV').addClass('btn-primary');
			
			console.log("enabled");
		}else{
			$('#btnExportCSV').prop("disabled", true);
			$('#btnExportCSV').removeClass('btn-primary');
			$('#btnExportCSV').addClass('btn-default');
			console.log("disabled");
		}
}