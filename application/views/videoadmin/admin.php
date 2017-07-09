<?php include('includes/header.php');?>

 <div class="row"  >
        <div class="col-lg-12" >
            <div class="panel panel-default" style="margin-bottom: 0px;">
            <!-- Default panel contents -->
            <div class="panel-heading">Admin Report</div>
            <!-- Table -->
<div class="row" style="margin-right: 0px;
margin-left: 0px;
height: 100%;">
              <div class="col-xs-6 col-md-3" style="background-color:#555;padding-left: 0px;padding-right: 0px;">
                <br>

                <div id="list_scroll" style="position: relative; overflow: hidden; width: auto; height: 400px;">
                  <div class="sidebar-inner slimscroller" style="overflow: hidden; width: auto; height: 400px;">
                    <div class="sidebar-menu" id="comments">
                        <ul class="media-list">
                             <li class="media" style="margin: 0px;">
                              <div class="media-body" style="padding-left: 5px;padding-bottom:0px;">
                                <h4 class="media-heading" ><a href="#" onclick="search_users(); return false;" style="font-size: 13px;">Search Users</a></h4>
                            </li>
                            <li class="media" style="margin: 0px;">
                              <div class="media-body" style="padding-left: 5px;padding-bottom:0px;">
                                <h4 class="media-heading" ><a href="#" onclick="check_user_stat(); return false;" style="font-size: 13px;">Check Users</a></h4>
                            </li>
                            <li class="media" style="margin: 0px;">
                              <div class="media-body" style="padding-left: 5px;padding-bottom:0px;">
                                <h4 class="media-heading" ><a href="#" onclick="check_user_name('paid'); return false;" style="font-size: 13px;">Check Paid Users [<?php echo $o['total_paid_users'];?>]</a> </h4>
                            </li>
                            <li class="media" style="margin: 0px;">
                              <div class="media-body" style="padding-left: 5px;padding-bottom:0px;">
                                <h4 class="media-heading" ><a href="#" onclick="check_user_name('expired_users'); return false;" style="font-size: 13px;">Check Ex-Paid Users [<?php echo $o['total_cancelled_users'];?>]</a></h4>
                            </li>
                            <li class="media" style="margin: 0px;">
                              <div class="media-body" style="padding-left: 5px;padding-bottom:0px;">
                                <h4 class="media-heading" ><a href="#" onclick="check_user_name('demo'); return false;" style="font-size: 13px;">Check Active Demo Users [<?php echo $o['total_demo_users'];?>]</a></h4>
                            </li>
                              <li class="media" style="margin: 0px;">
                              <div class="media-body" style="padding-left: 5px;padding-bottom:0px;">
                                <h4 class="media-heading" ><a href="#" onclick="check_user_name('demo_expired'); return false;" style="font-size: 13px;">Check Expired Demo Users [<?php echo $o['total_expired_demo'];?>]</a></h4>
                            </li>
                              </li>
                              <li class="media" style="margin: 0px;">
                              <div class="media-body" style="padding-left: 5px;padding-bottom:0px;">
                                <h4 class="media-heading" ><a href="#" onclick="check_adword_export_user('done'); return false;" style="font-size: 13px;">Users already use export</a></h4>
                            </li>                          
                            </li>
                              <li class="media" style="margin: 0px;">
                              <div class="media-body" style="padding-left: 5px;padding-bottom:0px;">
                                <h4 class="media-heading" ><a href="#" onclick="check_adword_export_user('none'); return false;" style="font-size: 13px;">Users not yet use export</a></h4>
                            </li>
                        </ul>
                    </div>
                  </div>
                </div>
   </div>

   <div class="col-xs-12 col-md-9" style="padding-left: 0px;padding-right: 0px;">
        <div id="admin_stat_container"></div>
    </div>
            </div>
            </div>
        </div>
  </div>


</div> <!-- /container -->
<div id="footer">
    <div class="container">
      <p class="footer-content">
      
    </p>
    </div>
</div>
<script src="<?php echo assets_url('js/jquery.min.js'); ?>"></script>
<script src="<?php echo assets_url('js/bootstrap.min.js'); ?>"></script>
<script src="<?php echo assets_url('js/MagnificPopup/dist/jquery.magnific-popup.js'); ?>"></script>
<script>
var baseurl = $("#baseurl").val();

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

  function check_user_stat(){
        $.ajax
        ({
        type: "POST",
        url: baseurl + "admin/check_stats",
        cache: false,
        beforeSend: function()
        {
          $("#admin_stat_container").html('<div class="alert alert-warning" role="alert">Loading, please wait..</div>');
        },
        success: function(result)
        {
          $("#admin_stat_container").html(result);          
        }        
      });   
  }

  function check_user_name(show_type){
        $.ajax
        ({
        type: "POST",
        url: baseurl + "admin/check_user_name",
        data: ({"show_type" : show_type}),
        cache: false,
        beforeSend: function()
        {
          if(show_type == 'paid'){
           $("#admin_stat_container").html('<div class="alert alert-warning" role="alert">This will take a moment, we are also checking users account on Paypal, please wait..</div>');  
          }else{
            $("#admin_stat_container").html('<div class="alert alert-warning" role="alert">Loading, please wait..</div>');
          }
        },
        success: function(result)
        {
          $("#admin_stat_container").html(result);          
        }        
      });   
  }

  function search_users(){
        $.ajax
        ({
        type: "POST",
        url: baseurl + "admin/search_users",
        //data: ({"show_type" : show_type}),
        cache: false,
        beforeSend: function()
        {
           $("#admin_stat_container").html('<div class="alert alert-warning" role="alert">Loading, please wait..</div>');  
        },
        success: function(result)
        {
          $("#admin_stat_container").html(result);          
        }        
      }); 
  }

  function check_adword_export_user(show_type){
        $.ajax
        ({
        type: "POST",
        url: baseurl + "admin/check_adword_export_user",
        data: ({"show_type" : show_type}),
        cache: false,
        beforeSend: function()
        {
           $("#admin_stat_container").html('<div class="alert alert-warning" role="alert">Loading, please wait..</div>');  
        },
        success: function(result)
        {
          $("#admin_stat_container").html(result);          
        }        
      }); 
  }
</script>
<?php include('includes/footer.php');?>
