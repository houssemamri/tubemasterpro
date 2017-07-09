var baseurl     = $("#baseurl").val();
var enable_submit_button = 0;
//$('.dataTables_filter input[type="search"]').attr('placeholder','Type in customer name, date or amount').css({'width':'250px','display':'inline-block'});
$('#create-optimized-campaign-list').dataTable({
		"order": [[ 1, "desc" ]],
		"columns": [
			 { "orderable": false },
		    null,
		    null,
		    { "orderable": false }
		],
		'bPaginate' : $("#create-optimized-campaign-list").find('tbody tr').length > 10
});

$("#overview_form_create").ready(function(e){
	 
	$('#SaveOverview').attr('class','btn btn-default'); 
	$('#SaveOverview').addClass('disabled');
	var checked = [];

	$("#optimized-campaign-table input[type=checkbox]").on('click', function(){
		
		var is_checked2 = $("#optimized-campaign-table input[type=checkbox]:checked").length;
		enable_submit_button(1,is_checked2);
	});
	

	$("#overview_name").keyup(function(e){
		var overview_name = $("#overview_name").val();

		var is_checked = $("#optimized-campaign-table input[type=checkbox]:checked").length;

		if(overview_name.length < 5){
	//class="alert alert-success"
			$('#notification').attr('class','alert alert-danger'); 
			$('#notification').css('display','block'); 
			$('#notification').text("Overview name at least 5 characters"); 
			enable_submit_button(0,is_checked);
		}else{
			enable_submit_button(1,is_checked);
		}
	})


	function enable_submit_button(type_button,checked_length){

		if(type_button == 0){
			$('#SaveOverview').attr('class','btn btn-default'); 
			$('#SaveOverview').addClass('disabled');
			$('#optimized-campaign-table').css('display','none');
		}else{
			if(checked_length > 0){
				$('#SaveOverview').removeClass('disabled');
				$('#SaveOverview').attr('class','btn btn-success'); 
			}else{
				$('#SaveOverview').attr('class','btn btn-default'); 
				$('#SaveOverview').addClass('disabled');
			}
			$('#notification').css('display','none');
			$('#optimized-campaign-table').css('display','block');
		}
	}
});

$("#SaveOverview").click(function(e){

	if($("#overview_name").length > 0){
		var optimizer_name = $("#overview_name").val();
		var data = { 'checked[]' : [], "optimizer_name" : optimizer_name};
	
		 $("#checked_ov:checked").each(function() {
	      if($(this).val() != 'on'){
	      	 console.log($(this).val())
	          data['checked[]'].push($(this).val());
	      }
	  	});
	
		if(data['checked[]'].length == 0){
				$('#notification').attr('class','alert btn btn-danger'); 
				$('#notification').css('display','block'); 
				$('#notification').text("Please check atleast 1 campaign"); 
		}else{
	
				$.ajax
		        ({
		        type: "POST",
		        url: baseurl + "overview/main/create_new_optimizer",
		        cache: false,
		        data: data,
		        beforeSend: function()
		        {
		        	$('#SaveOverview').addClass('disabled');
					$('#notification').css('display','block'); 
					$('#notification').text("Creating.. please wait...");
					$('#notification').attr('class','alert alert-info'); 
		        },
		        success: function(result)
		        {
		        	var res = result.split("|");
		        	if(res[0] == 'success'){
		        		$('#notification').text("Optimizer Successfully Saved. Refreshing page.. one moment..");
						$('#notification').attr('class','alert alert-success'); 
						var data_post = { 'overview_id' : res[1], "overview_key" : res[2]};
						$.ajax
						({
						        type: "POST",
						        url: baseurl + "overview/main/generate_youtube_view",
						        cache: false,
						        data: data_post
						});

						window.location.replace(baseurl + 'overview/main/overview/existing/popup/' + res[1]+ '/' + res[2]);
						//setTimeout("refresh_to_existing()", 1500);
		        	}else{
		        	$('#SaveOverview').removeClass('disabled');
					$('#notification').css('display','block'); 
					$('#notification').text("Error creating optimizer, please try again.");
					$('#notification').attr('class','alert alert-danger'); 
		        	}
	
		        }
	    	});
			
			}
	}
});

