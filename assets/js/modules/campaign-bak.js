$(document).ready(function(){
	loadvideoads();

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

		$.ajax({

			type: "POST",

			url: $(this).data("action") + '/insert_page',

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



	$('#btnExportCSV').click(function(event){

		var filename = $('#file_name').val();

		var video_ads = $('#video_ads').val();
		var video_ads_option = $('#video_ads option:selected');

		var disp_url = $('#display_url').val();

		var dest_url = $('#destination_url').val();

		var budget = $('#budget').val();

		var max_cpv = $('#max_cpv').val();

		var start_date = $('#start_date').val();
		
		var end_date = $('#end_date').val();
		if(end_date==""){
			end_date = "#N/A";
		}

		var delivery_method = $('#delivery_method').val();

		var mobile = $('#mobile_bid_mdifier').val();
		
		

		//Values in Array

		var lang = $('#language').val();

		var loc = $('#countries').val();

		var age = $('.age_checkbox:checked');

		var gender = $('.gender_checkbox:checked');


		console.log(lang);
		var targettr = $('#targeting_targets');
		var target_ads = $('#targeting_ads');
		var target_tg = $('#targeting_targettinggroups');
		
		var target_video_id = []; 
		$('#target_list option:selected').each(function(i, val){
		
			//Targeting Group Loop
			var target_tg_content = "<tr> <td> Add </td> <td> Enabled </td> <td>"+$(val).html()+"</td> <td>"+filename+"</td> <td>"+max_cpv+"</td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> </tr>";

					$(target_tg).after(target_tg_content);
			
		
			//Ads Loop
			for(i=0;i<video_ads.length;i++){

				if(video_ads[i]!=null){	
					console.log($(video_ads_option[i]).data('title'));			
					var target_ads_content = "<tr> <td> Add </td> <td> Enabled </td> <td>"+$(video_ads_option[i]).data('title')+"</td> <td>"+video_ads[i]+"</td> <td> default</td> <td>#N/A</td> <td> #N/A </td> <td>#N/A</td> <td>"+disp_url+"</td> <td>"+dest_url+"</td> <td>#N/A</td> <td>in-stream</td> <td>#N/A</td> <td>"+$(val).html()+"</td> <td>"+filename+"</td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> </tr>";

					$(target_ads).after(target_ads_content);

					}		

			}
			
			
            //Gender Loop
			for(i=0;i<gender.length;i++){

				if(gender[i]!=null){

					var target_g_content = "<tr> <td> Add </td> <td> Gender </td> <td> Enabled </td> <td> "+$(gender[i]).val()+"</td> <td>"+$(val).html()+"</td> <td align= 'center'> #N/A </td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td> </td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> </tr>";

					$(targettr).after(target_g_content);

				}		

			}


		//Age Loop
			for(i=0;i<age.length;i++){

				if(age[i]!=null){

						var target_a_content = "<tr> <td> Add </td> <td> Age </td> <td> Enabled </td> <td> "+$(age[i]).val()+"</td> <td>"+$(val).html()+"</td> <td align= 'center'> #N/A </td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td> </td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> </tr>";

						$(targettr).after(target_a_content);

				}		

			}
            
            //Target Loop
            var ytdata = $(val).data('ytdata');
            console.log($(this).html());
            $.each(ytdata,function(key,value){
	            console.log(value['link_url']);
	            var target_details_content = "<tr> <td> Add </td> <td> Youtube video</td> <td>Enabled</td> <td>"+value['link_url']+"</td> <td>"+$(val).html()+"</td> <td>#N/A</td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td> </td> <td></td> <td></td> <td></td> <td> </td> <td></td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> </tr>";

				$(targettr).after(target_details_content);
            });
        });
		
				
		var targetsettr = $('#targeting_settings');
		//Location Loop
		for(i=0;i<loc.length;i++){

			if(loc[i]!=null){



				var target_loc_content = "<tr> <td> Add </td> <td> Location </td> <td>"+loc[i]+"</td> <td> "+filename+"</td> <td> </td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> </tr>";

				$(targetsettr).after(target_loc_content);

			}		

		}

		//Language Loop
		for(i=0;i<lang.length;i++){

			if(lang[i]!=null){



				var target_lang_content = "<tr> <td> Add </td> <td> Language </td> <td>"+lang[i]+"</td> <td> "+filename+"</td> <td> </td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> <td> </td> </tr>";

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
			console.log(html);

		});

	}
	
	var vidIdCollect = [];
	
	function getVideoTitle(){
		//Get Video Ad Details
		var option = vidIdCollect.pop();
		var vidId = $(option).val();
		
		$.ajax({
		  url: "https://gdata.youtube.com/feeds/api/videos/"+vidId+"?v=2&alt=jsonc",
		  beforeSend: function( xhr ) {
		    xhr.overrideMimeType( "text/plain; charset=x-user-defined" );
		  }
		})
		  .done(function( data ) {
		    if ( console && console.log ) {
		      var _data = $.parseJSON(data);
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
	
	
	function AddVideoAdsUrl () {
        var vidUrl = $('#add_video_ads').val();
        var videoAdsList = $('#video_ads_list');

        if(vidUrl != "") {
            var li = $('<li>');
            var button = $('<button>');

            li.addClass('video_ads_item');
            li.html('<span>' + vidUrl + '</span>');

            button.addClass('btn-danger');
            button.html('x');
            button.bind('click', function () {
                $(this).parents('li').remove();
            });

            button.appendTo(li);

            li.appendTo(videoAdsList);
            li.bind('click', function () {
                if($(this).hasClass('vid-ads-item-selected')) {
                    $(this).removeClass('vid-ads-item-selected');
                }
                else {
                    $(this).addClass('vid-ads-item-selected');     
                }
                
            })
        }
      }

      function GetVideoAdsUrl () {
        var urlList = [];

        $('#video_ads_list li.vid-ads-item-selected span').each(function(i, val){
            urlList.push($(val).html());
        });

        return urlList;
      }

	

});