$(document).ready(function(){
	//localStorage.setItem('run-tour-1','yes');
	//localStorage.setItem('step-tour-1','0');
	//$('#tour-modal').modal('show');
    var current_url = $(location).attr('href');
    var base_url 	= 'http://www.nathanhague.com/tubetargetpro/';
    var email1Valid = email2Valid = false;
    
    $('#invites-modal').modal('show');
    
	$('#invite-btn').attr('disabled',true);
    
    $('#invite-email-1').on('keyup', function(event){
    	var emailAddress = $.trim($(this).val());
    	if ( emailAddress != '' ) {
    		var pattern = new RegExp(/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i);

			if ( pattern.test(emailAddress) ) {
				
				$(this).parent().removeClass('has-error');
				$(this).parent().addClass('has-success');
				email1Valid = true;
			}
			else {
				$(this).parent().removeClass('has-success');
				$(this).parent().addClass('has-error');
				email1Valid = false;
			}
			
			if ( email1Valid && email2Valid ) {
				if ( emailAddress == $('#invite-email-2').val() ) {
					$(this).parent().removeClass('has-success');
					$(this).parent().addClass('has-error');
					$('#invite-btn').attr('disabled',true);
				}
				else {
					$('#invite-btn').attr('disabled',false);
				}
			}
			else {
				$('#invite-btn').attr('disabled',true);
			}
		}
		else {
			$(this).parent().removeClass('has-success');
			$(this).parent().addClass('has-error');
			email1Valid = false;
			$('#invite-btn').attr('disabled',true);
		}
    });

	$('#invite-email-2').on('keyup', function(event){
    	var emailAddress = $.trim($(this).val());
    	if ( emailAddress != '' ) {
    		var pattern = new RegExp(/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i);

			if ( pattern.test(emailAddress) ) {
			
				$(this).parent().removeClass('has-error');
				$(this).parent().addClass('has-success');
				email2Valid = true;
			}
			else {
			
				$(this).parent().removeClass('has-success');
				$(this).parent().addClass('has-error');
				email2Valid = false;
			}
			
			if ( email1Valid && email2Valid ) {
				if ( emailAddress == $('#invite-email-1').val() ) {
					$(this).parent().removeClass('has-success');
					$(this).parent().addClass('has-error');
					$('#invite-btn').attr('disabled',true);
				}
				else {
					$('#invite-btn').attr('disabled',false);
				}
			}
			else {
				$('#invite-btn').attr('disabled',true);
			}
		}
		else {
			$(this).parent().removeClass('has-success');
			$(this).parent().addClass('has-error');
			email2Valid = false;
			$('#invite-btn').attr('disabled',true);
		}
    });
    
    $('#invite-btn').on('click', function(e){
    	e.preventDefault();
    	show_loader('Sending',true,400);
	    var request = $.ajax({
			url: base_url + 'dashboard/invite_ajax/send_invites',
			type: "POST",
			data: {
				email1   : $.trim($('#invite-email-1').val()),
				email2   : $.trim($('#invite-email-2').val()),
			},
		});
	
		request.done(function(msg){
			console.log(msg);
			if (msg) {
				show_loader('Sent!',false,1000);
				$('#invites-modal').modal('hide');
				//location.reload(true);
			}
			else {
				show_loader('Try Again',false,1000);
			}
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