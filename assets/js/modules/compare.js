jQuery(document).ready(function($){
    'use strict';
    
    //$('#campaign_init_modal').modal('show');
    $('#campaign_init_modal').modal({
    		show: true,
    		keyboard: true,
    		backdrop: 'static'
    });
    var baseurl = $("#baseurl").val();
    
    //- Datatable plugin for sorting with moment time
    $.fn.dataTable.moment = function ( format, locale ) {
	    var types = $.fn.dataTable.ext.type;
	 
	    // Add type detection
	    types.detect.unshift( function ( d ) {
	        // Null and empty values are acceptable
	        if ( d === '' || d === null ) {
	            return 'moment-'+format;
	        }
	 
	        return moment( d.replace ? d.replace(/<.*?>/g, '') : d, format, locale, true ).isValid() ?
	            'moment-'+format :
	            null;
	    } );
	 
	    // Add sorting method - use an integer for the sorting
	    types.order[ 'moment-'+format+'-pre' ] = function ( d ) {
	        return d === '' || d === null ?
	            -Infinity :
	            parseInt( moment( d.replace ? d.replace(/<.*?>/g, '') : d, format, locale, true ).format( 'x' ), 10 );
	    };
	};
    
    $.fn.dataTable.moment( 'YYYY-MM-DD HH:mm:ss A' );
    
    //- Initialize Datatable
	var pageLength = 10;
	$('#campaign-table').dataTable({
		//"pageLength": pageLength,
		"order": [[ 2, "desc" ]],
		"columns": [
		    { "orderable": false },
		    null,
		    null,
		    { "orderable": false }
		],
		'bPaginate' : $("#campaign-table").find('tbody tr').length>pageLength,
		"drawCallback": function( settings ) {
        	delete_init();
        	
	        $('.to_delete').on('click', function(){
	        	delete_init();
			});
	    }
	});
	
	//- Event click for check all delete
	$('#delete-all').on('click', function(){
		if ( this.checked ) {
			$('.to_delete').prop('checked', true);
			$('#master-delete').removeClass('btn-default disabled');
		    $('#master-delete').addClass('btn-danger');
		}
		else {
			$('.to_delete').prop('checked', false);
			$('#master-delete').removeClass('btn-danger');
		    $('#master-delete').addClass('btn-default disabled');
		}
	});
	
	//- Event click for master delete/delete selected button
	$('#master-delete').on('click', function(){
		var checks = [];
		$('.to_delete:checked').each(function(){
			checks.push($(this).data('cid'));
		});
		
		$.ajax({
			url: baseurl + 'dashboard/optimizer_ajax/multiple_delete_campaign',
			type:"POST",
			data: {
				ids : checks
			},
			dataType: 'json'
		}).done(function(data){
			location.reload();
		});
	});
	
	//- Event click for Global Upload CSV button
	$('#master-delete').on('click', function(){
		var checks = [];
		$('.to_delete:checked').each(function(){
			checks.push($(this).data('cid'));
		});
		
		$.ajax({
			url: baseurl + 'dashboard/optimizer_ajax/multiple_delete_campaign',
			type:"POST",
			data: {
				ids : checks
			},
			dataType: 'json'
		}).done(function(data){
			location.reload();
		});
	});
	
	//- Main function for enabling/disabling master delete/delete selected button
	function delete_init () {
    	var total   = $('.to_delete').length;
    	var checked = $('.to_delete:checked').length;
    	
    	if ( checked > 0 ) {
        	$('#master-delete').removeClass('btn-default disabled');
        	$('#master-delete').addClass('btn-danger');
    	}
    	else {
        	$('#master-delete').removeClass('btn-danger');
        	$('#master-delete').addClass('btn-default disabled');
    	}
    	
		if ( checked == total ) {
			$('#delete-all').prop('checked', true);
		}
		else {
			$('#delete-all').prop('checked', false);
		}
	}
	
	//- Initialize jQuery file upload for the main upload functionality
    //- For general upload
	$('#global-upload').fileupload({
        url: baseurl + 'dashboard/optimizer_ajax/general_upload_file',
        dataType: 'json',
        autoUpload: true,
        acceptFileTypes: /(\.|\/)(csv)$/i
    }).on('fileuploadadd', function (e, data) {
    	$('.main_loader #progress-main #progress-containter .progress .progress-bar').css(
            'width', '1%'
        );
    	$('.main_loader').show();
        $('.main_loader #progress-main').fadeIn();
    	$('.main_loader #progress-main #progress-containter').fadeIn();
        var content		= 'Uploading CSV please wait...';
    	data.context	= $('.main_loader #progress-main #progress-containter #cancel-notice');
    	data.context.html(content);
    	data.context.show();
    }).on('fileuploadprocessalways', function (e, data) {
    	console.log(data.context);
        var index = data.index,
            file  = data.files[index];
        if (file.error) {
        	$('.main_loader').hide();
        	$('#campaign-upload-modal').modal('show');
        	$('#upload-notice').fadeIn();
	    	$('#upload-notice').parent().parent().removeClass('alert-success');
        	$('#upload-notice').parent().parent().removeClass('alert-warning');
        	$('#upload-notice').parent().parent().addClass('alert-danger');
        	$('#upload-notice').text(file.error);
        }
    }).on('fileuploadprogressall', function (e, data) {
        var progress = parseInt(data.loaded / data.total * 100, 10);
        $('.main_loader #progress-main #progress-containter .progress-bar').css(
            'width',
            progress + '%'
        );
    }).on('fileuploaddone', function (e, data) {
    	console.log(data);
    	$('.main_loader').hide();
    	$('.main_loader #progress-main').fadeOut(function(){
    		$('.main_loader #progress-main #progress-containter').fadeOut();
	    	$('.main_loader #progress-main #progress-containter .progress .progress-bar').css(
	            'width', '1%'
	        );
	    });
	    
        if ( !data.result.parse_data.diff ) {
	    	if ( data.result.parse_data.msg == 'No Changes' ) {
		    	$('#upload-notice').parent().parent().removeClass('alert-danger');
		    	$('#upload-notice').parent().parent().removeClass('alert-warning');
		    	$('#upload-notice').parent().parent().addClass('alert-success');
		    	$('#upload-notice').text(data.result.parse_data.msg);
	    	}
	    	else if ( data.result.parse_data.msg == 'Data Saved' ) {
		    	$('#upload-notice').parent().parent().removeClass('alert-danger');
		    	$('#upload-notice').parent().parent().removeClass('alert-warning');
		    	$('#upload-notice').parent().parent().addClass('alert-success');
		    	$('#upload-notice').text("SWEET! Let's go optimize campaign \""+data.result.parse_data.name+"\", shall we?!");
		    	$('#data-saved-btn').attr('data-dismiss', '');
		    	$('#data-saved-btn').on('click', function(e){
		    		e.preventDefault();
		    		if ( data.result.parse_data.overview ) {
			    		location.href = baseurl + 'overview/main/overview/existing/popup/'+data.result.parse_data.cid+'/'+data.result.parse_data.overview.token_key;
		    		}
		    		else {
			    		location.href = baseurl + 'overview/main/overview/uploaded/'+data.result.parse_data.cid;
		    		}
		    	});
				$('#campaign-upload-modal').modal({
					backdrop	: 'static',
					keyboard	: false,
					show		: true
				});
	    	}
	    	else {
		    	$('#upload-notice').parent().parent().removeClass('alert-success');
		    	$('#upload-notice').parent().parent().removeClass('alert-warning');
		    	$('#upload-notice').parent().parent().addClass('alert-danger');
		    	$('#upload-notice').text(data.result.parse_data.msg);
	    	}
	    	$('#campaign-upload-modal').css('z-index','1051');
			$('#campaign-upload-modal').modal('show');
			$('#upload-notice').show();
			
        }
		
    }).on('fileuploadfail', function (e, data) {
    	$('.main_loader').hide();
    	$('.main_loader #progress-main').fadeOut(function(){
    		$('.main_loader #progress-main #progress-containter').fadeOut();
	    	$('.main_loader #progress-main #progress-containter .progress .progress-bar').css(
	            'width', '1%'
	        );
	    });
        $('#campaign-upload-modal').modal('show');
        $('#upload-notice').show();
    	console.log(e);
	    $('#upload-notice').parent().parent().removeClass('alert-success');
    	$('#upload-notice').parent().parent().removeClass('alert-warning');
    	$('#upload-notice').parent().parent().addClass('alert-danger');
    	$('#upload-notice').text('File Upload Error');
    }).prop('disabled', !$.support.fileInput)
        .parent().addClass($.support.fileInput ? undefined : 'disabled');
    
    //- For Individual upload
    var url = baseurl + 'dashboard/optimizer_ajax/upload_file';
    $('.btn-upload').each(function(){
	    $(this).fileupload({
	        url: url,
	        dataType: 'json',
	        autoUpload: true,
	        acceptFileTypes: /(\.|\/)(csv)$/i,
	        add: function (e, data) {
		        var that = this;
		        $.getJSON(url, function (result) {
		            data.formData = {cid:$(that).data('cid')}; // e.g. {id: 123}
		            $.blueimp.fileupload.prototype
		                .options.add.call(that, e, data);
		        });
		    } 
	        // maxFileSize: 999000,
	        // Enable image resizing, except for Android and Opera,
	        // which actually support image resizing, but fail to
	        // send Blob objects via XHR requests:
	        // disableImageResize: /Android(?!.*Chrome)|Opera/
	        //    .test(window.navigator.userAgent),
	        // previewMaxWidth: 100,
	        // previewMaxHeight: 100,
	        // previewCrop: true
	    }).on('fileuploadadd', function (e, data) {
	    	$('.main_loader #progress-main #progress-containter .progress .progress-bar').css(
	            'width', '1%'
	        );
	    	$('.main_loader').show();
	        $('.main_loader #progress-main').fadeIn();
	    	$('.main_loader #progress-main #progress-containter').fadeIn();
	        var content		= 'Uploading CSV please wait...';
	    	data.context	= $('.main_loader #progress-main #progress-containter #cancel-notice');
	    	data.context.html(content);
	    	data.context.show();
	    }).on('fileuploadprocessalways', function (e, data) {
	    	console.log(data.context);
	        var index = data.index,
	            file  = data.files[index];
	        if (file.error) {
	        	$('.main_loader').hide();
	        	$('#campaign-upload-modal').modal('show');
	        	$('#upload-notice').fadeIn();
		    	$('#upload-notice').parent().parent().removeClass('alert-success');
	        	$('#upload-notice').parent().parent().removeClass('alert-warning');
	        	$('#upload-notice').parent().parent().addClass('alert-danger');
	        	$('#upload-notice').text(file.error);
	        }
	    }).on('fileuploadprogressall', function (e, data) {
	        var progress = parseInt(data.loaded / data.total * 100, 10);
	        $('.main_loader #progress-main #progress-containter .progress-bar').css(
	            'width',
	            progress + '%'
	        );
	    }).on('fileuploaddone', function (e, data) {
	    	console.log(data);
	    	$('.main_loader').hide();
	    	$('.main_loader #progress-main').fadeOut(function(){
	    		$('.main_loader #progress-main #progress-containter').fadeOut();
		    	$('.main_loader #progress-main #progress-containter .progress .progress-bar').css(
		            'width', '1%'
		        );
		    });
		    
	        if ( !data.result.parse_data.diff ) {
		    	if ( data.result.parse_data.msg == 'No Changes' ) {
			    	$('#upload-notice').parent().parent().removeClass('alert-danger');
			    	$('#upload-notice').parent().parent().removeClass('alert-warning');
			    	$('#upload-notice').parent().parent().addClass('alert-success');
			    	$('#upload-notice').text(data.result.parse_data.msg);
		    	}
		    	else if ( data.result.parse_data.msg == 'Data Saved' ) {
			    	$('#upload-notice').parent().parent().removeClass('alert-danger');
			    	$('#upload-notice').parent().parent().removeClass('alert-warning');
			    	$('#upload-notice').parent().parent().addClass('alert-success');
			    	//$('#upload-notice').text(data.result.parse_data.msg);
			    	//location.reload(true);
					$('#upload-notice').text("SWEET! Let's go optimize campaign \""+data.result.parse_data.name+"\", shall we?!");
			    	$('#data-saved-btn').attr('data-dismiss', '');
			    	$('#data-saved-btn').on('click', function(e){
			    		e.preventDefault();
			    		if ( data.result.parse_data.overview ) {
				    		location.href = baseurl + 'overview/main/overview/existing/popup/'+data.result.parse_data.cid+'/'+data.result.parse_data.overview.token_key;
			    		}
			    		else {
				    		location.href = baseurl + 'overview/main/overview/uploaded/'+data.result.parse_data.cid;
			    		}
			    	});
					$('#campaign-upload-modal').modal({
						backdrop	: 'static',
						keyboard	: false,
						show		: true
					});
		    	}
		    	else {
			    	$('#upload-notice').parent().parent().removeClass('alert-success');
			    	$('#upload-notice').parent().parent().removeClass('alert-warning');
			    	$('#upload-notice').parent().parent().addClass('alert-danger');
			    	$('#upload-notice').text(data.result.parse_data.msg);
		    	}
		    	
				$('#campaign-upload-modal').modal('show');
				$('#upload-notice').show();
				
	        }
	        else {
	        	$('#campaign-save-btn').show();
		    	$('#upload-notice').parent().parent().removeClass('alert-danger');
		    	$('#upload-notice').parent().parent().removeClass('alert-success');
		    	$('#upload-notice').parent().parent().addClass('alert-warning');
			    $('#upload-notice').text('There are some changes from AdWords CSV');
			    
			    var new_data = [];
		    	
		    	$.each(data.result.parse_data.campaign.ads, function(k,v){
				    var c_class 	= '';
				    var click_class	= '';
				    var view_class	= '';
			    	if ( v.diff ) {
				    	console.log(v);
				    	c_class = 'There are some changes';
				    	if ( v.dbdata.clicks != v.updata.clicks ) {
				    		click_class = 'bg-danger';
				    	}
				    	else {
				    		click_class = 'bg-success';
				    	}
				    	
				    	
				    	if ( v.dbdata.views != v.updata.views ) {
				    		view_class = 'bg-danger';
				    	}
				    	else {
				    		view_class = 'bg-success';
				    	}
			    	}
			    	else {
				    	c_class = 'No changes';
			    	}
			    	
			    	var ytlink = 'https://www.youtube.com/watch?v='+$.trim(v.dbdata.vid);
			    	var c_content = '<div class="cdata">'+
			    		'<h4><a class="video_link" target="_blank" href="'+ytlink+'">'+v.dbdata.vid+'</a> <small>('+c_class+')</small></h4>'+
					    '<div class="old-cdata col-md-6">'+
						    '<ul class="list-unstyled">'+
							    '<li class="ca-clicks">Clicks: '+v.dbdata.clicks+'</li>'+
							    '<li class="ca-views">Views: '+v.dbdata.views+'</li>'+
						    '</ul>'+
					    '</div>'+
					    '<div class="new-cdata col-md-6">'+
						    '<ul class="list-unstyled">'+
							    '<li class="ca-clicks '+click_class+'">Clicks: '+v.updata.clicks+'</li>'+
							    '<li class="ca-views '+view_class+'">Views: '+v.updata.views+'</li>'+
						    '</ul>'+
					    '</div>'+
			    	'</div>';
			    	$('#compare-div').html(c_content);
			    	
			    	new_data.push(v.updata);
		    	});
		    	
		    	/*
if (data.result.parse_data.cup_id) {
			    	$('#campaign-save-btn').data('cup_id', data.result.parse_data.cup_id);
		    	}
		    	
			    $('#campaign-save-btn').on('click',function(e){
				    e.preventDefault();
				    var id = $(this).data('cup_id');
					console.log(new_data);
				    $.ajax({
						type: "POST",
						url: baseurl + 'dashboard/optimizer_ajax/save_changes',
						data: { id: id, ads: new_data },
						cache: false
					}).done(function( data ) {
						location.reload(true);
					});
			    });
*/
	        }
			
	    }).on('fileuploadfail', function (e, data) {
	    	$('.main_loader').hide();
	    	$('.main_loader #progress-main').fadeOut(function(){
	    		$('.main_loader #progress-main #progress-containter').fadeOut();
		    	$('.main_loader #progress-main #progress-containter .progress .progress-bar').css(
		            'width', '1%'
		        );
		    });
	        $('#campaign-upload-modal').modal('show');
	        $('#upload-notice').show();
	    	console.log(e);
		    $('#upload-notice').parent().parent().removeClass('alert-success');
	    	$('#upload-notice').parent().parent().removeClass('alert-warning');
	    	$('#upload-notice').parent().parent().addClass('alert-danger');
	    	$('#upload-notice').text('File Upload Error');
	    }).prop('disabled', !$.support.fileInput)
	        .parent().addClass($.support.fileInput ? undefined : 'disabled');
    });
    
    //- Event Delete Campaign modal
	$('.btn-delete').on('click',function(){
    	var campaign_name = $(this).parent().parent().parent().children('td').first().text();
		$('#campaign_modal .modal-body p').text('Are you sure you want to delete this Campaign "'+campaign_name+'"?');
	    $('#campaign_modal').modal('show');
	    $('#campaign_modal').data('cid', $(this).data('cid'));
    });
    
    //- Event Delete Campaign
    $('#campaign-delete-btn').on('click',function(){
    	console.log($(this).data('cid'));
    	var action = baseurl + 'dashboard/campaign_ajax/delete_campaign';
	    var cid = $('#campaign_modal').data('cid');
		$.ajax({
			url: action,
			type:"POST",
			data: {
				id : cid
			},
			dataType: 'json'
		}).done(function(data){
			location.reload();
		});
    });
    
    //- Event info popup
    $('.btn-info').on('click',function(){
    	console.log('info');
    	show_loader( 'Getting info', true, 300 );
    	var cid = $(this).data('cid');
    	$.ajax({
			url: baseurl + 'dashboard/optimizer_ajax/get_campaign_info',
			type:"POST",
			data: {
				cid : cid
			}
		}).done(function(data){
			//console.log(data);
    		show_loader( 'Done!', false, 300 );
			$('#campaign-info-modal .modal-body').html('');
			$('#campaign-info-modal .modal-body').html(data);
    		$('#campaign-info-modal').modal('show');
    		
    		$('.video_link').each(function(){
				$(this).on('click', function(e){
					e.preventDefault();
					$.magnificPopup.open({
					    items: {
					      src: $(this).prop('href')
					    },
					    type: 'iframe',
					    callbacks: {
						  open: function() {
						  	$('.mfp-bg').css('z-index','1052');
						  	$('.mfp-wrap').css('z-index','1052');
						  },
						}
					});
				});
			});
		});
    });
    
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
});