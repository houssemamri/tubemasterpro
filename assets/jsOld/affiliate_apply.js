    $('#submit_aff_apply').on('click', function(e){
         var baseurl 		= $("#baseurl").val();
         var firstname 		= $("#firstname").val();
         var lastname 		= $("#lastname").val();
         var email 			= $("#email").val();
         var country 		= $("#countries_phone").val();
         var mobile 		= $("#mobile").val();
         var website 		= $("#website").val();
         var skype_id 		= $("#skype_id").val();
         var promote_note 	= $("#promote_note").val();

         var paypal_email 	= $("#paypal_email").val();
         var company 		= $("#company").val();
         var whatsApp 		= $("#whatsApp").val();
         var twitter 		= $("#twitter").val();
         var fb 			= $("#fb").val();
         var ln 			= $("#ln").val();


         var data = { 
         				"firstname" 	: firstname, 
         				"lastname" 		: lastname,
         				"email" 		: email,
         				"country" 		: country,
         				"mobile" 		: mobile,
         				"website" 		: website,
         				"skype_id" 		: skype_id,
         				"promote_note" 	: promote_note,
         				"paypal_email" 	: paypal_email,
         				"company" 	: company,
         				"whatsApp" 	: whatsApp,
         				"twitter" 	: twitter,
         				"fb" 		: fb,
         				"ln" 		: ln,
         			};
        $.ajax
        ({
        type: "POST",
        url: baseurl + "affiliateapply/signup",
        cache: false,
        data: data,
        beforeSend: function()
        {
          $('#submit_aff_apply').addClass('disabled');
          $('#submit_aff_apply').removeClass('btn-primary');
          $('#submit_aff_apply').attr('value','Sending, please wait....');
        },
        success: function(result)
        {
          $('#submit_aff_apply').removeClass('disabled');
          $('#submit_aff_apply').addClass('btn-primary'); 
          $('#submit_aff_apply').attr('value','Submit Application fo Approval');
          if(result == 'success'){
	         $("#firstname").val('');
	         $("#lastname").val('');
	         $("#email").val('');
	         $("#countries_phone").val('');
	         $("#mobile").val('');
	         $("#website").val('');
	         $("#skype_id").val('');
	         $("#promote_note").val('');
   
	         $("#paypal_email").val('');
	         $("#company").val('');
	         $("#whatsApp").val('');
	         $("#twitter").val('');
	         $("#fb").val('');
	         $("#ln").val('');         
            $('#show_success').modal('show');
          } else{
            $('#show_failed').modal('show');
          }     
        }        
      }); 
    });
