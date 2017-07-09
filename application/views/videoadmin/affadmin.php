<?php include('includes/header.php');?>

    <!--- PENDING PROCESS TABLE -->
    <?php if($o['show_table_list']) { ?>
 <div class="row" style="padding-top: 15px;" >
        <div class="col-lg-12">
            <div class="panel panel-default">
  <!-- Default panel contents -->
  <div class="panel-heading"><?php echo $o['title']; ?> </div>
  <!-- Table -->
  <table class="table">
<table class="table">
        <thead>
          <tr>
            <th>Name</th>
            <th>Date Signup</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
            <?php
                foreach($o['show_pend'] as $sp)
                 {

             ?>
          <tr>
            <th scope="row"><?php echo ucwords($sp['user_name']); ?></th>
            <td><?php echo $sp['aff_added']; ?></td>
            <td><a href="<?php echo site_url('/affiliateadmin/check_request/' . $sp['id']);?>"><span class="glyphicon glyphicon-edit"></span> View Details</a></td>
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
  

   
 <!-- SHOW REQUEST TABLE -->
     <?php if($o['show_request_table']) { ?>
 <div class="row" style="padding-top: 15px;" >
       <div class="col-lg-offset-2 col-lg-8">

              <h4><?php if($o['gc']['aff_status'] == "pending"){ echo "Affiliate Request"; } else { echo "Rejected Request"; } ?></h4>
<p>
<input type="hidden" name="baseurl" id="baseurl" value="<?php echo $o['baseurl']; ?>">
</p>
<form id="data" method="post" enctype="multipart/form-data" action="<?php echo site_url('affiliateadmin/check_request/' . $o['gc']['id']); ?>">
<div id="notification"></div>
<p>
    <label for="name">Name:</label> 
    <?php echo ucwords($o['gc']['first_name'] . " " . $o['gc']['last_name']);?>
<br>
    <label for="name">Paypal: </label> 
     <?php echo $o['gc']['paypal_email'];?>
<br>
    <label for="name">Company: </label> 
     <?php echo $o['gc']['company'];?>

<?php if(strlen($o['gc']['phone']) < 3) { ?>
<br>
    <label for="name">Phone #: </label> 
    <span class="phone_span"><?php echo $o['gc']['mobile'];?></span>
<?php
  }
  if(strlen($o['gc']['mobile']) < 3) { 
?>
<br>
    <label for="name">Mobile #: </label> 
    <span class="phone_span"><?php echo $o['gc']['mobile'];?></span>
<?php
  }
?>
</p>
<h4>Others</h4>
<label for="name">Website: </label> 
<?php echo $o['gc']['company'];?>
<br>
<?php if($o['gc']['whatsapp']){ ?> 
<label for="name">Whatsapp: </label> 
<i class="fa fa-whatsapp fa-5" style="color:#aad450;font-size: 2em"> <small style="font-size: 14px;"><?php echo $o['gc']['whatsapp']; ?></small></i>&nbsp;&nbsp;<br>
<?php } ?>
<label for="name">Social: </label> 
<?php if($o['gc']['facebook']){ ?> 
<a href="<?php echo $o['gc']['facebook']; ?>" target="_blank"><i class="fa fa-facebook-official fa-5" style="color:#3b5998;font-size: 2em"></i></a>&nbsp;&nbsp;
<?php } ?>

<?php if($o['gc']['twitter']){ ?> 
<a href="<?php echo $o['gc']['twitter']; ?>" target="_blank"><i class="fa fa-twitter-square fa-5" style="color:#00aced;font-size: 2em"></i></a>&nbsp;&nbsp;
<?php } ?>
<?php if($o['gc']['google']){ ?> 
<a href="<?php echo $o['gc']['google']; ?>" target="_blank"><i class="fa fa-google-plus-square fa-5" style="color:#dd4b39;font-size: 2em"></i></a>&nbsp;&nbsp;
<?php } ?>
<?php if($o['gc']['linkedin']){ ?> 
<?php
if(strpos($o['gc']['linkedin'], "http://") !== false) {
  $linkedin_url = $o['gc']['linkedin'];
}
else {
  $linkedin_url = "http://".$o['gc']['linkedin'];
}
?>
<a href="<?php echo $linkedin_url; ?>" target="_blank"><i class="fa fa-linkedin-square fa-5" style="color:#007bb6;font-size: 2em"></i></a>&nbsp;&nbsp;
<?php } ?>
<?php if($o['gc']['aff_status'] == "pending"){ ?>
<hr noshade>
<p>
    <label for="name">Admin Response: </label> 
      <select name="upload_status" class="form-control upload_status">
      <option value="pending" <?php  if($o['gc']['aff_status'] == 'pending') { echo "selected";}?>>Pending</option>
      <option value="approved" <?php  if($o['gc']['aff_status'] == 'approved') { echo "selected";}?>>Approved</option>
      <option value="rejected" <?php  if($o['gc']['aff_status'] == 'rejected') { echo "selected";}?>>Reject</option>
    </select>
</p>
<p>
    <label for="name">Notes <small>(Optional)</small>: </label> 
        <textarea name="notes" id="notes" rows="4" cols="20" class="form-control">  <?php echo $o['gc']['notes'];?></textarea>
</p>
<p>
<button name="p" value="submit_request" type="submit" class="btn btn-primary" id="submit_request">Update Detail</button>
 <input type="hidden" name="user_request_id" id="user_request_id" value="<?php echo $o['gc']['id']; ?>">
</p>
<?php
}
else
{
?>
<br>
<label for="name">Admin Response: </label> 
<strong class="label label-danger"><?php echo strtoupper($o['gc']['aff_notes']);?></strong>
<br>
<?php
}
?>
</form>
        </div>
        </div>
 </div>
     <?php } ?>    
  <!--END UPDATE CONTENT -->          
    
<!---- APPROVED LIST -->
    <?php if($o['approved_users_table']) { ?>
 <div class="row" style="padding-top: 15px;" >
        <div class="col-lg-12">
            <div class="panel panel-default">
  <!-- Default panel contents -->
  <div class="panel-heading"><?php echo $o['title']; ?> </div>
  <!-- Table -->
  <table class="table">
<table class="table">
        <thead>
          <tr>
            <th>Name</th>
            <th>Date Signup</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
            <?php
                foreach($o['show_pend'] as $sp)
                 {

             ?>
          <tr>
            <th scope="row"><?php echo ucwords($sp['user_name']); ?></th>
            <td><?php echo $sp['aff_added']; ?></td>
            <td><a href="<?php echo site_url('/affiliateadmin/view_details/' . $sp['id']);?>"><span class="fa fa-file-text"></span> View Details</a> | <a href="<?php echo site_url('/affiliateadmin/view_summary/' . $sp['id']);?>"><span class="fa fa-money"></span> View Summary</a></td>
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
<!-- END APPROVED LIST -->
<!-- SHOW APPROVED USER TABLE -->
<?php if($o['show_user_approved_table']) {  ?>
 <div class="row" style="padding-top: 15px;" >
       <div class="col-lg-offset-2 col-lg-8">
              <h4>Affiliate detail of  <?php echo ucwords($o['gc']['first_name'] . " " . $o['gc']['last_name']);?></h4>
<p>
<input type="hidden" name="baseurl" id="baseurl" value="<?php echo $o['baseurl']; ?>">
</p>
<form id="data" method="post" enctype="multipart/form-data" action="<?php echo site_url('affiliateadmin/check_request/' . $o['gc']['id']); ?>">
<div id="notification"></div>
<p>
  <label for="name">Status: </label> 
<?php echo $o['gc']['aff_status'];?> <br>
<label for="name">Paypal: </label> 
<?php echo $o['gc']['paypal_email'];?>
<br>

    <label for="name">Company: </label> 
     <?php echo $o['gc']['company'];?>

<?php if(strlen($o['gc']['phone']) < 3) { ?>
<br>
    <label for="name">Phone #: </label> 
    <span class="phone_span"><?php echo $o['gc']['mobile'];?></span>
<?php
  }
  if(strlen($o['gc']['mobile']) < 3) { 
?>
<br>
    <label for="name">Mobile #: </label> 
    <span class="phone_span"><?php echo $o['gc']['mobile'];?></span>
<?php
  }
?>
</p>
<h4>Others</h4>
<label for="name">Website: </label> 
<?php echo $o['gc']['company'];?>
<br>
<?php if($o['gc']['whatsapp']){ ?> 
<label for="name">Whatsapp: </label> 
<i class="fa fa-whatsapp fa-5" style="color:#aad450;font-size: 2em"> <small style="font-size: 14px;"><?php echo $o['gc']['whatsapp']; ?></small></i>&nbsp;&nbsp;<br>
<?php } ?>
<label for="name">Social: </label> 
<?php if($o['gc']['facebook']){ ?> 
<a href="<?php echo $o['gc']['facebook']; ?>" target="_blank"><i class="fa fa-facebook-official fa-5" style="color:#3b5998;font-size: 2em"></i></a>&nbsp;&nbsp;
<?php } ?>

<?php if($o['gc']['twitter']){ ?> 
<a href="<?php echo $o['gc']['twitter']; ?>" target="_blank"><i class="fa fa-twitter-square fa-5" style="color:#00aced;font-size: 2em"></i></a>&nbsp;&nbsp;
<?php } ?>
<?php if($o['gc']['google']){ ?> 
<a href="<?php echo $o['gc']['google']; ?>" target="_blank"><i class="fa fa-google-plus-square fa-5" style="color:#dd4b39;font-size: 2em"></i></a>&nbsp;&nbsp;
<?php } ?>
<?php if($o['gc']['linkedin']){ ?>
<?php
if(strpos($o['gc']['linkedin'], "http://") !== false) {
  $linkedin_url = $o['gc']['linkedin'];
}
else {
  $linkedin_url = "http://".$o['gc']['linkedin'];
}
?>
<a href="<?php echo $o['gc']['linkedin']; ?>" target="_blank"><i class="fa fa-linkedin-square fa-5" style="color:#007bb6;font-size: 2em"></i></a>&nbsp;&nbsp;
<?php } ?> 
<p>
  <button name="p" value="back" type="button" class="btn btn-danger" id="back" onclick="window.location.replace('<?php echo site_url('/affiliateadmin/affiliate_member');?>');">Back</button>
    <button name="p" value="back" type="button" class="btn btn-info" id="back" onclick="window.location.replace('<?php echo site_url('/affiliateadmin/view_summary/' . $o['gc']['id']);?>');">View Summary</button>
 <input type="hidden" name="user_request_id" id="user_request_id" value="<?php echo $o['gc']['id']; ?>">
</p>

</form>
        </div>
        </div>
 </div>
<?php
}
if($o['show_user_affiliate_table']){
?>
<br>
<div class="panel panel-default ">
  <!-- Default panel contents -->
  
   <div class="panel-heading"><strong>Affiliate Account Overview</strong></div>
  <!-- Table -->
  <table class="table">
    <tr>
      <td><strong>Affiliate ID:</strong> </td>
      <td><?php echo $o['gc']['is_aff']; ?></td>
    </tr>
  <tr>
      <td><strong>Affiliate Referral Link:</strong> </td>
      <td><a href="<?php echo site_url("/signup/aff/{$o['gc']['is_aff']}"); ?>" class="alert-link"><?php echo site_url("/signup/aff/{$o['gc']['is_aff']}"); ?></a> </td>
    </tr>    
    <tr>
      <td><strong>Signups:</strong> </td>
      <td><?php echo $o['affiliate_count']; ?></td>
    </tr>  
    <tr>
      <td><strong>Active Users:</strong> </td>
      <td><?php echo $o['active_count_users']; ?></td>
    </tr>     
     <tr >
      <td colspan="2"><button name="p" value="back" type="button" class="btn btn-danger" id="back" onclick="window.location.replace('<?php echo site_url('/affiliateadmin/view_details/' . $o['gc']['id']);?>');">Back</button></td>
    </tr>          
  </table>
</div>
<?php
  if($o['show_user_aff_table']){
?>

  <div class="panel panel-default ">
  <!-- Default panel contents -->
 <div class="panel-heading"><strong>Referral Signups (<small>user that paid</small>)</strong></div>
  <!-- Table -->
  <table class="table">
    <tr>
      <td><strong>Signup date</strong> </td>
       <td><strong>Name</strong></td>
      <td><strong>Comission</strong></td>
      <td><strong>Status</strong></td>
    </tr>
    <?php foreach($o['show_ud'] as $su) { ?>
    <tr>
      <td><?php echo $su['date_confirmed']; ?></td>
      <td><?php echo '$ 67.00 ' . $su['curr']; ?></td>
      <td>
        <?php if($su['p_status'] == 'ACTIVE') { ?>
          <span class="label label-success"><?php echo $su['p_status']; ?></span>
        <?php } else { ?>
          <span class="label label-danger"><?php echo $su['p_status']; ?></span>
        <?php } ?>
      </td>
    </tr> 
    <?php } ?>      
  </table>
</div>
<!-- END SHOW APPROVED USER TABLE -->
<?php
  }
}

if($o['show_chargeback_table']){
?>
<!--  SHOW CHARGEBACK USER TABLE -->
  <div class="panel panel-default ">
  <!-- Default panel contents -->
 <div class="panel-heading">Chargeback</strong></div>
  <!-- Table -->
  <table class="table">
    <tr>
      <td><strong>Date Added</strong> </td>
       <td><strong>Name</strong></td>
      <td><strong>Reason Code</strong></td>
      <td><strong>Status</strong></td>
      <td><strong>Action</strong></td>
    </tr>
    <?php foreach($o['show_chargeback'] as $su) { ?>
    <tr>
      <td><?php echo $su['date_added']; ?></td>
      <td><?php echo $su['name']; ?></td>
      <td><?php echo $su['reason_code']; ?></td>
      <td><?php echo $su['payment_status']; ?></td>
      <td> <a href="<?php echo site_url("/affiliateadmin/chargebackdetails/{$su['parent_txn_id']}"); ?>"><i class="fa fa-cogs"></i> View Running Details</a></td>
    </tr> 
    <?php } ?>      
  </table>
</div>
<!-- END SHOW CHARGEBACK USER TABLE -->
<?php
}

if($o['show_chargeback_detail_table']){
?>
<!-- SHOW CHARGEBACK USER DETAIL TABLE -->
  <div class="panel panel-default ">
  <!-- Default panel contents -->
 <div class="panel-heading">Chargeback Details for <?php echo $o['show_chargeback'][0]['name']; ?></strong></div>
  <!-- Table -->
  <table class="table">
    <tr>
      <td><strong>Transaction Date</strong> </td>
      <td><strong>Reason Code</strong></td>
      <td><strong>Status</strong></td>
      <td><strong>Action</strong></td>
    </tr>
    <?php foreach($o['show_chargeback'] as $su) { ?>
    <tr>
      <td><?php echo $su['payment_date']; ?></td>
      <td><?php echo $su['reason_code']; ?></td>
      <td><?php echo $su['payment_status']; ?></td>
      <td><a href="#" tabindex="0"  data-toggle="modal" data-target="#myModal" data-content="<?php foreach($su['all_resp'] as $ar) { echo $ar . "<br/>";} ?>">All Transaction Details</a></td>
    </tr> 
    <?php } ?>      
  </table>
</div>
<!-- END SHOW CHARGEBACK USER  DETAIL TABLE -->
<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Modal title</h4>
      </div>
      <div class="modal-body">
        <div id="show_trans_data"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
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
<script src="<?php echo assets_url('js/main.js'); ?>"></script>
<script src="<?php echo assets_url('js/intl-tel-input/intlTelInput.min.js'); ?>"></script>

<script src="<?php echo site_url('nodejs/node_modules/socket.io/node_modules/socket.io-client/socket.io.js'); ?>"></script>
<script src="<?php echo assets_url('js/chatroom.js'); ?>"></script>
<script src="<?php echo assets_url('js/moment.min.js'); ?>"></script>
<script src="<?php echo assets_url('js/MagnificPopup/dist/jquery.magnific-popup.js'); ?>"></script>

<script src="<?php echo assets_url('js/jquery.slimscroll.min.js'); ?>"></script>
<script src="<?php echo assets_url('js/slimscroll.config.js'); ?>"></script>

<?php
if($o['show_chargeback_detail_table']){
?>
<script>
  $('#myModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget); // Button that triggered the modal
    var content = button.data('content'); // Extract info from data-* attributes

    var modal = $(this)
      modal.find('.modal-title').text('Charge Back Details');
      $("#show_trans_data").html(content);
});
</script>
<?php
}
?>
<script>
  $(document).ready(function(){
    var mobile  = "<?php echo $o['gc']['mobile']; ?>";
    var userCountry = "<?php echo $o['gc']['country']; ?>";
    var countryData = $.fn.intlTelInput.getCountryData();
    $.each(countryData, function(i, country) {
      console.log(userCountry+' -- '+country.iso2);
        if ( userCountry == country.iso2 ) {
          var txt = '+'+country.dialCode+' '+mobile+' ('+country.name+')';
          $('.phone_span').each(function(){
            $(this).text(txt);
          });
          return false;
        }
    });
  });

      $(document).ready(function() 
    {
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

    /* admin panel*/
    socket.on( 'check_chatlist', function( data ) {
    show_chatlist();
  });
//socket.emit( 'check_chatlist', true );
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
</script>
<?php include('includes/footer.php');?>
