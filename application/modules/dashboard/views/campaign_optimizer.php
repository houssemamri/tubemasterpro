<input type="hidden" name="baseurl" id="baseurl" value="<?php echo site_url(''); ?>"/>
<input type="hidden" name="videoCount" id="videoCount" value=""/>
<h1>
    Campaign Optimizer
</h1>
<div id="main_optimizer_form">
<form id="optimizer_form" rel="async" action="" autocomplete="off">
  <div id="notification"></div>
  <!-- <p>Valid format: *.mp4, *.avi</p> -->
<p>
    <label for="file">Import file</label> <br />
    <input type="hidden" name="new_filename" value="" id="new_filename" class="form-control" /> 
    <input type="hidden" name="original_filename" value="" id="original_filename" class="form-control" /> 
    <div class="attach_video_file_notif"></div>
    <p class="attach_video_file"><input type="file" name="campaign_file" id="campaign_file" accept=".csv,.txt,.xls"/></p> 
                <div class="progress progress-sm progress-striped"  style="display:none;">
                <div class="progress progress-bar progress-bar-success" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 80%">
                <span class="sr-only">0% Complete</span>

                </div>
              </div>
              <p class="text-center cancel_button" style="display:none;"><button type="button" class="btn btn-warning btn-xs" id="cancel_upload">Cancel Upload</button></p>

   <p style="clear:both;"></p>
</p>
</form>
</div>

<div id="main_optimizer_table" style="display:none" class="col-sm-12">
<div class="row"  style="background-color: rgb(224, 224, 224);padding: 10px;border: 1px solid #e0e0e0;">
	<div class="col-sm-6">
		<button class="btn btn-primary btn-md" type="button" id="btnOptimizerCount" style="width:80px" disabled>0</button>
	</div>
	<div class="col-sm-6">
		<div class="col-sm-4"></div>
		<div class="col-sm-4"></div>
		<div class="col-sm-4"><button class="btn btn-default" type="button" id="btnOptimizerReplace" disabled>Replace</button></div>
	</div>
</div>
<div class="containerTable">
	<table class="table table-bordered dataTable" id="Optimizer_Data_table" role="grid">
		<thead>
			<tr role="row">
				<th rowspan="1" colspan="1" aria-label="" style="width: 12px;">
					<input class="video_check_all" type="checkbox">
				</th>
				<th style="text-align: center; width: 125px;" class="sorting_disabled" rowspan="1" colspan="1" aria-label="Delete Selected">
				</th>
				<th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label="Title: activate to sort column ascending" style="width: 235px;">Title</th>
				<th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label="Views: activate to sort column ascending" style="width: 40px;">Views</th>
				<th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label="Likes: activate to sort column ascending" style="width: 36px;">Likes</th>
				<th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label="Dislikes: activate to sort column ascending" style="width: 53px;">Rating</th>
				<th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label="Comments: activate to sort column ascending" style="width: 74px;">Rating Count</th>
			</tr>
		</thead>
		<tbody>
        	
        </tbody>
	</table>
</div>
</div>
<div id="optimizer_targets" style="display:none" class="col-sm-12">
	<div class="row"  style="background-color: rgb(224, 224, 224);padding: 10px;border: 1px solid #e0e0e0;margin-bottom:5px">
		<div class="col-sm-6">
			<button class="btn btn-primary btn-md" type="button" id="btnVideoCount" style="width:80px" disabled>0</button>
		</div>
		<div class="col-sm-6">
			<div class="col-sm-4"></div>
			<div class="col-sm-4"></div>
			<div class="col-sm-4">
				<!--button class="btn btn-primary" type="button" id="btnOptimizerImport">Export</button-->
				<a class="btn btn-default" type="button" id="btnOptimizerImport" disabled>Export</a>
			</div>
		</div>
	</div>