/* UPDATE FUNCTION */
$("#overview_form_update").ready(function(e){
	 
	 
	if($("#overview_name").length > 0){ 
		var overview_name = $("#overview_name").val();
	
		if(overview_name.length < 5){
			enable_update_button(0);
		}else{
			enable_update_button(1);
		}
		$("#overview_name").keyup(function(e){
			var overview_name = $("#overview_name").val();
			if(overview_name.length < 5){
		//class="alert alert-success"
				$('#notification').attr('class','alert alert-danger'); 
				$('#notification').css('display','block'); 
				$('#notification').text("Overview name atleast 5 characters"); 
	
				enable_update_button(0);
			}else{
				enable_update_button(1);
			}
		})
	
	
		function enable_update_button(type_button){
			if(type_button == 0){
				$('#UpdateOverview').attr('class','btn btn-lg btn-default'); 
				$('#UpdateOverview').addClass('disabled');
			}else{
				$('#UpdateOverview').removeClass('disabled');
				$('#notification').css('display','none');
				$('#UpdateOverview').attr('class','btn btn-lg btn-success');  
			}
		}
	}
});

$("#UpdateOverview, #UpdateOverviewSaveExit").click(function(e){

	if($("#overview_name").length > 0){ 
		var optimizer_name = $("#overview_name").val();
		var oid = $("#oid").val();
		var data = { 'checked[]' : [], "optimizer_name" : optimizer_name, "oid" : oid};
	
		 $("#checked_ov:checked").each(function() {
	      if($(this).val() != 'on'){
	          data['checked[]'].push($(this).val());
	      }
	  	});
	
		if(data['checked[]'].length == 0){
				$('#notification').attr('class','alert btn btn-danger'); 
				$('#notification').css('display','block'); 
				$('#notification').text("Please check atleast 1 campaign"); 
		}else{
			console.log(data);
				$.ajax
		        ({
		        type: "POST",
		        url: baseurl + "overview/main/update_optimizer",
		        cache: false,
		        data: data,
		        beforeSend: function()
		        {
		        	$('#UpdateOverview').addClass('disabled');
					$('#notification').css('display','block'); 
					$('#notification').text("Updating.. please wait...");
					$('#notification').attr('class','alert alert-info'); 
		        },
		        success: function(result)
		        {
		        	var res = result.split("|");
		        	if(res[0] == 'success'){
		        		$('#notification').text("Update Successful. Refreshing page.. one moment..");
						$('#notification').attr('class','alert alert-success'); 
	
						var data_post = { 'overview_id' : res[1], "overview_key" : res[2]};
						$.ajax
						({
						        type: "POST",
						        url: baseurl + "overview/main/generate_youtube_view",
						        cache: false,
						        data: data_post
						});
	
						setTimeout("refresh_to_existing()", 1500);
		        	}else{
		        	$('#UpdateOverview').removeClass('disabled');
					$('#notification').css('display','block'); 
					$('#notification').text("Error updating optimizer, please try again.");
					$('#notification').attr('class','alert alert-danger'); 
		        	}
	
		        }
	    	});
				
		}
	}
});
$("#cancelUpdate").click(function(e){
	if($("#overview_name").length > 0){ 
		var optimizer_name 		= $("#overview_name").val();
		var overview_name_dup 	= $("#overview_name_dup").val();
		if(optimizer_name != overview_name_dup){
			$("#changes_name").text(optimizer_name + " to " + optimizer_name);
			$('#show_backupdate').modal();
		}else{
			refresh_to_existing();
		}
	}
});

