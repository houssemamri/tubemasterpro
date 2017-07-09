jQuery(document).ready(function($){
    'use strict';
    
    var baseurl = $("#baseurl").val();
    
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
    
	$('#campaign-table').dataTable({
		"order": [[ 1, "desc" ]],
		"columns": [
		    null,
		    null,
		    { "orderable": false }
		],
		'bPaginate' : $("#campaign-table").find('tbody tr').length>10
	});
	
	// Change this to the location of your server-side upload handler:
    var url = window.location.hostname === 'blueimp.github.io' ?
                '//jquery-file-upload.appspot.com/' : baseurl + 'dashboard/optimizer_ajax/upload_file',//'server/php/',
        uploadButton = $('<button/>')
            .addClass('btn btn-primary')
            .prop('disabled', true)
            .text('Processing...')
            .on('click', function () {
                var $this = $(this),
                    data = $this.data();
                $this
                    .off('click')
                    .text('Abort')
                    .on('click', function () {
                        $this.remove();
                        data.abort();
                    });
                data.submit().always(function () {
                    $this.remove();
                });
            });
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
			    	$('#upload-notice').text("SWEET! Let's go optimize this campaign, shall we?!");
			    	$('#data-saved-btn').attr('data-dismiss', '');
			    	$('#data-saved-btn').on('click', function(e){
			    		e.preventDefault();
			    		if ( data.result.parse_data.overview ) {
				    		location.href = baseurl + 'overview/main/existing/popup/'+data.result.parse_data.cid+'/'+data.result.parse_data.overview.token_key;
			    		}
			    		else {
				    		location.href = baseurl + 'overview/main/';
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
    
   /*
 $('.btn-upload').on('click',function(){
    	var campaign_name = $(this).parent().parent().parent().children('td').first().text();
    	$('#campaign-upload-modal .modal-title').text('Campaign: '+campaign_name);
	    $('#campaign-upload-modal').modal('show');
	    $('#campaign-upload-modal').data('cid', $(this).data('cid'));
	    $('#compare-div').html('');
		$('#campaign-save-btn').hide();
		$('#upload-notice').hide();
    });
*/
	$('.btn-delete').on('click',function(){
    	var campaign_name = $(this).parent().parent().parent().children('td').first().text();
		$('#campaign_modal .modal-body p').text('Are you sure you want to delete this Campaign "'+campaign_name+'"?');
	    $('#campaign_modal').modal('show');
	    $('#campaign_modal').data('cid', $(this).data('cid'));
    });
    
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

/*
jQuery.extend(jQuery.fn.dataTableExt.oSort, {
    "time-uni-pre": function (a) {
        var uniTime;
 
        if (a.toLowerCase().indexOf("am") > -1 || (a.toLowerCase().indexOf("pm") > -1 && Number(a.split(":")[0]) === 12)) {
            uniTime = a.toLowerCase().split("pm")[0].split("am")[0];
            while (uniTime.indexOf(":") > -1) {
                uniTime = uniTime.replace(":", "");
            }
        } else if (a.toLowerCase().indexOf("pm") > -1 || (a.toLowerCase().indexOf("am") > -1 && Number(a.split(":")[0]) === 12)) {
            uniTime = Number(a.split(":")[0]) + 12;
            var leftTime = a.toLowerCase().split("pm")[0].split("am")[0].split(":");
            for (var i = 1; i < leftTime.length; i++) {
                uniTime = uniTime + leftTime[i].trim().toString();
            }
        } else {
            uniTime = a.replace(":", "");
            while (uniTime.indexOf(":") > -1) {
                uniTime = uniTime.replace(":", "");
            }
        }
        return Number(uniTime);
    },
 
    "time-uni-asc": function (a, b) {
        return ((a < b) ? -1 : ((a > b) ? 1 : 0));
    },
 
    "time-uni-desc": function (a, b) {
        return ((a < b) ? 1 : ((a > b) ? -1 : 0));
    }
});

$.fn.dataTable.moment = function ( format, locale ) {
    var types = $.fn.dataTable.ext.type;
 
    // Add type detection
    types.detect.unshift( function ( d ) {
        return moment( d, format, locale, true ).isValid() ?
            'moment-'+format :
            null;
    } );
 
    // Add sorting method - use an integer for the sorting
    types.order[ 'moment-'+format+'-pre' ] = function ( d ) {
        return moment( d, format, locale, true ).unix();
    };
};
*/