<div id="target_lists" class="col-sm-12" style="padding-bottom: 20px;">
</div>
</div>
<div id="optimize_export_table" style="display:none">
<table border="0" cellpadding="0" cellspacing="0" width="9713" style="border-collapse:collapse;table-layout:fixed">
<tr class="table-data">
  <td>Input targets here</td>
  <td colspan="55">"Campaign, locale=en_US"</td>
 </tr>
 <tr id="targeting_targets" class="table-data">
  <td>Action</td>
  <td>Type</td>
  <td>Status</td>
  <td>Target</td>
  <td>Targeting group</td>
  <td>Max CPV</td>
  <td colspan="50"></td>
 </tr>
 <tr id="targeting_add" class="table-data">
 </tr>
</table>
</div>

<script>
    document.querySelector('#campaign_file').addEventListener('change', function(e) {
        var baseurl     = $("#baseurl").val();
        var cancel_upload = document.getElementById('cancel_upload');
        var file_name = $("#campaign_file").val();
        var file = this.files[0];

		
      if(file_name == ''){
        return;
      }
      var fd = new FormData();
      fd.append("campaign_file", file);

      var xhr = new XMLHttpRequest();
      xhr.open('POST', baseurl + 'dashboard/optimizer_ajax/upload_file', true);
     
      function detach() {
            // remove listeners after they become irrelevant
        cancel_upload.removeEventListener('click', canceling, false);
      }
     function canceling() {
          detach();
          xhr.abort();
          console.log('Cancel upload');
          disable_progress_disaply();
      }

      xhr.upload.onprogress = function(e) {
        if (e.lengthComputable) {
          var percentComplete = (e.loaded / e.total) * 100;

          $(".progress").css('display' , 'block');
          $(".cancel_button").css('display' , 'block');
          
          $(".progress-bar").attr('aria-valuenow' , percentComplete);
          $(".progress-bar").css('width' , percentComplete +'%');
          $(".progress-bar").text(Math.round(percentComplete) + "%");
          console.log(percentComplete + '% uploaded');
        }
      };


      xhr.onload = function() {
        if (this.status == 200) {
          setTimeout("disable_progress_disaply()", 1500);
          var resp = JSON.parse(this.response);
          console.log('response raw:', baseurl);
          console.log('Server got:', resp);

            if(resp.error == 1){
                  $(".attach_video_file_notif").html("<div class=\"alert alert-danger\" role=\"alert\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button> " + resp.error_msg +"</div>");
                  //$('#campaign_file').reset();
            }else{
                  $("#original_filename").val(resp.orig_filename);
                  $("#new_filename").val(resp.filename);
                  $("#campaign_file").val("");
                  $('#main_optimizer_form').hide();
                  $('#main_optimizer_table').show();
				  
              var res = resp.parse_data.replace(/&quot;/g,'"').replace(/&#13;/g,'');
    				  var startIndex = res.lastIndexOf('{"Input targets here": {');
    				  var lastIndex = res.lastIndexOf('{"Input targets here (targeting group negatives)": {');
    				  var content = res.slice(startIndex, lastIndex);
    				  var content2 = content.split('\"data\"')[1];
    				  
    				  trim_content1 = "{\"data\""+ content2;
					  trim_content1 = $.trim(trim_content1);
    				  var trim_content = trim_content1.slice(0,trim_content1.length - 1);
    				  
    				  console.log(trim_content);
    				  var t = $.parseJSON(trim_content);
    				  var optviddata = t.data;
    				  targetinggroup = optviddata[0]['Targeting group'];
    				  vids_result = 0;
    				  ajax_result = t.data.length;
    				  
    				  show_loader('Loading Videos...',true,100);
    				  $.each(t.data, function(i,k){
    					  //console.log(i+" " + k.Type);
    					if(k.Type == 'YouTube video'){
    						vids_result++;
    			
    						//console.log(k.Target);
    						vidTargetCollect.push(k.Target);
    						vidTotalCollect.push(k.Target);	    												
						}
    				  });
    				  
    				  $("#btnOptimizerCount").html(vids_result);
    				  
    				  if(vidTargetCollect.length > 0) {
	    				  setVideoDetails(vidTargetCollect.length);
    				  }
    				  
            }
             
        }
      };

      xhr.send(fd);
	  
        // and, of course, cancel if "Cancel" is clicked
        cancel_upload.addEventListener('click', canceling, false);

    }, false);
    
    var targetTotalCollect = [];
    var vidTotalCollect = [];
    var vidTargetCollect = [];
    var vids_to_remove = []; 
    var vidIDCollect = [];
    var globalId;
    var vidTargeID = [];
    var targetinggroup ='';
	
	var tableCollectClass = function () {
		this.vid_add = [];
	};
	
	var tableCollect = {};
	var totalVidsCount = 0;     
    
    function setVideoDetails(vidTargetCollectCount) {
   		//var vidID = vidIDCollect.pop();
   		var vidTarget = vidTargetCollect.pop();
    	
    	//getVideoDetails(vidID);
    	if(vidTarget != null) getVideoDetails(vidTarget);
    		
    	if(vidTargetCollect.length > 0) {
			setTimeout(function(){ setVideoDetails(vidTargetCollectCount); }, 500);
	    }else{
	    	//console.log("check: "+$('#Optimizer_Data_table tbody tr').length + " " + vidTargetCollectCount);
		    if($('#Optimizer_Data_table tbody tr').length < vidTargetCollectCount) {
			    setTimeout(function(){ setVideoDetails(vidTargetCollectCount); }, 500);
			    return false;
		    }
		    
		    $('.co_ytcheckbox').change(function(){	
	            var add = 1 * ((this.checked) ? -1 : 1);
	            var total = parseInt($("#btnOptimizerCount").html()) + add;
	            
	            if(total >= 500){
	            	 $('#btnOptimizerCount').css('color', 'red');
	            }
	            else{
	            	$('#btnOptimizerCount').css('color', '#ffffff');
	            }

	            $("#btnOptimizerCount").html(total);
	            
			});
			
			show_loader ( '',false,100 );
			
			$('#Optimizer_Data_table .co_ytcheckbox').each(function(){
				$(this).click(function(){
				vids_to_remove=[];
					 $('#Optimizer_Data_table .co_ytcheckbox:checked').each(function(i,j){
						vids_to_remove.push($(j).parents('tr').data('video-id'));
					});
					//console.log(vids_to_remove);
					
					//Enable Disabl Replace Button
					if(vids_to_remove.length > 0){
		            $('#btnOptimizerReplace').prop('disabled',false);
		            $('#btnOptimizerReplace').addClass('btn-primary');
		            $('#btnOptimizerReplace').removeClass('btn-default');
	            }else{
		            $('#btnOptimizerReplace').prop('disabled',true);
		            $('#btnOptimizerReplace').removeClass('btn-primary');
		            $('#btnOptimizerReplace').addClass('btn-default');
	            }	
	            
				});
			});
			
			$('.video_check_all').change(function() {
			    var checkboxes = $('.co_ytcheckbox');
			    if($(this).is(':checked')) {
			        checkboxes.prop('checked', true);
			        var total = 0;
			    } else {
			        checkboxes.prop('checked', false);
			        var total = vidTotalCollect.length;
			    } 
			    						
			    vids_to_remove=[];
				 $('#Optimizer_Data_table .co_ytcheckbox:checked').each(function(i,j){
					vids_to_remove.push($(j).parents('tr').data('video-id'));	
				});
				
				//console.log(vids_to_remove);
				if(vids_to_remove.length > 0){
		            $('#btnOptimizerReplace').prop('disabled',false);
		            $('#btnOptimizerReplace').addClass('btn-primary');
		            $('#btnOptimizerReplace').removeClass('btn-default');
	            }else{
		            $('#btnOptimizerReplace').prop('disabled',true);
		            $('#btnOptimizerReplace').removeClass('btn-primary');
		            $('#btnOptimizerReplace').addClass('btn-default');
	            }
	            
	            $("#btnOptimizerCount").html(total);
			});
			
				$('#btnOptimizerReplace').click(function(){
					$.ajax({

						type: "POST",
			
						url: $('#baseurl').val() + 'dashboard/optimizer_ajax/get_target_list',
			
						data: { url: "fafas"},
			
						cache: false
			
					}).done(function(data) {
						
					  var _data = $.parseJSON(data);	
					  var videoCount = $("#btnOptimizerCount").html();
					  $("#Optimizer_Data_table>tbody").html("");
					  $('#main_optimizer_table').hide();
					  $('#optimizer_targets').show();
					  $('#btnVideoCount').html(videoCount);
					  var target_video_list = [];
					  //console.log(_data.target_data);
					  globalId = _data.target_data;
					  for(i=0;i<globalId.length;i++){
					  
					  	if(i==0) console.log($.map(globalId[i].data[0], function(el) { return el; }).join(", "));
					  	vidTargeID = [];
					  	for(x=0;x<globalId[i].data.length;x++){
						  	if($.inArray(globalId[i].data[x].ytid, vidTargeID) == -1) vidTargeID.push(globalId[i].data[x].ytid);
					  	}
					  	
					  	for(x=0;x<vidIDCollect.length;x++){
						  	if($.inArray(vidIDCollect[x], vidTargeID) == -1) vidTargeID.push(globalId[i].data[x].ytid);
					  	}
					  
					  	var optimizer_target_lists = $("<div class=\"row row-target opti_target\" id=\"otdiv_id_"+i+"\" data-targetvid-ids=\""+vidTargeID.join(',')+"\" style=\"height:50px;padding:15px;cursor:pointer\">"+_data.target_data[i].name+" ("+vidTargeID.length+")</div><div class=\"target_videos\"style=\"display:none\"><div class=\"containerTable\"><table class=\"table table-bordered dataTable\" id=\"Optimizer_Video_table_"+i+"\" role=\"grid\"><thead><tr role=\"row\"><th rowspan=\"1\" colspan=\"1\" aria-label=\"\" style=\"width: 12px;\"><input data-id=\""+i+"\" class=\"target_check_all\" type=\"checkbox\"></th><th style=\"text-align: center; width: 125px;\" class=\"sorting_disabled\" rowspan=\"1\" colspan=\"1\"></th><th class=\"sorting\" tabindex=\"0\" aria-controls=\"DataTables_Table_0\" rowspan=\"1\" colspan=\"1\" aria-label=\"Title: activate to sort column ascending\" style=\"width: 235px;\">Title</th><th class=\"sorting\" tabindex=\"0\" aria-controls=\"DataTables_Table_0\" rowspan=\"1\" colspan=\"1\" aria-label=\"Views: activate to sort column ascending\" style=\"width: 40px;\">Views</th><th class=\"sorting\" tabindex=\"0\" aria-controls=\"DataTables_Table_0\" rowspan=\"1\" colspan=\"1\" aria-label=\"Likes: activate to sort column ascending\" style=\"width: 36px;\">Likes</th><th class=\"sorting\" tabindex=\"0\" aria-controls=\"DataTables_Table_0\" rowspan=\"1\" colspan=\"1\" aria-label=\"Dislikes: activate to sort column ascending\" style=\"width: 53px;\">Rating</th><th class=\"sorting\" tabindex=\"0\" aria-controls=\"DataTables_Table_0\" rowspan=\"1\" colspan=\"1\" aria-label=\"Comments: activate to sort column ascending\" style=\"width: 74px;\">Rating Count</th></tr></thead><tbody></tbody></table></div></div>");
				(optimizer_target_lists).appendTo($("#target_lists"));
					  
				}
				
				
				$('.opti_target').each(function(i,j){
					$(this).click(function(){
					
						if($(this).next('div').find('table>tbody tr').length == 0){
													
							targetVidCollect = [];
							targetTotalCollect = [];
							
							//$('.target_videos table>tbody').html('');
							//$('.target_videos').hide();
							$(this).next("div").show();
							var vidTargetID = $(this).data('targetvid-ids').split(',');
							
							show_loader("Loading Videos...",true,100);
							
							for( x = 0;x<vidTargetID.length; x++ ) {
								//getVideoDetailsforTarget(vidTargetID[x]);
								if($.inArray(vidTargetID[x], targetVidCollect) == -1){
									targetVidCollect.push(vidTargetID[x]);
									//targetTotalCollect.push(vidTargetID[x]);
								} 
							}
								//console.log(targetVidCollect);
								showTargetList (targetVidCollect.length, i);
						}else{
							$(this).next('div').slideToggle();
						}
	
				  });  
				});
					  			 						
				  	setTimeout(function () {
				  	
						$('#btnOptimizerImport').bind('click',function(){
							$(this).attr('disabled', true);
							//console.log("sulit");
							var filename = 'OptimizeUpdate';
							var targettr = $('#targeting_targets');	
							var targettradd = $('#targeting_add');
							//Target Loop
				            $.each(vids_to_remove,function(key,value){
					            //console.log(value['link_url']);
					            var target_details_content = "<tr class=\"table-data\"> <td>Remove</td> <td>Youtube video</td> <td>Enabled</td> <td>http://www.youtube.com/watch?v="+value+"</td> <td>"+targetinggroup+"</td> <td>#N/A</td> <td colspan=\"50\"></td> </tr>";
				
									$(targettr).after(target_details_content);
							});
							
							$.each(GetMergedTargetVidCollect(),function(key,value){
					            //console.log(value['link_url']);
					            var target_details_content = "<tr class=\"table-data\"> <td>Add</td> <td>Youtube video</td> <td>Enabled</td> <td>http://www.youtube.com/watch?v="+value+"</td> <td>"+targetinggroup+"</td> <td>#N/A</td> <td colspan=\"50\"></td> </tr>";
				
									$(targettradd).after(target_details_content);
							});
								show_loader('Exporting...',true,100);
								exportTableToCSV.apply(this, [$('#optimize_export_table>table'), filename+'.csv']);
				
						});				  	
				  	
				  		totalVidsCount = parseInt($('#btnVideoCount').html().trim());
						$('#target_lists').parent('div').css({'padding':'0px'});	
					}, 1000);
					
					})
				});
				 			    
	    }
    }
	
 
	
	function showTargetList (targetVidCollectCount, tableId) {   
		var id = targetVidCollect.pop(); 
		
		if(id != null) getVideoDetailsforTarget(id, tableId);      
	   
	   if (targetVidCollect.length > 0) {  
		   setTimeout(function () {    
		      	 showTargetList(targetVidCollectCount, tableId);	                                                     
		   }, 500);
	   }
	   else {
	   
	    	//console.log("check: " + tableId + " " + $('#Optimizer_Video_table_' + tableId + ' tbody tr').length + " " + targetVidCollectCount);
		    if($('#Optimizer_Video_table_' + tableId + ' tbody tr').length < targetVidCollectCount) {
			    setTimeout(function(){ showTargetList(targetVidCollectCount, tableId); }, 500);
			    return false;
		    }	   
	   
			$('#Optimizer_Video_table_'+tableId).DataTable({
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
				null
				]
			});
			
/*
			$('.tv_ytcheckbox').change(function(){	
	            var add = 1 * ((this.checked) ? 1 : -1);
	            var total = parseInt($("#btnVideoCount").html()) + add;
	            
	            if(total >= 500){
	            	 $('#btnVideoCount').css('color', 'red');
	            }
	            else{
	            	$('#btnVideoCount').css('color', '#ffffff');
	            }

	            $("#btnVideoCount").html(total);

				//console.log(total);
			
			});
*/
			

			
			//$('#Optimizer_Video_table_'+tableId +' .target_check_all').each(function(i,j){
			tableCollect[""+tableId] = new tableCollectClass();
			tableCollect[""+tableId].vid_add = [];
			//});
			
			$('#Optimizer_Video_table_'+tableId +' .target_check_all').click(function() {
			    var checkboxes = $(this).parents('table').find('.tv_ytcheckbox');
			    if($(this).is(':checked')) {
			        checkboxes.prop('checked', true);
			        var add = targetTotalCollect.length * 1;
			    } else {
			        checkboxes.prop('checked', false);
			        var add = targetTotalCollect.length * -1;   
			    }
			    
			    tableCollect[""+tableId].vid_add = [];
			    
				 $('#Optimizer_Video_table_'+tableId+' .tv_ytcheckbox:checked').each(function(i,j){
					tableCollect[""+tableId].vid_add.push($(j).parents('tr').data('video-id'));
				});
				
				//var total = parseInt($("#btnVideoCount").html()) + add;
				 $("#btnVideoCount").html(totalVidsCount + CountTargetVid());
				
				//console.log(vids_to_add);
				if(totalVidsCount + CountTargetVid() >= 500){
					$('#btnOptimizerImport').attr('disabled',true);
					$('#btnOptimizerImport').addClass('btn-default');
					$('#btnOptimizerImport').removeClass('btn-primary');
				}else{
					$('#btnOptimizerImport').attr('disabled',false);
					$('#btnOptimizerImport').removeClass('btn-default');
					$('#btnOptimizerImport').addClass('btn-primary');
				}

			});
			
			$('#Optimizer_Video_table_'+tableId+' .tv_ytcheckbox').each(function(){
				$(this).click(function(){
				tableCollect[""+tableId].vid_add = []; //#Optimizer_Video_table_'+tableId+' 
					 $('#Optimizer_Video_table_'+tableId+' .tv_ytcheckbox:checked').each(function(i,j){
						tableCollect[""+tableId].vid_add.push($(j).parents('tr').data('video-id'));
					});
					//console.log(tableId + " " + tableCollect[""+tableId].vid_add);
				$("#btnVideoCount").html(totalVidsCount + CountTargetVid());	
					
				if(totalVidsCount + CountTargetVid() >= 500){
					$('#btnOptimizerImport').attr('disabled',true);
					$('#btnOptimizerImport').addClass('btn-default');
					$('#btnOptimizerImport').removeClass('btn-primary');
				}else{
					$('#btnOptimizerImport').attr('disabled',false);
					$('#btnOptimizerImport').removeClass('btn-default');
					$('#btnOptimizerImport').addClass('btn-primary');	
				}
					
				});
			});
			
			show_loader("",false,100);		
	   }
	}
	
	function CountTargetVid () {
		var totalCount = 0;
		$.each(tableCollect, function(i,j){
			totalCount += j.vid_add.length;
		});
		
		return totalCount;
	}
	
	function GetMergedTargetVidCollect () {
		var arr = [];
		
		$.each(tableCollect, function(i,j){
			arr = $.merge(arr, j.vid_add);
		});
		
		//console.log("to add : " + arr.length);
		
		return arr;
		
	}
   
