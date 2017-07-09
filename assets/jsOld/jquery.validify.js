(function ( $ ) {
 
    $.fn.validify = function( options ) {
 
        // This is the easiest way to have default options.
        var settings = $.extend({
            // These are the defaults.
            valid_color  	: "#009e0f",
            invalid_color	: "red",
            required_class	: "validify_required",
            form_button     : "validify_button"
        }, options );
        
        var instance = $(this);
        var base_url = "http://www.tubemasterpro.com/";
        //var base_url = "http://localhost/myvideoads/";
        var required = instance.find('.'+settings.required_class).length;
        var emailT;
        var passwT;
        
        instance.find('.'+settings.required_class).each(function(){
	        check( $(this) );
        });
        
        instance.find('.'+settings.required_class).on('keyup change', function(){
        	check( $(this) );
        });
        
        instance.find('input[type="password"].'+settings.required_class).on('focus', function(){
        	$(this).tooltip('show');
        }).blur(function(){
	        $(this).tooltip('hide');
        });
        
        instance.find('.validify_alpha').on('keypress', function(event){
    		var regex = new RegExp("^[a-zA-Z. ]*$");
            var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
            if (!regex.test(key)) {
	        	event.preventDefault();
		        return false;
	        }
        });
        
        instance.find('.validify_numeric, .validify_mobile').on('keypress', function(event){
    		var regex = new RegExp("^[0-9]*$");
            var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
            if (!regex.test(key)) {
	        	event.preventDefault();
		        return false;
	        }
        });
        
        instance.find('.validify_alphanumeric').on('keypress', function(event){
    		var regex = new RegExp("^[a-zA-Z0-9. ]*$");
            var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
            if (!regex.test(key)) {
	        	event.preventDefault();
		        return false;
	        }
        });
        
        instance.find('.validify_email').on('keypress', function(event){

        	var regex = new RegExp("^[a-zA-Z0-9_@.-]*$");
            var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);

            if(!regex.test(key)) {
            	if(!event.charCode == 0){
               	event.preventDefault();
               	return false;
           		}
         	}
        	
        });
        
        function check ( obj ) {
	        if ( $(obj).is('input[type="text"], textarea') && $(obj).val() != '' ) {
	        	var is_plain = true;
	        	
		        //----- PHONE -----//
	        	if ( $(obj).hasClass('bfh-phone') ) {
	        		var phone_length = $(obj).data('bfhphone').options.format.length;
	        		if ( $(obj).val().length == phone_length ) {
		        		valid( $(obj), true );
	        		}
	        		else {
		        		valid( $(obj), false );
	        		}
	        		is_plain = false;
	        	}
	        	
		        //----- EMAIL with AJAX CALL -----//
	        	if ( $(obj).hasClass('validify_email_ajax') ) {
			        var email_regex = /^(([a-z0-9\+_\-]{2,})+)(\.([a-z0-9\+_\-]{2,})+)*@(([a-z0-9\-]{2,})+\.)+[a-z]{2,6}$/i;
			        if ( $(obj).val().match(email_regex) ) {
					    
                        clearTimeout(emailT);
                        emailT = setTimeout(function(){
							$.ajax({
							  type: "POST",
							  crossDomain: true,
							  url: base_url+"warroom/warroom_ajax/email_check",
							  data: { email: $(obj).val() }
							}).done(function( msg ) {
							    if ( msg ) {
							    	$(obj).tooltip('hide');
							    	if ( $(obj).val().match(email_regex) ) {
										valid( $(obj), true );
							    	}
							    	else {
										valid( $(obj), false );
							    	}
							    }
							    else {
							    	$(obj).tooltip('show');
								    valid( $(obj), false );
							    }
							});
                        }, 300 );
			        }
			        else {
				        valid( $(obj), false );
			        }
			        is_plain = false;
		        }
		        
		        //----- EMAIL -----//
		        if ( $(obj).hasClass('validify_email') ) {
			        var email_regex = /^(([a-z0-9\+_\-]{2,})+)(\.([a-z0-9\+_\-]{2,})+)*@(([a-z0-9\-]{2,})+\.)+[a-z]{2,6}$/i;
			        if ( $(obj).val().match(email_regex) ) {
			        	valid( $(obj), true );
			        }
			        else {
				        valid( $(obj), false );
			        }
			        is_plain = false;
			    }
		        
		        //----- WEBSITE -----//
		        if ( $(obj).hasClass('validify_website') ) {
			        var website_regex = /^(http:\/\/www\.|https:\/\/www\.)[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,3}(:[0-9]{1,5})?(\/.*)?$/;
			        if ( $(obj).val().match(website_regex) ) {
				        valid( $(obj), true );
			        }
			        else {
			        	valid( $(obj), false );
			        }
			        is_plain = false;
		        }
		    	if ( $(obj).hasClass('validify_website_complete') ) {
			        var website_regex = /^(http:\/\/.|https:\/\/.|http:\/\/www\.|https:\/\/www\.)[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,3}(:[0-9]{1,5})?(\/.*)?$/;
			        if ( $(obj).val().match(website_regex) ) {
				        valid( $(obj), true );
			        }
			        else {
			        	valid( $(obj), false );
			        }
			        is_plain = false;
		        }    
		        //----- FACEBOOK -----//
		        if ( $(obj).hasClass('validify_facebook') ) {
			        var facebook_regex = /^(http:\/\/www.facebook.com\/|https:\/\/www.facebook.com\/)[a-zA-Z0-9. ]+$/;
			        if ( $(obj).val().match(facebook_regex) ) {
				        valid( $(obj), true );
			        }
			        else {
			        	valid( $(obj), false );
			        }
			        is_plain = false;
		        }
		        
		        //----- TWITTER -----//
		        if ( $(obj).hasClass('validify_twitter') ) {
			        var twitter_regex = /^(http:\/\/www.twitter.com\/|https:\/\/www.twitter.com\/)[a-zA-Z0-9. ]+$/;
			        if ( $(obj).val().match(twitter_regex) ) {
				        valid( $(obj), true );
			        }
			        else {
			        	valid( $(obj), false );
			        }
			        is_plain = false;
		        }
		        
		        //----- LINKEDIN -----//
		        if ( $(obj).hasClass('validify_linkedin') ) {
			        var linkedin_regex = /^(https:\/\/www.linkedin.com\/profile\/view\?id\=)[a-zA-Z0-9. ]+$/;
			        if ( $(obj).val().match(linkedin_regex) ) {
				        valid( $(obj), true );
			        }
			        else {
			        	valid( $(obj), false );
			        }
			        is_plain = false;
		        }
		        
		        if ( is_plain ) valid( $(obj), true );
        	}
        	else if ( $(obj).is('input[type="tel"]') && $(obj).val() != '' ) {
	        	//----- MOBILE -----//
	        	if ( $(obj).hasClass('validify_mobile') ) {
	        		console.log($(obj).intlTelInput("isValidNumber"));
	        		if ($.trim($(obj).val())) {
					    if ($(obj).intlTelInput("isValidNumber")) {
					    	valid( $(obj), true );
					    }
					    else {
					    	valid( $(obj), false );
					    }
					}
	        	}
        	}
        	else if ( $(obj).is('input[type="password"]') && $(obj).val() != '' ) {
        		if ( $(obj).hasClass('validify_old_password') ) {
        			if ( $(obj).val().length >= 8 && $(obj).val().length <= 20 ) {
	        			clearTimeout(passwT);
	                    passwT = setTimeout(function(){
							$.ajax({
							  type: "POST",
							  crossDomain: true,
							  url: base_url+"dashboard/dashboard_ajax/password_check",
							  data: { password: $(obj).val() }
							}).done(function( msg ) {
							    if ( msg ) {
							    	$(obj).popover('hide');
									valid( $(obj), true );
							    }
							    else {
							    	$(obj).popover('show');
								    valid( $(obj), false );
							    }
							});
	                    }, 300 );
                	}
        		}

	        	if ( $(obj).hasClass('validify_password') ) {
		        	var password_confirm = instance.find('.validify_password_confirm');
		        	if ( $(obj).val().length >= 8 && $(obj).val().length <= 20 ) {
			        	if ( $(obj).val() == password_confirm.val() ) {
				        	valid( $(obj), true );
				        	valid ( password_confirm, true );
			        	}
			        	else {
				        	valid( $(obj), false );
				        	valid ( password_confirm, false );
			        	}
		        	}
		        	else {
			        	valid( $(obj), false );
			        	valid( password_confirm, false );
		        	}
		        }
		        
		        if ( $(obj).hasClass('validify_password_confirm') ) {
		        	var password = instance.find('.validify_password');
		        	if ( $(obj).val().length >= 8 && $(obj).val().length <= 20 ) {
			        	if ( $(obj).val() == password.val() ) {
				        	valid( $(obj), true );
				        	valid ( password, true );
			        	}
			        	else {
				        	valid( $(obj), false );
							valid( password, false );
			        	}
		        	}
		        	else {
			        	valid( $(obj), false );
			        	valid( password, false );
		        	}
		        }
		        
		        if ( $(obj).hasClass('validify_password_login') ) {
		        	if ( $(obj).val().length >= 8 && $(obj).val().length <= 20 ) {
			        	valid( $(obj), true );
		        	}
		        	else {
			        	valid( $(obj), false );
		        	}
		        }
        	}
        	else if ( $(obj).is('input[type="checkbox"]')) {
		 		var checked = $(obj).is(':checked');
		 		if(checked){
					valid( $(obj), true );
					console.log("true");
				}else{
					valid( $(obj), false );
					console.log("false");
				}
        	}
        	else if ( $(obj).is('select') && $(obj).val() != 0 ) {
				valid( $(obj), true );
        	}
        	else {
	        	valid( $(obj), false );
        	}
        }
        
        function finalize () {
        	var total_valids = 0;
        	
        	instance.find('.'+settings.required_class).each(function(){
	        	if ( $(this).data('validify_valid') === true ) {
	        		// valid( $(this), true );
	        		total_valids++;
	        	}
	        	else {
		        	// valid( $(this), false );
	        	}
        	});
        	
        	// console.log( total_valids + ' == ' + required );
        	
        	if ( total_valids == required ) {
	        	instance.find('.'+settings.form_button).removeClass('btn-disabled');
	        	instance.find('.'+settings.form_button).addClass('btn-primary');
	        	instance.find('.'+settings.form_button).prop('disabled',false);
        	}
        	else {
	        	instance.find('.'+settings.form_button).removeClass('btn-primary');
	        	instance.find('.'+settings.form_button).addClass('btn-disabled');
	        	instance.find('.'+settings.form_button).prop('disabled',true);
        	}
        }
        
        function valid ( obj, valid ) {
        	var parent = $(obj).parent();
        	if ( $(obj).hasClass('validify_mobile') ) {
        		parent = $(obj).parent().parent();
        	}
        	//console.log(valid);
        	if ( valid ) {
        		parent.removeClass('has-error');
        		parent.addClass('has-success');
        		parent.find('span.glyphicon-remove').fadeOut(100, function(){
        			parent.find('span.glyphicon-ok').fadeIn(100);
        		});
	        	//$(obj).css('border-color', settings.valid_color);
	        	$(obj).data('validify_valid', true);
        	}
        	else {
        		parent.removeClass('has-success');
        		parent.addClass('has-error');
        		parent.find('.glyphicon-ok').fadeOut(100, function(){
        			parent.find('.glyphicon-remove').fadeIn(100);
        		});
	        	//$(obj).css('border-color', settings.invalid_color);
	        	$(obj).data('validify_valid', false);
        	}
        	
        	finalize();
        }
 
        // Validify the collection based on the settings variable.
        // return this.find('.'+required_class).data('validify_valid', false);
 
    };
 
}( jQuery ));