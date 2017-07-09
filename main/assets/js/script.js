(function($) {
	// var settings_url = "http://localhost/myvideoads/";
	var settings_url = "https://www.tubemasterpro.com/";
	
	var logged_in = $.ajax({
		url: settings_url+"main_ajax/logged_in",
		type: "POST"
	});
		
	logged_in.done(function( msg ) {
		var html = '';
		console.log(msg);
		if ( parseInt(msg) != 0 ) {
			$('#logo-div a').attr('href', settings_url+'dashboard');
			console.log($('#logo-div a').prop('href'));
			html += '<li><a class="btn btn-nav btn-danger" href="'+settings_url+'auth/logout">Logout</a></li>';
		}
		else {
			$('#logo-div a').attr('href', '#top');
			html += '<li><a class="btn btn-nav" data-toggle="modal" href="#" data-target="#loginModal">Login</a></li>';
			html += '<li><a class="btn btn-nav btn-color" data-toggle="modal" href="#" data-target="#signupModal">Free Trial</a></li> ';
		}
		$('header .navbar-right').append(html);
	});

	$('#signup_form').validify();
	$('#signup_form').on('submit',function(e){
		e.preventDefault();
		var request = $.ajax({
			url: settings_url+"main_ajax/signup",
			type: "POST",
			data: {
				first_name : $.trim($('#first_name').val()),
				last_name  : $.trim($('#last_name').val()),
				email      : $.trim($('#email').val()),
				password   : $.trim($('#password').val())
			}
		});
		
		request.done(function( msg ) {
			$('#signupModal .modal-body').removeClass('text-left');
			$('#signupModal .modal-body').addClass('text-center');
			
			if ( msg ) {
				$('#signupModal .modal-header h1').text('SUCCESS');
				$('#signupModal .modal-body').html("Thank you! We'll be in touch within a couple of hours.");
			}
			else {
				$('#signupModal .modal-header h1').text('FAILED');
				$('#signupModal .modal-body').html("Can't process signup at the moment. Please try again later.");
			}
			$('#signupModal .modal-header p').text('');
			$('#signupModal .modal-footer').fadeOut('fast');
		});
	});
	
	
	$('#login_form').validify();
	$('#login_form').on('submit',function(e){
		e.preventDefault();
		var request = $.ajax({
			url: settings_url+"main_ajax/login",
			type: "POST",
			data: {
				identity : $.trim($('#identity').val()),
				password : $.trim($('#password').val()),
				remember : $.trim($('#remember').val())
			},
			dataType: 'json'
		});
		
		request.done(function( msg ) {
			if ( msg.valid ) {
				location.href = settings_url;
			}
			else {
				$('#infoMessage p').html(msg.message);
				$('#infoMessage').fadeIn('fast');
			}
		});
	});

}( jQuery ));