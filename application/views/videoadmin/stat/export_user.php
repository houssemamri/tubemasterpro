  <div class="row" style="padding-top: 15px;" >
       <div class="col-lg-offset-2 col-lg-8">
<div class="panel panel-default ">
  <!-- Default panel contents -->
  
   <div class="panel-heading" style="line-height:2em;"><strong><?php echo $o['export_title']; ?></strong> 
   <input type="hidden" name="show_type" value="<?php echo $o['show_type'];?>" id="show_type" >
  <button type="button" class="btn btn-info pull-right email_button_show" id="email_button_popup" >EMAIL</button>
   </div>
    <!-- Table -->
    <table class="table"  class="tableClass">
 <tr >
          <td colspan="3"><div align="center"> <a href="#" id="showAll">show all</a> | <a href="#" id="showSubscribe">show all subscribe (<?php echo $o['count_subscirbe']; ?>)</a>
           | <a href="#" id="showUnsubscribe">show all unsubcribe (<?php echo $o['count_unsubscribe']; ?>)</a>
          </div>
          </td>
      </tr>
      <tr>
        <td><strong>Name</strong> </td>
        <td><input type="checkbox" onchange="checkAll(this)" name="chk[]" /></td>
      </tr>
      <?php 
        $i = 0;
        foreach($o['show_res'] as $spu) { 
          $i++;
      ?>
      <tr class="<?php if($spu['is_subscribe']) { echo "subscribe";} else{ echo "unsubscribe";}?>">
        <td><?php echo ucfirst($spu['first_name']) . " " . ucfirst($spu['last_name']); ?></td>
        <td><input type="checkbox" name="checked[]" id="checked[]" value="<?php echo $spu['id']; ?>" class="<?php if($spu['is_subscribe']) { echo "is_subscribe";} else{ echo "is_unsubscribe";}?>"></td>
      </tr>
      <?php } ?>    
    </table>
          <tr >
        <td colspan="2"> <div align="center"><button name="send" type="button" class="btn btn-success subscribe_row" id="subscribed_users">Subscribed users</button> &nbsp; <button name="send" type="button" class="btn btn-danger unsubscribe_row" id="unsubscribed_users">Unsubscribed users</button> </div></td>
      </tr>   
  </div>
</div>
</div>

<div class="modal fade text-left" id="show_subscribe_email" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog">

    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="myModalLabel"></h4>
      </div>
      <div class="modal-body">
            <div id="email_notification"></div>
            <div id="email_body_dets">
              <label for="name">Subject:</label> <br />
  <input type="text" name="subject" value="" id="subject" style="width: 500px;">
  </p>   
<p>
    <label for="name">Message:</label> <br />
    <textarea name="message" id="message" rows="5" cols="20" class="form-control" placeholder=""></textarea>   
</p>
</div>
      </div>
      <div class="modal-footer">
        <a href="#" id="close_send_email_modal" type="button" class="btn btn-danger"  data-dismiss="modal" >Close</a> 
        <a href="#" id="send_email_to_users" type="button" class="btn btn-success" >Send</a>
      </div>
    </div>
  </div>
</div>

<script>

 function checkAll(ele) {
     var checkboxes = document.getElementsByTagName('input');
     if (ele.checked) {
         for (var i = 0; i < checkboxes.length; i++) {
             if (checkboxes[i].type == 'checkbox') {
                 checkboxes[i].checked = true;
             }
         }
     } else {
         for (var i = 0; i < checkboxes.length; i++) {
             console.log(i)
             if (checkboxes[i].type == 'checkbox') {
                 checkboxes[i].checked = false;
             }
         }
     }
 } 

