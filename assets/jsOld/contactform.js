/* Creat chatroom module */
$("form#data").submit(function(){
    var baseurl     = $("#baseurl").val();
    var formData = new FormData($(this)[0]);
    var form = $(this);
    var url = form.attr("action");

    $.ajax({
        url: baseurl + "support/send",
        type: 'POST',
        data: formData,
        async: false,
        beforeSend: function()
        {
            $("#submit_contact_form").text("Sending...");
            $("#notification").html("<div class=\"alert alert-info\" role=\"alert\">Loading, please wait...</div>");
        },        
        success: function (response) {
            var status = response.msgStatus;
            var msg = response.message;
            

            $("#submit_contact_form").text("Send message");
            if(status == "ok") 
            {
               // $("#notification").html('<p class="msg success"><a class="hide" href="#">hide this</a>' + msg + '</p>');
                $(".message_content").html('<p class="msg success"><a class="hide" href="#">hide this</a>' + msg + '</p>');
                form.find("input, select, textarea, file").val("");
                var valField = form.find(".select .value");
                var selectField = valField.siblings("select");
                var selectedText = selectField.find("option").eq(0).html();
                valField.html(selectedText);
                
                $("#clear1").css('display','none');
                $("#clear2").css('display','none');
                $("#clear3").css('display','none');
                $('.counter_text').html(0);
                
            } else if(status == "error") {
                if(response.errorFields.length) {
                    var fields = response.errorFields;
                    for (i = 0; i < fields.length; i++) {
                        form.find("#" + fields[i]).addClass("error");
                        form.find("select#" + fields[i]).parents(".select").addClass("error");
                    }
                    var errors = response.errors;
                    var errorList = "<ul>";
                    for (i = 0; i < errors.length; i++) {
                        errorList += "<li>" + errors[i] + "</li>";
                    }
                    errorList += "</ul>";
                  $("#notification").html('<div class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><p>There were errors in your form:</p>' + errorList + '<p>Please make the necessary changes and re-submit your form</p></div>');

                } else $("#notification").html('<div class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><p>' + msg + '</p></div>');
            }
        },
        cache: false,
        contentType: false,
        processData: false
    });

    return false;
});