var vids_result = 0;
var ajax_result = 0;
var vids_to_add = [];
var vids_to_remove = [];

	function disable_progress_disaply(){
	    $(".progress-bar").css('width' ,  '0%');
	    $(".progress").css('display' , 'none');
	    $(".cancel_button").css('display' , 'none');
	}
	
	function remove_uploaded_video(){
	   $(".attach_video_file_notif").css('display' , 'none');
	   $(".attach_video_file").css('display' , 'block');
	   $("#original_filename").val('');
	   $("#new_filename").val('');    
	}
	
	//Display Video List from File
	function getVideoDetails(vidTarget){

		var vidID_init = vidTarget.split('youtube.com/yt_')[1];
    	var vidId = vidID_init.slice(0,11);
    	//console.log(vidId);

		//Get Video Ad Details
		$.ajax({
		  url: "https://gdata.youtube.com/feeds/api/videos/"+vidId+"?v=2&alt=jsonc",
		  beforeSend: function( xhr ) {
		    xhr.overrideMimeType( "text/plain; charset=x-user-defined" );
		  }
		})
		  .done(function( data ) {
		    if ( console && console.log ) {
		    
			  var _data = $.parseJSON(data);
		      
			  var campaign_video_details = $("<tr data-video-id=\""+vidTarget+"\"><td><input type=\"checkbox\" class=\"co_ytcheckbox\" name=\"co_ytcheckbox\"></td><td style=\"text-align:center;\"><img alt=\""+_data.data['title']+"\" src=\""+_data.data.thumbnail['sqDefault']+"\" width=\"50px\" height=\"28px\"></td><td><a class=\"video_link\" target=\"_blank\" href=\"https://www.youtube.com/watch?v="+vidId+"\">"+_data.data['title']+"</a></td><td>"+numberWithCommas(_data.data['viewCount'])+"</td><td>"+numberWithCommas(_data.data['likeCount'])+"</td><td>"+parseFloat(_data.data['rating']).toFixed(1)+"</td><td>"+numberWithCommas(_data.data['ratingCount'])+"</td></tr>");
			  
			  (campaign_video_details).appendTo($("#Optimizer_Data_table>tbody"));
		      ajax_result--;
		      var final_result = ajax_result - vids_result;
		     
		      if ( final_result == 0 ) {
			      $('#Optimizer_Data_table').DataTable({
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
						    null
						]
					});
		      }
		    }
		}).fail(function(data) {

			
			var campaign_video_details = $("<tr data-video-id=\""+vidId+"\"><td><input type=\"checkbox\" class=\"co_ytcheckbox\" name=\"co_ytcheckbox\"></td><td style=\"text-align:center;\"><span>#N/A</span></td><td><a class=\"video_link\" target=\"_blank\" href=\"https://www.youtube.com/watch?v="+vidId+"\">"+"https://www.youtube.com/watch?v="+vidId+"</a></td><td><span>#N/A</span></td><td><span>#N/A</span></td><td><span>#N/A</span></td><td><span>#N/A</span></td></tr>");
						 
			  (campaign_video_details).appendTo($("#Optimizer_Data_table>tbody"));
		      ajax_result--;
		      var final_result = ajax_result - vids_result;
		      
		      if ( final_result == 0 ) {
			      $('#Optimizer_Data_table').DataTable({
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
						    null
						]
					});
		      }
			
			
		});	
	}
	
	function getVideoDetailsforTarget(vidId, tableId){
		//Get Video Ad Details
		$.ajax({
		  url: "https://gdata.youtube.com/feeds/api/videos/"+vidId+"?v=2&alt=jsonc",
		  beforeSend: function( xhr ) {
		    xhr.overrideMimeType( "text/plain; charset=x-user-defined" );
		  }
		})
		  .done(function( data ) {
		    if ( console && console.log ) {
		    
			  var _data = $.parseJSON(data);
		      
			  var campaign_video_details = $("<tr data-video-id=\""+vidId+"\"><td><input type=\"checkbox\" class=\"tv_ytcheckbox\" name=\"tv_ytcheckbox\"></td><td style=\"text-align:center;\"><img alt=\""+_data.data['title']+"\" src=\""+_data.data.thumbnail['sqDefault']+"\" width=\"50px\" height=\"28px\"></td><td><a class=\"video_link\" target=\"_blank\" href=\"https://www.youtube.com/watch?v="+vidId+"\">"+_data.data['title']+"</a></td><td>"+numberWithCommas(_data.data['viewCount'])+"</td><td>"+numberWithCommas(_data.data['likeCount'])+"</td><td>"+parseFloat(_data.data['rating']).toFixed(1)+"</td><td>"+numberWithCommas(_data.data['ratingCount'])+"</td></tr>");
			  
			  (campaign_video_details).appendTo($("#Optimizer_Video_table_"+tableId+">tbody"));
		      ajax_result--;
		      var final_result = ajax_result - vids_result;
		      if ( final_result == 0 ) {

		      }
		    }
		}).fail(function(data) {
			
			
			var campaign_video_details = $("<tr data-video-id=\""+vidId+"\"><td><input type=\"checkbox\" class=\"tv_ytcheckbox\" name=\"tv_ytcheckbox\"></td><td style=\"text-align:center;\"><span>#N/A</span></td><td><a class=\"video_link\" target=\"_blank\" href=\"https://www.youtube.com/watch?v="+vidId+"\">"+"https://www.youtube.com/watch?v="+vidId+"</a></td><td><span>#N/A</span></td><td><span>#N/A</span></td><td><span>#N/A</span></td><td><span>#N/A</span></td></tr>");
						 
			  (campaign_video_details).appendTo($("#Optimizer_Video_table_"+tableId+">tbody"));
		      ajax_result--;
		      var final_result = ajax_result - vids_result;
		     
		      if ( final_result == 0 ) {

		      }
			
			
		});	
	}
	
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


        $(this).attr({
            'download': filename,
            'href': csvData,
			'target': '_blank'
        });

        //ClearForm();
        setTimeout(location.reload(), 400);

    }
    
    function numberWithCommas(x) {
	    	//return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
	    	return (x+'').replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,");  		
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
</script>
