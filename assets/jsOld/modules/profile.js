$(document).ready(function(){
	$("#profile_form").validify();
	$("#profile_form").on('submit', function(e){
		e.preventDefault();
		var request = $.ajax({
			url: $(this).data('action'),
			type: "POST",
			crossDomain: true,
			data: {
				old : $("#old_password").val(),
				new : $("#new_password").val(),
				pic : $("#file_hidden").val()
			},
			dataType: "json"
		});

		request.done(function(msg){
			if (msg.valid) {
				$('#infoMessage').fadeOut();
				location.reload();
			}
			else {
				$('#infoMessage').html(msg.message);
				$('#infoMessage').fadeIn();
			}
		});
	});

	$("#file_remove").on("click", function(e){
        e.preventDefault();
        $("#file_hidden").val("");
        $("#file-upload").find(".files").empty();
        $("#file_container").hide();
        $("#image-holder").prop("src", "http://www.tubetargetpro.com/assets/avatar/nophoto.png");
        $("#image-holder").css("width", "50px");
        $("#image-holder").css("height", "50px");
        // Save to db
        var request = $.ajax({
            url: "http://tubetargetpro.com/dashboard/dashboard_ajax/save_profile_pic",
            type: "POST",
            crossDomain: true,
            data: {
            	id  : $(this).data('id'),
                pic : 'nophoto.png'
            }
        });
		request.done(function(msg){
			console.log(msg);
		});
    });
});