$(function(){
   $(".subscribe_row").show();
   $(".unsubscribe_row").show();
   $(".email_button_show").hide();

   $("#showSubscribe").click(function(){
    $(".unsubscribe").hide();
    $(".subscribe").show();
    $(".unsubscribe_row").show();
    $(".subscribe_row").hide();
    $(".email_button_show").show();

   });
   $("#showUnsubscribe").click(function(){
    $(".subscribe").hide();
    $(".unsubscribe").show();
    $(".unsubscribe_row").hide();
    $(".subscribe_row").show();
    $(".email_button_show").hide();

   });
   $("#showAll").click(function(){
    $(".unsubscribe").show();
    $(".subscribe").show();
    $(".unsubscribe_row").show();
    $(".subscribe_row").show();
    $(".email_button_show").hide();
   });
});
$("#unsubscribed_users").click(function(e){
  var baseurl     = $("#baseurl").val();
  var show_type     = $("#show_type").val();
  var data = { 'checked[]' : [], "update_type" : "unsubscribe"};
    $(":checked").each(function() {
      if($(this).val() != 'on'){
        if($(this).attr('class') == 'is_subscribe'){
          data['checked[]'].push($(this).val());
        }
      }
  });

    if(data['checked[]'].length == 0)
    {
      alert("Please check atleast 1 users");
    }else{
        if(confirm("Are yu sure you want to unsubscribed this users?")){
              $.ajax
              ({
              async: true,  
              type: "POST",
              url: baseurl + "admin/sub_unsubscribe_users",
              cache: false,
              data: data,
              beforeSend: function()
              {
                
              },
              success: function(result)
              {
                 check_adword_export_user(show_type);
              }
             
          });
        }
    }
});

$("#subscribed_users").click(function(e){
  var baseurl     = $("#baseurl").val();
  var show_type     = $("#show_type").val();
  var data = { 'checked[]' : [], "update_type" : "subscribe"};
    $(":checked").each(function() {
      if($(this).val() != 'on'){
        if($(this).attr('class') == 'is_unsubscribe'){
          data['checked[]'].push($(this).val());
        }
      }
  });

    if(data['checked[]'].length == 0)
    {
      alert("Please check atleast 1 user");
    }else{

        if(confirm("Are yu sure you want to subscribed this users again?")){
            $.ajax
            ({
              async: true,  
              type: "POST",
              url: baseurl + "admin/sub_unsubscribe_users",
              cache: false,
              data: data,
              beforeSend: function()
              {
                
              },
              success: function(result)
              {
                 check_adword_export_user(show_type);
              }
            });
        }
    }
});
$("#email_button_popup").click(function(e){
  var baseurl     = $("#baseurl").val();
  var show_type    = $("#show_type").val();
  // id="myModalLabel">Send Email to <?php echo $o['user_text']; ?>

  var data = { 'checked[]' : [], "update_type" : "subscribe"};
    $(":checked").each(function() {
      if($(this).val() != 'on'){
        if($(this).attr('class') == 'is_subscribe'){
          data['checked[]'].push($(this).val());
        }
      }
      }); 

   if(data['checked[]'].length == 0)
    {
      alert("Please check atleast 1 user");
    }else{
       
        $('#show_subscribe_email').modal('show');
        $("#myModalLabel").text('Send Email to ' + data['checked[]'].length + ' <?php echo $o['export_title']; ?>');
        //email_body_dets
    }

});

$("#send_email_to_users").click(function(e){
  var baseurl     = $("#baseurl").val();
  var subject     = $("#subject").val();
  var message     = $("#message").val();

  
  var data = { 'checked[]' : [], "subject" : subject, "message" : message};
    $(":checked").each(function() {
      if($(this).val() != 'on'){
        if($(this).attr('class') == 'is_subscribe'){
          data['checked[]'].push($(this).val());
        }
      }
      });   

    if(data['checked[]'].length == 0)
    {
      alert("Please check atleast 1 user");
    }else{
        if(subject.length < 5 || message.length < 5){
          $('#email_notification').html('<div class="alert alert-danger" role="alert">Subject or Message atleast 5 characters.</div>');
        }else{
          $.ajax
          ({
            type: "POST",
            url: baseurl + "admin/email_users",
            cache: false,
            data: data,
            beforeSend: function()
            {
              $('#email_notification').html('<div class="alert alert-warning" role="alert">Sending email to users, one moment...</div>');
              $('#email_body_dets').css('display','none');
            },
            success: function(result)
            {   
              var res = result.split("__emaildemosplit__");
              console.log(result);
                if(res[1] == 'success'){
                  $("#subject").val('');
                  $("#message").val('');
                  $('#email_notification').html('');
                  //$('#close_send_email_modal').attr('class','btn btn-success');    
                  //$('#send_email_to_users').css('display','none');
                  $('#email_body_dets').css('display','block');
                  $('#email_notification').html('<div class="alert alert-success" role="alert">Message successfully sent to users</div>');
                }else{
                  $('#email_notification').html('<div class="alert alert-danger" role="alert">Sending failed, please try again..</div>');
                  $('#email_body_dets').css('display','block');
                }
            }        
          }); 
        }
    }    
})
</script>
