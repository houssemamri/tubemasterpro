jQuery(document).ready(function($){
    'use strict';
    
    var baseurl = $("#baseurl").val();
    
	$('#campaign-table').dataTable({
		"columns": [
		    null,
		    null,
		    { "orderable": false }
		]
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
    $('#campaign-upload').fileupload({
        url: url,
        dataType: 'json',
        autoUpload: true,
        acceptFileTypes: /(\.|\/)(csv)$/i,
        add: function (e, data) {
	        var that = this;
	        $.getJSON(url, function (result) {
	            data.formData = {cid:$('#campaign-upload-modal').data('cid')}; // e.g. {id: 123}
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
    	show_loader( '', true, 300 );
        $('#upload-notice').hide();
    	$('#progress').fadeIn();
    	$('#progress .progress-bar').css(
            'width', '0%'
        );
    	var content		= '<div><p><span id="upload-notice" class="alert alert-warning">Uploading CSV please wait...</span></p></div>';
    	data.context	= $('#files');
    	$('#files').html(content);
    	/*
console.log(data.context);
        data.context = $('<div/>').appendTo('#files');
        $.each(data.files, function (index, file) {
            var node = $('<p/>')
                    .append($('<span/>').text(file.name));
            if (!index) {
                node
                    .append('<br>')
                    .append(uploadButton.clone(true).data(data));
            }
            node.appendTo(data.context);
        });
*/
    }).on('fileuploadprocessalways', function (e, data) {
    	console.log(data.context);
        var index = data.index,
            file = data.files[index],
            node = $(data.context.children()[index]);
        $('#upload-notice').show();
        if (file.error) {
	    	$('#upload-notice').removeClass('alert-success');
        	$('#upload-notice').removeClass('alert-warning');
        	$('#upload-notice').addClass('alert-danger');
        	$('#upload-notice').text(file.error);
        }
        else {
	    	$('#upload-notice').removeClass('alert-success');
        	$('#upload-notice').removeClass('alert-danger');
        	$('#upload-notice').addClass('alert-warning');
        }
    }).on('fileuploadprogressall', function (e, data) {
        var progress = parseInt(data.loaded / data.total * 100, 10);
        $('#progress .progress-bar').css(
            'width',
            progress + '%'
        );
    }).on('fileuploaddone', function (e, data) {
    	console.log(data);
    	show_loader( '', false, 300 );
    	$('#progress').fadeOut();
    	$('#progress .progress-bar').css(
            'width', '0%'
        );
        $('#upload-notice').show();
        if ( !data.result.parse_data.diff ) {
		    $('#campaign-save-btn').hide();
	    	if ( data.result.parse_data.msg == 'No Changes' ) {
		    	$('#upload-notice').removeClass('alert-danger');
		    	$('#upload-notice').removeClass('alert-warning');
		    	$('#upload-notice').addClass('alert-success');
		    	$('#upload-notice').text(data.result.parse_data.msg);
	    	}
	    	else if ( data.result.parse_data.msg == 'Data Saved' ) {
		    	$('#upload-notice').removeClass('alert-danger');
		    	$('#upload-notice').removeClass('alert-warning');
		    	$('#upload-notice').addClass('alert-success');
		    	$('#upload-notice').text(data.result.parse_data.msg);
		    	location.reload(true);
	    	}
	    	else {
		    	$('#upload-notice').removeClass('alert-success');
		    	$('#upload-notice').removeClass('alert-warning');
		    	$('#upload-notice').addClass('alert-danger');
		    	$('#upload-notice').text(data.result.parse_data.msg);
	    	}
        }
        else {
        	$('#campaign-save-btn').show();
	    	$('#upload-notice').removeClass('alert-danger');
	    	$('#upload-notice').removeClass('alert-success');
	    	$('#upload-notice').addClass('alert-warning');
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
	    	
	    	/*
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
*/
        }
    }).on('fileuploadfail', function (e, data) {
    	show_loader( '', false, 300 );
    	$('#progress').fadeOut();
    	$('#progress .progress-bar').css(
            'width', '0%'
        );
        $('#upload-notice').show();
    	console.log(e);
	    $('#upload-notice').removeClass('alert-success');
    	$('#upload-notice').removeClass('alert-warning');
    	$('#upload-notice').addClass('alert-danger');
    	$('#upload-notice').text('File Upload Error');
    }).prop('disabled', !$.support.fileInput)
        .parent().addClass($.support.fileInput ? undefined : 'disabled');
    
    $('.btn-upload').on('click',function(){
    	var campaign_name = $(this).parent().parent().parent().children('td').first().text();
    	$('#campaign-upload-modal .modal-title').text('Campaign: '+campaign_name);
	    $('#campaign-upload-modal').modal('show');
	    $('#campaign-upload-modal').data('cid', $(this).data('cid'));
	    $('#compare-div').html('');
		$('#campaign-save-btn').hide();
		$('#upload-notice').hide();
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