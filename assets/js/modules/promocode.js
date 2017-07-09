var baseurl = $("#baseurl").val();

function show_promocode(){
    $.ajax
        ({
        type: "POST",
        url: baseurl + "subscription/show_promocode",
        cache: false,
        beforeSend: function()
        {
        	$("#enter_promocode").html("<div class=\"alert alert-info\" role=\"alert\"><i class=\"fa fa-cog fa-spin\"></i> Checking promo code, please wait...</div>");
        },
        success: function(result)
        {
        	var res = 	result.split("|");
          $("#enter_promocode").html(res[1]);

          if(res[0] == "SUCCESS"){
          	$(".skip_promo_code").hide();
          }else{
          	$(".skip_promo_code").show();
          }
        }        
      });   
}
setTimeout("show_promocode()",500);

function submit_promo_code(){
	var plan_price = $("#plan_price").val();
	var promo_code = $("#promo_code").val();

	if(promo_code.length < 3){
		$("#pc_notification").html("<div class=\"alert alert-danger\" role=\"alert\">error! envalid promo code, please try again..</div>");
		$("#promo_code").val('');
		$('#submit_button').addClass('disabled');
        $('#submit_button').removeClass('btn-primary');
	}else{

    $.ajax
        ({
        type: "POST",
        url: baseurl + "subscription/submit_promo_code",
        data: {"plan_price" : plan_price, "promo_code" : promo_code},
        cache: false,
        beforeSend: function()
        {
        	$("#pc_notification").html("<div class=\"alert alert-info\" role=\"alert\"><i class=\"fa fa-cog fa-spin\"></i> Checking....</div>");
        },
        success: function(result)
        {
        	var res = result.split("|");
        	if(res[0] == "error"){
        		$("#pc_notification").html("<div class=\"alert alert-danger\" role=\"alert\">" + res[1] +"</div>");
        		$("#promo_code").val('');
                $(".skip_promo_code").show();
                $('#submit_button').addClass('disabled');
				$('#submit_button').removeClass('btn-primary');
                
        	}else{
        		$("#enter_promocode").html("<div class=\"alert alert-success\" role=\"alert\">" + res[1] +"</div>");
                $(".skip_promo_code").hide();
        	}
          
        }        
      }); 

		
	}
}

function remove_promo_confirm(promo_code_id){

	if(confirm("Are you sure you want to remove this promo code?")){
	    $.ajax
	        ({
	        type: "POST",
	        url: baseurl + "subscription/remove_promo",
	        data: {"promo_code" : promo_code_id},
	        cache: false,
	        beforeSend: function()
	        {
	        	$("#enter_promocode").html("<div class=\"alert alert-info\" role=\"alert\"><i class=\"fa fa-cog fa-spin\"></i> Removing, please wait....</div>");
	        },
	        success: function(result)
	        {
	        	$("#enter_promocode").html("<div class=\"alert alert-success\" role=\"alert\">Remove successful</div>");
	          	setTimeout("show_promocode()",1000);
	        }        
	      }); 
	}
}