$("#DeleteOverview").click(function(e){

	if($("#overview_name").length > 0){ 
		var optimizer_name = $("#overview_name").val();
		var oid = $("#oid").val();
		var data = { 'checked[]' : [], "optimizer_name" : optimizer_name, "oid" : oid};
	
		 $(":checked").each(function() {
	      if($(this).val() != 'on'){
	          data['checked[]'].push($(this).val());
	      }
	  	});
	
	  	if(confirm("Are you sure you want to delete this optimizer?")){
				$.ajax
		        ({
		        type: "POST",
		        url: baseurl + "overview/main/delete_optimizer",
		        cache: false,
		        data: data,
		        beforeSend: function()
		        {
		        	$('#UpdateOverview').addClass('disabled');
					$('#notification').css('display','block'); 
					$('#notification').text("Deleting.. please wait...");
					$('#notification').attr('class','alert alert-info'); 
		        },
		        success: function(result)
		        {
		        	console.log(result);
		        	if(result == 'success'){
		        		$('#notification').text("Delete Successful. Refreshing page.. one moment..");
						$('#notification').attr('class','alert alert-success'); 
						setTimeout("refresh_to_main()", 1500);
		        	}else{
		        	$('#UpdateOverview').removeClass('disabled');
					$('#notification').css('display','block'); 
					$('#notification').text("Error deleting optimizer, please try again.");
					$('#notification').attr('class','alert alert-danger'); 
		        	}
	
		        }
	    	});
	  	}
	  }
});
function refresh_to_main(){
	window.location.replace(baseurl + 'overview/main');
}

function refresh_to_existing(){
	window.location.replace(baseurl + 'overview/main/overview/existing');
}

$("#ForceGenerateData").click(function(e){

	var opt_id = $("#opt_id").val();
	
		$.ajax
	        ({
	        type: "POST",
	        url: baseurl + "overview/main/generate_optimizer_report",
	        cache: false,
	        data: {"opt_id": opt_id, "is_force" : 1},
	        beforeSend: function()
	        {
				//$('#notification').html("<div class=\"alert alert-warning\" role=\"alert\">Getting data, this may take awhile...</div>");
	        	show_loader( 'PLEASE WAIT!', true, 400 );
	        },
	        success: function(result)
	        {
	        	show_loader ( 'ERROR', false, 100 );
	        	if(result == 'error'){
	        		$('#notification').html("<div class=\"alert alert-danger\" role=\"alert\">Cannot generate new data, please try again</div>");
	        	}else{
	        		$('#notification').html("<div class=\"alert alert-success\" role=\"alert\">Getting data successful</div>");
	        		setTimeout("refresh_to_details('"+ opt_id +"')", 1500);
	        	}
	        	
	        }
    	});
});

function save_campaign_from_optimizer(campaign_id,token_key,optimizer_id){
	//console.log("Campaign id: " + campaign_id + " Token key: " + token_key + " Optimizer id: " + optimizer_id);

			var data = {"campaign_id" : campaign_id, "optimizer_id" : optimizer_id};
			$.ajax
	        ({
	        type: "POST",
	        url: baseurl + "overview/main/addNewCampaignToOptimizer",
	        cache: false,
	        data: data,
	        success: function(result)
	        {
	        	console.log(result);
	        	var res = result.split("|");
	        	if(res[0] == 'success'){
					var data_post = { 'overview_id' : res[1], "overview_key" : res[2]};
					$.ajax
					({
					        type: "POST",
					        url: baseurl + "overview/main/generate_youtube_view",
					        cache: false,
					        data: data_post
					});
	        	}
	        }
    	});	
}

