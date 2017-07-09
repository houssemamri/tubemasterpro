<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title><?php echo $title; ?></title>
        <meta name="description" content="<?php echo $description; ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta http-equiv="Content-type" value="text/html; charset=UTF-8" />

        <!-- Extra metadata -->
        <?php echo $metadata; ?>
        <!-- / -->

        <!-- favicon.ico and apple-touch-icon.png -->
        <link rel="icon" href="<?php echo assets_url('images/tlf-favicon-1.png'); ?>" type="image/x-icon">
        <link rel="shortcut icon" href="<?php echo assets_url('images/tlf-favicon-1.png'); ?>" type="image/x-icon">

        <!-- Bootstrap core CSS -->
        <link rel="stylesheet" href="<?php echo assets_url('css/bootstrap.min.css'); ?>">
        <!-- Font Awesome CSS -->
        <link rel="stylesheet" href="<?php echo assets_url('css/font-awesome.min.css'); ?>">
        <!-- Powertour CSS -->
        <link rel="stylesheet" href="<?php echo assets_url('powertour/css/powertour.2.1.5.css'); ?>">
        <link rel="stylesheet" href="<?php echo assets_url('powertour/css/powertour-style-clean.css'); ?>">
        <link rel="stylesheet" href="<?php echo assets_url('powertour/css/powertour-connectors.css'); ?>">
        <link rel="stylesheet" href="<?php echo assets_url('powertour/css/animate.min.css'); ?>">
        <link rel="stylesheet" href="<?php echo assets_url('powertour/css/powertour-mobile.css'); ?>">
        <!-- Custom styles -->
        <link rel="stylesheet" href="<?php echo assets_url('css/main.css'); ?>">
        <link rel="stylesheet" href="<?php echo assets_url('js/MagnificPopup/dist/magnific-popup.css'); ?>">
        <?php echo $css; ?>
        <!-- / -->

        <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
            <script src="<?php echo assets_url('js/html5shiv.min.js'); ?>"></script>
            <script src="<?php echo assets_url('js/respond.min.js'); ?>"></script>
        <![endif]-->
        
		<!-- CSS adjustments for browsers with JavaScript disabled -->
		<noscript><link rel="stylesheet" href="<?php echo assets_url('css/jquery-fileupload/jquery.fileupload-noscript.css'); ?>"></noscript>
		<noscript><link rel="stylesheet" href="<?php echo assets_url('css/jquery-fileupload/jquery.fileupload-ui-noscript.css'); ?>"></noscript>
    </head>
    <body>
    	<!-- Google Tag Manager -->
		<noscript><iframe src="//www.googletagmanager.com/ns.html?id=GTM-TCN6M2"
		height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
		<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
		new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
		j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
		'//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
		})(window,document,'script','dataLayer','GTM-TCN6M2');</script>
		<!-- End Google Tag Manager -->
		
        <input type="hidden" name="baseurl" id="baseurl" value="<?php echo site_url(); ?>">
        <input type="hidden" name="usrloginName" id="usrloginName" value="<?php $user = $this->ion_auth->user()->row(); echo $user->first_name; ?>">
        <input type="hidden" name="user_login_id" id="user_login_id" value="<?php $user = $this->ion_auth->user()->row(); echo $user->id; ?>">
           
        <div class="main_loader">
            <div class="loader"></div>
            <div id="progress-main">
                <div id="progress-containter">
                    <div class="progress progress-striped active">
                        <div class="progress-bar" style="width: 1%;"></div>
                    </div>
                    <p id="cancel-notice"></p>
                    <button id="cancel-search" class="btn btn-primary" type="submit">Cancel Search</button>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modalAffiliate" aria-hidden="true" data-backdrop="static" style="display: none;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title text-center text-success">Thank you for submitting your review!</h4>
                    </div>
                    <div class="modal-body">
                        Do you want to become an AFFILIATE?
                    </div>
                    <div class="modal-footer">
                        <a href="<?php echo site_url('affiliate/signup'); ?>" class="btn btn-success" type="button">Yes</a>
                        <a href="<?php echo site_url('subscription'); ?>" class="btn btn-danger" type="button">No</a>
                    </div>
                </div>
            </div>
        </div>
        
        <?php //- Check for trial account

         //check users has 100% lifetime login
            $this->user = $this->ion_auth->user()->row();
            $is_fullaccess_promo = false;

            if($this->user->is_jvzoo == 1){
                $is_fullaccess_promo = true;
                echo $body;
            }else{
	            if($this->user->use_promocode != 0){
	                $sql = "select discount_amt,is_onetime, option_desc from promo_code where promo_code_id = '". $this->user->use_promocode."' LIMIT 1";
	                $check_promo = $this->db->query($sql);
	                    if($check_promo->num_rows() > 0){
	                        $cp = $check_promo->row_array();
	                        
	                        if($cp['discount_amt'] == 100 && $cp['is_onetime'] == 0){
	                            $is_fullaccess_promo = true;
	                             echo $body;
	                             
	                        }else{
	                            $show_is_subscribe_table = true;
	                        }
	                    }else{
	                        $show_is_subscribe_table = true;
	                    }
	            }else{
	                 $show_is_subscribe_table = true;
	            } 
	        }
            if($show_is_subscribe_table){
	            if(!$is_subscription_table){
	                $show_notice = false;
	                $show_pending= false;
	                $uri  = $this->uri->segment(1);
	
	                //- Check if first 50
	                $users = $this->ion_auth->users(array('2'))->result();
	                $has_review = 0;
	                $user_limit = 50;
	                if ( $users && count($users) > 0 ) {
	                    foreach ($users as $key => $value) {
	                        if ( $value->has_review == 1 ) $has_review++;
	                    }
	                }
	                $users_remaining = $user_limit - $has_review;
	                
	                $is_logged_in = $this->ion_auth->logged_in();
	                if ( $is_logged_in && $this->ion_auth->in_group(3) ) {
	                    $user = $this->ion_auth->user()->row();
	                    //- Check if client affiliate
	                    $sql = "select aff_id from affiliates where user_id_aff = '".$user->id."' LIMIT 1";
	
	                    $check_new_user = $this->db->query($sql);
	                    $is_client = false;
	                    if( $check_new_user->num_rows() > 0 ){
	                        $is_client = true;
	                    }
	
	                    $user_hasreview = $user->has_review;
	                    $end_date = new DateTime(date('Y/m/d H:i:s',strtotime('+7 days', $user->first_login)));
	                    //$end_date = new DateTime(date('Y/m/d H:i:s',strtotime('+5 minutes', $user->first_login)));
	                    $now_time = new DateTime(date('Y/m/d H:i:s', time()));
	                    
	                    $time_diff = $end_date->getTimestamp() - $now_time->getTimestamp();
	                    $show_notice = false;
	                    if($user_hasreview == 0){
	                        if ( $time_diff <= 0  ) {
	                            if ( $users_remaining > 0 && !$is_client ) {
	                                $show_notice = true;
	                            }
	                            else {
	                                redirect('subscription');
	                            }
	                        }
	                        else {
	                            echo $body;
	                        }
	                    }
	                    else if ( $user->aff_status == 'pending' ) {
	                        $show_pending = true;
	                    }
	                    else{
	                        echo $body;
	                    }
	                }
	                else {
	                    echo $body;
	                }
	            }
	            else{
	                 echo $body;
	            }
           }
        ?>

        <!-- / -->

        <script src="<?php echo assets_url('js/jquery.min.js'); ?>"></script>
        <script src="<?php echo assets_url('js/bootstrap.min.js'); ?>"></script>
        <?php  if(!$is_subscription_table){ ?>
        <script src="<?php echo assets_url('js/jquery.countdown.min.js'); ?>"></script>
        <?php } ?>
        <script src="<?php echo assets_url('powertour/js/powertour.2.1.5.min.js'); ?>"></script>
        <script src="<?php echo assets_url('js/main.js'); ?>"></script>
        <script src="<?php echo assets_url('js/MagnificPopup/dist/jquery.magnific-popup.js'); ?>"></script>

        <?php 
            $is_logged_in = $this->ion_auth->logged_in();
            if ( $is_logged_in ) {
                $user = $this->ion_auth->user()->row();
                if ( !$user->has_tour ) {
                    ?>
                        <!-- <script src="<?php echo assets_url('js/modules/tour.js?v='.time()); ?>"></script> -->
                    <?php
                }
                else if ( !$user->has_invites && $user->invites_counter == 2 ) {
	                
	                /*
                        <script src="<?php echo assets_url('js/modules/invites.js?v='.time()) ?>"></script>
                       */
                    
                }
            }
        ?>
        <?php
            //for subscription popup review first.
            if($open_review_table_from_sub && $user->has_review == 0){
        ?>
          <script>
          /*
                $(document).ready(function(){
                    var base_url = "<?php echo base_url(); ?>";
                    $.magnificPopup.open({
                        iframe: {
                            markup: '<div class="mfp-iframe-scaler">'+
                                        '<iframe class="mfp-iframe" frameborder="0" allowfullscreen></iframe>'+
                                    '</div>',
                        },
                        items: {
                          src: base_url+'review/main'
                        },
                        type: 'iframe',
                        closeOnBgClick: false,
                        closeBtnInside: false,
                        showCloseBtn: false
                    });

                    window.workPlease = function()
                    {
                        $.magnificPopup.close();
                    }
                });
                */
            </script>
        <?php
            }
            if ($this->ion_auth->in_group(3) ){
        ?>
            <script>
                $("#becomeAffiliate").click(function(){
                     $('#modalBecomeAffiliate').modal('show');
                });
            </script>
        <?php
            }
        ?>
        <!-- CHAT SUPPORT -->
        <!--
        <script src="<?php echo site_url('nodejs/node_modules/socket.io/node_modules/socket.io-client/socket.io.js'); ?>"></script>
        -->
        <script src="<?php echo assets_url('js/moment.min.js'); ?>"></script>
      
        <script>
        var is_chrome = navigator.userAgent.toLowerCase().indexOf('chrome') > -1;
