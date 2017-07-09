$(document).ready(function(){
	
	// get the country data from the plugin
	var countryData = $.fn.intlTelInput.getCountryData(),
	  telInput = $("#mobile"),
	  addressDropdown = $("#countries_phone");
	
	// init plugin
	telInput.intlTelInput({
	  //autoFormat: false,
      //autoHideDialCode: false,
      //defaultCountry: "auto",
      //ipinfoToken: "yolo",
      //nationalMode: false,
      numberType: "MOBILE",
      //onlyCountries: ['us', 'gb', 'ch', 'ca', 'do'],
      preferredCountries: ['au', 'gb', 'us'],
      preventInvalidNumbers: true,
	  utilsScript: "../assets/js/intl-tel-input/lib/libphonenumber/build/utils.js", // just for formatting/placeholders etc
      autoPlaceholder: false
	});
	
	// populate the country dropdown
	$.each(countryData, function(i, country) {
	  addressDropdown.append($("<option></option>").attr("value", country.iso2).text(country.name));
	});
	
	// listen to the telephone input for changes
	telInput.change(function() {
	  var countryCode = telInput.intlTelInput("getSelectedCountryData").iso2;
	  addressDropdown.val(countryCode);
	});
	
	// trigger a fake "change" event now, to trigger an initial sync
	telInput.change();
	
	/*
telInput.keyup(function(){
		console.log(telInput.intlTelInput("isValidNumber"));
	});
*/
	
	// listen to the address dropdown for changes
	addressDropdown.change(function() {
	  var countryCode = $(this).val();
	  telInput.intlTelInput("selectCountry", countryCode);
	});
	
	telInput.parent().css({
		"width" : "100%"
	});

	//console.log(telInput.prop('placeholder'));
	telInput.prop('placeholder', '');

	$('form#signup_form').validify();
	
	$('#myModal').on('show.bs.modal', function (event) {
	    var button = $(event.relatedTarget); // Button that triggered the modal
	    var transaction_id = button.data('trans-id'); // Extract info from data-* attributes
	    var baseurl = $("#baseurl").val();
	    var modal = $(this)
      	modal.find('.modal-title').text('PayPal Transaction: ' + transaction_id);
	   $.ajax
	    ({
	      type: "POST",
	      url:  baseurl + "affiliate/paypaltransaction_details",
	      data: ({"transaction_id" : transaction_id}),
	      cache: false,
	      beforeSend: function()
	      {
	        $("#show_trans_data").html("<div class=\"alert alert-info\" role=\"alert\"><i class=\"fa fa-cog fa-spin fa-3x fa-fw margin-bottom\"></i> Loading, please wait...</div>");
	      },
	      success: function(result)
	      {
	        transaction_id = "";
	        $("#show_trans_data").html(result);

	      }         
	    }); 
	})

	$('#ModalViewActive').on('show.bs.modal', function (event) {
	    var button = $(event.relatedTarget); // Button that triggered the modal
	    var user_id = button.data('trans-id'); // Extract info from data-* attributes
	    var search_type = button.data('search-type');
	    var baseurl = $("#baseurl").val();
	    var modal = $(this)
      	modal.find('.modal-title').text('' + search_type + ' users using your affiliate link');
    
	   $.ajax
	    ({
	      type: "POST",
	      url:  baseurl + "affiliate/view_signup_users_details",
	      data: ({"user_id" : user_id, "search_type" : search_type}),
	      cache: false,
	      beforeSend: function()
	      {
	        $("#show_trans_data").html("<div class=\"alert alert-info\" role=\"alert\"><i class=\"fa fa-cog fa-spin fa-3x fa-fw margin-bottom\"></i> Loading, please wait...</div>");
	      },
	      success: function(result)
	      {
	        transaction_id = "";
	        search_type = "";
	        $("#show_trans_data").html(result);

	      }         
	    }); 

	})

	$('#ModalViewUserTransaction').on('show.bs.modal', function (event) {
	    var button = $(event.relatedTarget); // Button that triggered the modal
	    var user_id = button.data('affuser-id'); // Extract info from data-* attributes
	    var user_name = button.data('affuser-name');
	    var baseurl = $("#baseurl").val();
	    var modal = $(this)
      	modal.find('.modal-title').text('' + user_name + ' Affiliate transaction');
   
	   $.ajax
	    ({
	      type: "POST",
	      url:  baseurl + "affiliate/viewusertransaction",
	      data: ({"user_id" : user_id}),
	      cache: false,
	      beforeSend: function()
	      {
	        $("#show_user_trans_data").html("<div class=\"alert alert-info\" role=\"alert\"><i class=\"fa fa-cog fa-spin fa-3x fa-fw margin-bottom\"></i> Loading, please wait...</div>");
	      },
	      success: function(result)
	      {
	        user_id = "";
	        user_name = "";
	        $("#show_user_trans_data").html(result);

	      }         
	    }); 

	})

});