$("#GenerateCampaign").click(function(e){
	var opt_id = $("#opt_id").val();
	var local_date = new Date();
	//console.log(local_date);
	show_loader( 'PLEASE WAIT!', true, 400 );
	$.ajax
    ({
	    type: "POST",
	    url: baseurl + "overview/main/GenerateNewCampaign",
	    cache: false,
	    data: {"opt_id" : opt_id, "local_date" : local_date},
	    beforeSend: function()
	    {
			//$('#notification').html("<div class=\"alert alert-warning\" role=\"alert\">Getting data, this may take awhile...</div>");
	    	show_loader( 'PLEASE WAIT!', true, 400 );
	    },    
	    success: function(result)
	    {
	    	show_loader ( 'ERROR', false, 100 );
	    	if(result == 'error'){
	    		$('#notification').html("<div class=\"alert alert-danger\" role=\"alert\">Cannot generate campaign, please try again</div>");
	    	}else{
	    		$('#notification').html("<div class=\"alert alert-success\" role=\"alert\">Campaign successfully created, transfering you to campaign builder. please wait</div>");
	    		setTimeout("refresh_to_campaign_builder('"+ opt_id +"')", 1500);
	    	}
	    }	
	});

});
function generateNewCampaignCheck(optimizer_id){
	$.ajax
    ({
	    type: "POST",
	    url: baseurl + "overview/main/checkGenerateCampaign",
	    cache: false,
	    data: {"opt_id" : optimizer_id},
	    beforeSend: function()
	    {
			//$('#notification').html("<div class=\"alert alert-warning\" role=\"alert\">Getting data, this may take awhile...</div>");
	    	show_loader( 'PLEASE WAIT!', true, 400 );
	    },    
	    success: function(result)
	    {
	    	console.log(result);
	    	show_loader ( 'PLEASE WAIT', false, 100 );
	    	if(result == 'generate_new'){
	    		generateNewCampaign(optimizer_id);
	    	}else{
	    		$('#show_unable_generate_campaign').modal();
	    		$('#show_camp_data').html(result);

	    	}
	    }
	});
}
function generateNewCampaign(optimizer_id){
	show_loader( 'PLEASE WAIT!', true, 400 );
	var local_date = new Date();
	//console.log(local_date);

	$.ajax
    ({
	    type: "POST",
	    url: baseurl + "overview/main/GenerateNewCampaign",
	    cache: false,
	    data: {"opt_id" : optimizer_id, "local_date" : local_date},
	    beforeSend: function()
	    {
			//$('#notification').html("<div class=\"alert alert-warning\" role=\"alert\">Getting data, this may take awhile...</div>");
	    	show_loader( 'PLEASE WAIT!', true, 400 );
	    },    
	    success: function(result)
	    {
	    	show_loader ( 'ERROR', false, 100 );
	    	if(result == 'error'){
	    		$('#notification').html("<div class=\"alert alert-danger\" role=\"alert\">Cannot generate campaign, please try again</div>");
	    	}else{
	    		$('#notification').html("<div class=\"alert alert-success\" role=\"alert\">Campaign successfully created, transfering you to campaign builder. please wait</div>");
	    		//setTimeout("refresh_to_campaign_builder('"+ optimizer_id +"')", 100);
	    		window.location.replace(baseurl + 'dashboard/adwords_export/uopt/' + result + "/" + optimizer_id);
	    	}
	    }	
	});
}
function create_the_optimized_campaign(optimizer_id){
	$.ajax
    ({
	    type: "POST",
	    url: baseurl + "overview/main/create_the_optimized_campaign",
	    cache: false,
	    data: {"opt_id" : optimizer_id},
	    beforeSend: function()
	    {
			//$('#notification').html("<div class=\"alert alert-warning\" role=\"alert\">Getting data, this may take awhile...</div>");
	    	//show_loader( 'PLEASE WAIT!', true, 400 );
	    },    
	    success: function(result)
	    {
	    	if(result == 'success'){
	    		generateNewCampaignCheck(optimizer_id);
	    	}else{

	    		$('#show_unable_generate_campaign').modal();
	    		$('#show_camp_data').html(result);
	    	}
	    }
	});
}
function refresh_to_campaign_builder(opt_id){
	var token_key = $("#token_key").val();
	window.location.replace(baseurl + 'dashboard/adwords_export/uopt/' + token_key + "/" + opt_id);
}
function refresh_to_details(opt_id){
	window.location.replace(baseurl + 'overview/main/view_opt_detail/' + opt_id);
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

function popup_video(url){
	$.magnificPopup.open({
                iframe: {
                    markup: '<div class="mfp-iframe-scaler">'+
                                '<iframe class="mfp-iframe" frameborder="0" allowfullscreen></iframe>'+
                            '</div>',
                },
                items: {
                  src: "https://www.youtube.com/watch?v=" + url
                },
                type: 'iframe',
                closeOnBgClick: true,
                closeBtnInside: true,
                showCloseBtn: true
    });
}

function open_popup_optimized_campaign(id){
	create_the_optimized_campaign(id);
}
function open_popup_from_uploaded_csv(){
	$('#campaign-to-start').modal();
}