if(!is_chrome){
  $('#modalCheckBrowser').modal('show');
}/*
 socket.on( 'check_chatlist', function( data ) {
        show_chatlist();
  });
  */
    function show_chatlist(){
        $.ajax
            ({
            type: "POST",
            url:  "/livesupport/checkchatlist",
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
                $("#conversation_count_top").html("<span class=\"fa-stack\" style=\"color: #E85344;\"><i class=\"fa fa-circle fa-stack-2x danger\"></i><i class=\"fa fa-stack-1x fa-inverse\"><b style='font-size:0.8em'>" + res[0] + "</b></i></span>");
 $("#conversation_count_admin_top").html("<span class=\"fa-stack\" style=\"color: #E85344;\"><i class=\"fa fa-circle fa-stack-2x danger\"></i><i class=\"fa fa-stack-1x fa-inverse\"><b style='font-size:0.8em'>" + res[0] + "</b></i></span>");
               
              }else{
                $("#conversation_count").text("");
                $("#conversation_count_sm").text("");
                $("#conversation_count_top").html("");
              }
             // 
            }        
          });   
    }
    setTimeout("show_chatlist()",1000);

    function check_existing_opt_camp(){
        $.ajax
            ({
            type: "POST",
            url:  "/overview/main/check_existing_opt_camp",
            cache: false,
            beforeSend: function()
            {
            },
            success: function(result)
            {
              if(result > 0){
                $("#existing_opt_count").html("<span class=\"fa-stack\" style=\"color: #a94442;\"><i class=\"fa fa-circle fa-stack-2x danger\"></i><i class=\"fa fa-stack-1x fa-inverse\"><b>"+ result +"</b></i></span>");
              }else{
                $("#conversation_count").html("");
              }
             // 
            }        
          });     
    }
    check_existing_opt_camp();
