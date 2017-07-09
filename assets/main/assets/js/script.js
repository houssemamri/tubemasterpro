(function($) {
	// var settings_url = "http://www.myvideoads.dev/";
	var settings_url = "https://www.tubemasterpro.com/";
	
	var logged_in = $.ajax({
		url: settings_url+"main_ajax/logged_in",
		type: "POST",
		dataType: "json"
	});
		
	logged_in.done(function( msg ) {
		var html = '';
		if ( msg.logged_in ) {
		console.log('test');
			$('#logo-div a').prop('href',settings_url+'dashboard');
			$('#logo-div a').removeClass('scrollto');
			$('#logo-div a').on('click', function(){
				window.location.href = $(this).prop('href');
			});

			html += '<li><a class="btn btn-nav btn-danger" href="'+settings_url+'auth/logout">Logout</a></li>';
		}
		else {
			$('#logo-div a').prop('href','#top');
			html += '<li><a id="tmp-main-login-btn" class="btn btn-nav" data-toggle="modal" href="#" data-target="#loginModal">Login</a></li>';
			//html += '<li><a class="btn btn-nav" href="#" onclick="popup_login("'+settings_url+'auth/login","iframe"); return false;">Login</a></li>';
			html += '<li><a class="btn btn-nav btn-color" data-toggle="modal" href="#" data-target="#signupModal">Free Trial</a></li> ';
		}
		$('header .navbar-right').append(html);
		
		if ( msg.message ==  '<p>Account Activated<\/p>' ) {
			$('#loginModal').modal('show');
			$('#infoMessage p').html(msg.message);
			$('#infoMessage').fadeIn('fast');
		}
		else if ( msg.activate ==  'forgot' ) {
			$('#forgotModal').modal('show');
		}
	});

	$('#signup_form').validify();
	$('#signup_form').on('submit',function(e){
		e.preventDefault();
		$(this).unbind('submit');
		var request = $.ajax({
			url: settings_url+"main_ajax/signup",
			type: "POST",
			data: {
				first_name 	: $.trim($('#first_name').val()),
				last_name  	: $.trim($('#last_name').val()),
				email      	: $.trim($('#email').val()),
				password   	: $.trim($('#signup_password').val()),
				signuptoken : $('#signuptoken').val()
				
			}
		});
		
		request.done(function( msg ) {
			var res = msg.split("|");

			$('#signupModal .modal-body').removeClass('text-left');
			$('#signupModal .modal-body').addClass('text-center');
			
			if ( res[0] == "SUCCESS" ) {
				$('#signupModal .modal-header h1').text(res[1]);
				$('#signupModal .modal-header h1').css('color', '#3c763d');
				$('#signupModal .modal-body').html(res[2]);
			}
			else {
				$('#signupModal .modal-header h1').text(res[1]);
				$('#signupModal .modal-body').html(res[2]);
			}
			$('#signupModal .modal-header p').text('');
			$('#signupModal .modal-footer').fadeOut('fast');
		});
	});

	$('#signupModal').on('shown.bs.modal', function (e) {
		$('#first_name').focus();
	})
	/*
		
		
	*/
	var login_submit = false;
	$('#login_form').validify();
	$('#login_form').on('submit',function(e){

		e.preventDefault();
		if ( !login_submit ) {
			login_submit = true;
			$('.preloader').fadeIn('fast');
			$('.preloader .signal').fadeIn('fast');
			var request = $.ajax({
				url: settings_url+"main_ajax/login",
				//url: settings_url+"auth/popup_login",
				type: "POST",
				data: {
					identity : $.trim($('#identity').val()),
					password : $.trim($('#password').val()),
					remember : $.trim($('#remember').val())
				},
				dataType: 'json'
			});
			
			request.done(function( msg ) {
				console.log(msg);
				login_submit = false;
				if ( msg.valid ) {
					window.location.href = settings_url+'auth/';
				}
				else {
					$('.preloader').fadeOut('fast');
					$('.preloader .signal').fadeOut('fast');
					$('#infoMessage p').html(msg.message);
					$('#infoMessage').fadeIn('fast');
					if ( $('#tmp-resend').length > 0 ) {
						$('#tmp-resend').on('click', function(event){
							event.preventDefault();
							$('.preloader').fadeIn('fast');
							$('.preloader .signal').fadeIn('fast');
							var url = $(this).data('url');
							$.ajax({
								url		: url,
								type	: "GET",
								success	: function(msg){
									console.log(msg);
									$('.preloader').fadeOut('fast');
									$('.preloader .signal').fadeOut('fast');
									$('#infoMessage p').html(msg);
									$('#infoMessage').fadeIn('fast');
								}
							});
						});
					}
				}
			});
			
			request.fail(function( jqXHR, textStatus ) {
				login_submit = false;
				console.log( "Request failed: " + textStatus );
				// console.log( "Can\'t add new target. Please try again later." );
					$('.preloader').fadeOut('fast');
					$('.preloader .signal').fadeOut('fast');
					$('#infoMessage p').html("Connection problem, try again later.");
					$('#infoMessage').fadeIn('fast');
			});
		}
	});

	$('#loginModal').on('shown.bs.modal', function (e) {
		$('#identity').focus();
	});

	$('#forgot_popup').on('click',function(){
		$('#loginModal').modal('hide');
		$('#forgotModal').modal('show');
	});

	$('#forgotModal').on('show.bs.modal', function (e) {
		$('#forgot_email').focus();
	});

	$('#forgot_form').validify();
	$('#forgot_form').on('submit',function(e){
		e.preventDefault();
		var request = $.ajax({
			url: settings_url+"main_ajax/forgot_password",
			type: "POST",
			data: {
				email : $.trim($('#forgot_email').val()),
			},
			dataType: 'json'
		});
		
		request.done(function( msg ) {
			console.log(msg);
			if ( msg.valid ) {
				$('#forgotModal').modal('hide');
				$('#show_success').modal('show');
			}
			else {
				$('#infoForgotMessage p').html(msg.msg);
				$('#infoForgotMessage').fadeIn('fast');
			}
		});
	});

	$('#yt-video').magnificPopup({
		items: {
			src: 'https://www.youtube.com/watch?v=1aBZkvrpysM'
		},
		type: 'iframe',
		iframe: {
			markup: '<div class="mfp-iframe-scaler">'+
	            		'<div class="mfp-close"></div>'+
	            		'<iframe class="mfp-iframe" frameborder="0" allowfullscreen></iframe>'+
	            		'</div>', 
	        patterns: {
	        	youtube: {
		              index: 'youtube.com/', 
		              id: 'v=', 
		              src: '//www.youtube.com/embed/%id%?autoplay=1&rel=0&modestbranding=1&autohide=1&showinfo=0&controls=0' 
			    }
			},
			srcAction: 'iframe_src', 
	     }
	});
}( jQuery ));