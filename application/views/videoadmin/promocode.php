<?php include('includes/header.php');?>

    <!--- PENDING PROCESS TABLE -->
    <?php if($o['show_available_promo_table']) { ?>
 <div class="row" style="padding-top: 15px;" >
        <div class="col-lg-12">
            <div class="panel panel-default">
  <!-- Default panel contents -->
  <div class="panel-heading">Available Promo codes</div>
  <!-- Table -->
  <table class="table">
<table class="table">
        <thead>
          <tr>
            <th>Promo Code</th>
            <th>Date</th>
            <th>Discount</th>
            <th>Claim</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
            <?php
                foreach($o['show_promo'] as $sp)
                 {

             ?>
          <tr>
            <th scope="row"><?php echo $sp['secret_code'] ; ?> <br /> <small><?php echo $sp['option_desc'] ; ?></small></th>
            <td><?php echo $sp['start_date'] . " - ". $sp['end_date']; ?></td>
            <td><?php echo $sp['discount_amt'] . "%"; ?></td>
            <td><?php echo $sp['claim_count'] . " / ". $sp['num_claim']; ?></td>
             <td><?php  if($sp['is_live'] == 0){ echo "<span class=\"label label-danger\">Hold</span>"; } else { echo "<span class=\"label label-info\">Live</span>";} ; ?></td>
            <td>
            <?php if($sp['is_live'] == 0){
              ?>
              <a href="<?php echo site_url('/promocode/updatecode/' . $sp['promo_code_id']);?>"> <i class="fa fa-file-text"></i> Update</a> &nbsp;
            <?php
              }
              ?>
                <a href="<?php echo site_url('/promocode/delete_promo/' . $sp['promo_code_id']);?>" onclick="return confirm('Are you sure you want to delete this promo?'); return false;"><i class="fa fa-times-circle" style="color:red;"></i> Delete</a>
            </td>
          </tr>
        <?php
            }
        ?>
        </tbody>
      </table>
  </table>
</div>
            </div>
        </div>
    </div>
    <?php } ?>
  


<!-- USED PROMO CODE -->
    <?php if($o['show_used_promo_table']) { ?>
 <div class="row" style="padding-top: 15px;" >
        <div class="col-lg-12">
            <div class="panel panel-default">
  <!-- Default panel contents -->
  <div class="panel-heading">Used / Expired Promo code</div>
  <!-- Table -->
  <table class="table">
<table class="table">
        <thead>
          <tr>
            <th>Promo Code</th>
            <th>Date</th>
            <th>Discount</th>
            <th>Claim</th>
          </tr>
        </thead>
        <tbody>
            <?php
                foreach($o['show_promo'] as $sp)
                 {

             ?>
          <tr>
            <th scope="row"><?php echo $sp['secret_code'] ; ?> <br /> <small><?php echo $sp['option_desc'] ; ?></small></th>
            <td><?php echo $sp['start_date'] . " - ". $sp['end_date']; ?></td>
            <td><?php echo $sp['discount_amt'] . "%"; ?></td>
            <td><?php echo $sp['num_claim'] . " / ". $sp['claim_count']; ?></td>
            
          </tr>
        <?php
            }
        ?>
        </tbody>
      </table>
  </table>
</div>
            </div>
        </div>
    </div>
    <?php } ?>
<?php
  if($o['create_promo_code_table']){
?>
<div class="row"  >
        <div class="col-lg-12" >
            <div class="panel panel-default" style="margin-bottom: 0px;">
            <!-- Default panel contents -->
            <div class="panel-heading">Create Promo Code </div>
            <!-- Table -->
<div class="row" style="margin-right: 0px;
margin-left: 0px;
height: 100%;">
             
                    <div class="col-lg-12">

<?php echo $form; ?>
<div id="notification"></div>

<p>
    <label for="name">Secret Code:</label>   <br>
    <?php echo form_input($secret_code);?>
</p>
<p>
    <label for="name">Date range: </label> <br>
     <?php echo form_input($date_from) . " - " .form_input($date_to);?>
</p >
<p>
    <label for="name">Discount Amount: </label> <br>
     <?php echo $discount_amt;?>
</p>
<p>
    <label for="name">Description: </label> <br>
     <?php echo form_input($option_desc);?>
</p >
<p>
    <label for="name">Available for # of users: </label> <br>
          <?php echo $claim_count;?>
</p >
<p>
    <label for="name">Make it live?: </label> <br>
          <?php echo $is_live;?>
</p >
<p>
    <label for="name">is 1 time discount?: </label> <br>
          <?php echo $is_onetime;?>
</p >
 <p><button name="p" value="submit_request" type="submit" class="btn btn-primary" id="submit_request">Create new code</button> &nbsp; <?php echo $add_another;?> Add another promo code?</p>

<?php echo $form_close; ?>
</div>

   </div>

            </div>
            </div>
        </div>
  </div>
<?php
}
?>