// $(function(){
    

//         socket.on( 'user_connected', function( data ) { 
//           if(data.is_connected == 1){
//             var user_login_bv = $("#usrloginName").val();
//             var user_login_id_bv = $("#user_login_id").val();

//             console.log("Connected " + data.is_connected + " as " + user_login_bv);  
//             socket.emit( 'save_online', {username: user_login_bv, user_id: user_login_id_bv } );
//           }
//         });

//     socket.on("isTyping", function(data) {  
//       $(".list_typing_anim_" + data.room_id).html("<img src='"+ baseurl +"/assets/images/isTypingRoomList.GIF'>");
//     });

//     socket.on("remove_typing_room_list", function(data) {  
//       $(".list_typing_anim_" + data.room_id).html(""); 
//     });

// });
        </script>  
        <!-- Extra javascript -->
        <?php echo $js; ?>
        <!-- / -->
        
        <?php if ( $show_notice ) : ?>
            <script>
                $(document).ready(function(){
                    var base_url = "<?php echo base_url(); ?>";
                    $.magnificPopup.open({
                        iframe: {
                            markup: '<div class="mfp-iframe-scaler">'+
                                        '<iframe class="mfp-iframe" frameborder="0" allowfullscreen></iframe>'+
                                    '</div>',
                        },
                        items: {
                          src: base_url+'review/main'
                        },
                        type: 'iframe',
                        closeOnBgClick: false,
                        closeBtnInside: false,
                        showCloseBtn: false
                    });

                    window.workPlease = function()
                    {
                        $.magnificPopup.close();
                        $('#modalAffiliate').modal('show');
                    }
                });
            </script>
        <?php endif; ?>

        <?php if ( $show_pending ) : ?>
            <div class="modal fade" id="modalPending" aria-hidden="true" data-backdrop="static" style="display: none;">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-body text-center">
                            <h2>Pending acceptance onto Affiliate Program</h2>
                            <?php $end_date = new DateTime(date('Y/m/d H:i:s',strtotime('+1 day', $user->aff_added))); ?>
                            <h2><div id="pending-started" class="alert alert-danger" data-date-end="<?php echo $end_date->getTimestamp(); ?>"></div></h2>
                        </div>
                    </div>
                </div>
            </div>
            <script>
                $(document).ready(function(){
                    $('#modalPending').modal('show');
                    var date_end = $("#pending-started").data('date-end');
                    var d = new Date(date_end*1000);
                    var date_str = ''+d.getFullYear()+'/'+(d.getMonth()+1)+'/'+d.getDate()+' '+d.getHours()+':'+d.getMinutes()+':'+d.getSeconds();
                    $('#pending-started').countdown(date_str).on('update.countdown', function(event) {
                        $(this).text(
                            event.strftime('%H:%M:%S Remaining')
                        );
                    }).on('finish.countdown', function(){
                        //location.reload();
                        $('#pending-started').html('Please wait... Approval may take long as expected.');
                    });
                });
            </script>
        <?php endif; ?>

        <?php
            if(!$is_subscription_table){ 
        ?>
        <?php if($user_hasreview == 0 && !$is_fullaccess_promo) : //&& $now_time->getTimestamp() < $end_date->getTimestamp() ) : ?>
        <script>
            $(document).ready(function(){
                var base_url = "<?php echo base_url(); ?>";
                var uri = "<?php echo $this->uri->segment(1); ?>";
                var date_end = $("#getting-started").data('date-end');
                //console.log(date_end);
                var d = new Date(date_end*1000);
                //console.log(d);
                var date_str = ''+d.getFullYear()+'/'+(d.getMonth()+1)+'/'+d.getDate()+' '+d.getHours()+':'+d.getMinutes()+':'+d.getSeconds();
                //var n = d.getTimezoneOffset() * 60;
                //console.log(date_str);
                $('#getting-started').countdown(date_str).on('update.countdown', function(event) {
                    $(this).text(
                        event.strftime('TRIAL - %D days %H:%M:%S')
                    );
                }).on('finish.countdown', function(){
                    location.reload();
                });
                
            });
        </script>
        <?php endif; ?>
        <?php } else {?>
            <script>
                     $('#getting-started').css('display','none');
            </script>
        <?php }?>
        <?php  if($user_pixel_signup) { echo $user_pixel;} ?>

        <?php if ( ! empty($ga_id)): ?><!-- Google Analytics -->
        <script>
            (function(b,o,i,l,e,r){b.GoogleAnalyticsObject=l;b[l]||(b[l]=
            function(){(b[l].q=b[l].q||[]).push(arguments)});b[l].l=+new Date;
            e=o.createElement(i);r=o.getElementsByTagName(i)[0];
            e.src='//www.google-analytics.com/analytics.js';
            r.parentNode.insertBefore(e,r)}(window,document,'script','ga'));
            ga('create','<?php echo $ga_id; ?>');ga('send','pageview');
        </script>
        <?php endif; ?><!-- / -->
        
        <?php if ($optimizer['gopt']['open_update_optimized_campaign']){ ?>
		<script>
		        var opt_id = <?php echo $optimizer['gopt']['opt_update_id']; ?>;
		</script>
		<?php } else { ?>
		<script>
		        var opt_id = false;
		</script>
		<?php } ?>
		        <?php 
            //setcookie("user_accept_ad_notice", 0, time() + (86400 * 30), "/"); // 86400 = 1 day
            if($_COOKIE['user_accept_ad_notice'] != 1){
        ?>
        <script>
        var baseurl     = $("#baseurl").val();
        //$('#adwords_notice_model').modal('show');
         $("#data-adwords-changes").addClass('btn-default');
         $("#data-adwords-changes").attr("disabled",true);
        $('#adwords_notice').change(function() {
        if($(this).is(":checked")) {
            $("#data-adwords-changes").removeClass('btn-default');
            $("#data-adwords-changes").addClass('btn-success');
            $("#data-adwords-changes").attr("disabled",false);
        }else{
            $("#data-adwords-changes").addClass('btn-default');
            $("#data-adwords-changes").removeClass('btn-success');
            $("#data-adwords-changes").attr("disabled",true);
        }

        $("#data-adwords-changes").on('click',function(){
            if($("#adwords_notice").is(":checked")) {
                $.ajax
                    ({
                    type: "POST",
                    url: baseurl + "/dashboard/adwords_notice_accepts",
                    cache: false,
                    beforeSend: function()
                    {
                    },
                    success: function(result)
                    {
                      location.reload();
                    }        
                });    
            }
        });    
    });
        </script>
        <?php
            }
        ?>
        
        <script>
        $("#campaign_builder_google_notice").on('click',function(e){
        	e.preventDefault();
            //$('#adwords_notice_builder_model').modal('show');
            $('#youtube_video_notice').modal('show');
            $('#youtube_video_notice').css('z-index', '1041');
        });
        var baseurl     = $("#baseurl").val();
        
         $("#data-adwords-changes-builder").addClass('btn-default');
         //$("#data-adwords-changes-builder").attr("disabled",true);
        $('#adwords_notice_builder').change(function() {
        if($(this).is(":checked")) {
            $("#data-adwords-changes-builder").removeClass('btn-default');
            $("#data-adwords-changes-builder").addClass('btn-success');
            $("#data-adwords-changes-builder").attr("disabled",false);
        }else{
            $("#data-adwords-changes-builder").addClass('btn-default');
            $("#data-adwords-changes-builder").removeClass('btn-success');
            $("#data-adwords-changes-builder").attr("disabled",true);
        }

        $("#data-adwords-changes-builder").on('click',function(){
            if($("#adwords_notice_builder").is(":checked")) {
                $.ajax
                    ({
                    type: "POST",
                    url: baseurl + "/dashboard/adwords_back_notice",
                    cache: false,
                    beforeSend: function()
                    {
                    },
                    success: function(result)
                    {
                        $('#adwords_notice_builder').remove();
                        $("#data-adwords-changes-builder").removeClass('btn-default');
                        $("#data-adwords-changes-builder").addClass('btn-success');
                        $("#data-adwords-changes-builder").attr('data-dismiss','modal');
                         $("#data-adwords-changes-builder").text('Ok');
                      //location.reload();
                    }        
                });    
            }
        });    
    });
        </script>
    </body>
</html>