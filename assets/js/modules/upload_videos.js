$(document).ready(function(){
	InitViewDetails();
});

function InitViewDetails () {
	var detailsCollect = $('.view_details');

	if(detailsCollect.length > 0) {
		$.each(detailsCollect, function (i,v) {
			/*$(v).bind('click', function () {
				console.log($(this).data('vid-details'));


			});*/
			var id = $(v).data('vid-details');

			$(v).magnificPopup({
					type: 'ajax',
					overflowY: 'scroll'
				}).attr('href', 'https://www.nathanhague.com/tubetargetpro/dashboard/upload_videos/view_details/'+id);	
		});
	}
}

function updateUploadVideo() {

var baseurl = $('#baseurl').val();
var upload_id = $('#upload_id').val();
var new_filename = $('#new_filename').val();
var original_filename = $('#original_filename').val();


console.log(baseurl + " " + upload_id + " " + new_filename + " " + original_filename);

	$.ajax({

			type: "POST",

			url: baseurl + 'uploadvid_ajax/update_UploadData',

			data: {upload_id:upload_id,new_filename:new_filename,original_filename:original_filename},

			cache: false

	}).done(function () {
		email();
	});
}

function email() {
var baseurl = $('#baseurl').val();
var original_filename = $('#original_filename').val();

	$.ajax({

			type: "POST",

			url: baseurl + 'uploadvid_ajax/send_Email',

			data: {original_filename:original_filename},

			cache: false

	}).done(function () {
		window.location.replace(baseurl+'dashboard/upload_videos');
	});	
}