<?php 
//update promo code table
if($o['update_promo_code_table']){
?>
<div class="row"  >
        <div class="col-lg-12" >
            <div class="panel panel-default" style="margin-bottom: 0px;">
            <!-- Default panel contents -->
            <div class="panel-heading">Update Promo Code </div>
            <!-- Table -->
<div class="row" style="margin-right: 0px;
margin-left: 0px;
height: 100%;">
             
                    <div class="col-lg-12">

<?php echo $form; ?>
<div id="notification"></div>
<p>
    <label for="name">Status:</label> <span class="label label-info"><?php  if($sp['is_live'] == '1'){ echo "LIVE"; } else { echo "HOLD"; 
    }?></span>
</p>
<p>
    <label for="name">Secret Code:</label>   <br>
    <?php echo form_input($secret_code);?>
</p>
<p>
    <label for="name">Date range: </label> <br>
     <?php echo form_input($date_from) . " - " .form_input($date_to);?>
</p >
<p>
    <label for="name">Discount Amount: </label> <br>
     <?php echo $discount_amt;?>
</p>
<p>
    <label for="name">Description: </label> <br>
     <?php echo form_input($option_desc);?>
</p >
<p>
    <label for="name">Available for # of users: </label> <br>
          <?php echo $claim_count;?>
</p >
<p>
    <label for="name">Make it live?: </label> <br>
          <?php echo $is_live;?>
</p >
<p>
    <label for="name">is 1 time discount?: </label> <br>
          <?php echo $is_onetime;?>
</p >
 <p><button name="p" value="submit_request" type="submit" class="btn btn-primary" id="submit_request">Update Promo Code</button> &nbsp;  <?php echo form_input($promo_code_id);?></p>

<?php echo $form_close; ?>
</div>

   </div>

            </div>
            </div>
        </div>
  </div>
<?php
}
?>
</div> <!-- /container -->
<div id="footer">
    <div class="container">
      <p class="footer-content">
      
    </p>
    </div>
</div>


<script src="<?php echo assets_url('js/jquery.min.js'); ?>"></script>
<script src="<?php echo assets_url('js/bootstrap.min.js'); ?>"></script>
<script src="<?php echo assets_url('js/jqueryui/jquery-ui.min.js'); ?>"></script>
<script src="<?php echo assets_url('js/main.js'); ?>"></script>
<script src="<?php echo assets_url('js/intl-tel-input/intlTelInput.min.js'); ?>"></script>

<script src="<?php echo site_url('nodejs/node_modules/socket.io/node_modules/socket.io-client/socket.io.js'); ?>"></script>
<script src="<?php echo assets_url('js/chatroom.js'); ?>"></script>
<script src="<?php echo assets_url('js/moment.min.js'); ?>"></script>
<script src="<?php echo assets_url('js/MagnificPopup/dist/jquery.magnific-popup.js'); ?>"></script>

<script src="<?php echo assets_url('js/jquery.slimscroll.min.js'); ?>"></script>
<script src="<?php echo assets_url('js/slimscroll.config.js'); ?>"></script>

<script>

function show_chatlist(){
    $.ajax
        ({
        type: "POST",
        url: baseurl + "livesupport/checkchatlist",
        cache: false,
        beforeSend: function()
        {
        },
        success: function(result)
        {
          var res = result.split("__SPLITRESULT__");

          $("#chatlist_content").html(res[1]);
          if(res[0] > 0){
            $("#conversation_count").text("(" + res[0] + ")");

            //$("#conversation_count_top").html("<i class=\"fa fa-envelope\"></i><span class=\"label label-danger absolute\">" + res[0] + "</span>");
            //$("#conversation_count_top").html("<span class=\"badge \">"+ res[0] +"</span>");
            $("#conversation_count_top").html("<span class=\"fa-stack\"><i class=\"fa fa-circle fa-stack-2x danger\"></i><i class=\"fa fa-stack-1x fa-inverse\"><b style='font-size:0.8em'>" + res[0] + "</b></i></span>");
            $("#conversation_count_sm").text("(" + res[0] + ")");
          }else{
            $("#conversation_count").text("");
            $("#conversation_count_sm").text("");
            $("#conversation_count_top").html("");
          }

          var count_aff_chat = parseInt($(".chat_room_notif_17").text());
          var count_group_chat = parseInt($(".chat_room_notif_11").text());

          if(count_aff_chat > 0){
              if(count_aff_chat > 99){
                  count_aff_chat = "99+"
              }
              $("#conversation_count_affiliate").text("("+ count_aff_chat +")");
            }else{
              $("#conversation_count_affiliate").text("");
            }

          if(count_group_chat > 0){
              if(count_group_chat > 99){
                  count_group_chat = "99+"
              }
              $("#conversation_count_group").text("("+ count_group_chat +")");
            }else{
              $("#conversation_count_group").text("");
           }  
         
          
        }        
      });   
}
setTimeout("show_chatlist()",1000);

      $(document).ready(function() 
    {

       // Date Range Picker
      $("#date_from").datepicker({
        dateFormat: 'yy-mm-dd',
          defaultDate: "+1w",
          changeMonth: true,
          numberOfMonths:2,
          prevText: '<i class="fa fa-chevron-left"></i>',
          nextText: '<i class="fa fa-chevron-right"></i>',
          onClose: function (selectedDate) {
              $("#to").datepicker("option", "minDate", selectedDate);
          }
    
      });
      $("#date_to").datepicker({
        dateFormat: 'yy-mm-dd',
          defaultDate: "+1w",
          changeMonth: true,
          numberOfMonths: 2,
          prevText: '<i class="fa fa-chevron-left"></i>',
          nextText: '<i class="fa fa-chevron-right"></i>',
          onClose: function (selectedDate) {
              $("#date_from").datepicker("option", "maxDate", selectedDate);
          }
      });

        //show_chat_content('12');

        socket.on( 'user_connected', function( data ) { 
              if(data.is_connected == 1){
                var user_login_bv = $("#usrloginName").val();
                var user_login_id_bv = $("#user_login_id").val();

                console.log("Connected " + data.is_connected + " as " + user_login_bv);  
                socket.emit( 'save_online', {username: user_login_bv, user_id: user_login_id_bv } );
              }
            });

        socket.on("isTyping", function(data) {  
          $(".list_typing_anim_" + data.room_id).html("<img src='"+ baseurl +"assets/images/chat/isTypingRoomList.GIF'>");
        });

        socket.on("remove_typing_room_list", function(data) {  
          $(".list_typing_anim_" + data.room_id).html(""); 
        });
    });  
</script>
<?php include('includes/footer.php');?>
