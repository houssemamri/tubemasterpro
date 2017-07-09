<style>
	.white-tubeuniversity-popup {
  position: relative;
  background: #FFF;
  padding: 20px;
  width: auto;
  max-width: 500px;
  margin: 20px auto;
	}
  .blur_othervideo{
  	    -webkit-filter: blur(10px);
   		 -webkit-transition: -webkit-filter .50s;
  }

  .unlock_word{
  	-webkit-filter: blur(0px);
  	z-index:100;
  }

  .unlock_text{
    margin-top: -7em;
    margin-left: 4em;
    font-size: 1.5em;
    color: #27ae60;
    font-weight: bold;
    -webkit-filter: blur(0px);
    position: absolute;
  }

  .video_text {
    margin-top: -3.5em;
    width: 90%;
  }
</style>
<?php 
if ($this->ion_auth->in_group(3) ){
	$is_trial_user = true;
}else{
	$is_paid_user = true;
}
?>
<h1>
    Tube University
</h1>
<div class="alert alert-info" role="alert">
  coming next week
</div>
<div class="row">
  <div class="col-xs-6 col-md-4"><a href="#" onclick="video_popup('kjWrDcwJKPY','active'); return false;">
  <img src="/assets/images/university/Lesson1.png"	></a>
<p class="video_text"><a href="#" onclick="video_popup('kjWrDcwJKPY','active'); return false;">Lesson 1:  TubeMaster Training</a> <br> Our essential training on getting the best from TubeMasterPro</p>
  </div>
  <div class="col-xs-6 col-md-4"><a href="#" onclick="video_popup('HA36X2EI2g4','active'); return false;">
  <img src="/assets/images/university/Lesson2.png"></a>
  <p class="video_text"><a href="#" onclick="video_popup('HA36X2EI2g4','active'); return false;">Lesson 2 Video SEO - TubeUniversity</a> <br>
Big One! How to rank your YouTube™ videos on Page 1</p>
  </div>
  <!--
<div class="col-xs-6 col-md-4"><a href="#" onclick="VideonotAvailable(); return false;">
  <img src="/assets/images/university/03.png"></a>
<p class="video_text"><a href="#">Lesson 3:  YouTube™ Video Ad Creation </a><br>
Creating ads on YouTube™</p>
  </div>
-->
</div>
<div class="row ">
  <div class="col-xs-6 col-md-4">
  <a href="#" onclick="video_popup('<?php if($is_paid_user) { echo "0Ogqgx9PD5k"; } ?>','<?php if($is_paid_user) { echo "active"; }else{ echo "lock";} ?>'); return false;">
  <img src="/assets/images/university/Lesson4.png" class="<?php if($is_trial_user) { echo "blur_othervideo"; } ?>">
  
    <?php if($is_trial_user) { ?>
      <div class="unlock_text">UNLOCK</div>
    <?php } ?>
    </a>
    <p class="video_text"><a href="#" onclick="video_popup('<?php if($is_paid_user) { echo "0Ogqgx9PD5k"; } ?>','<?php if($is_paid_user) { echo "active"; }else{ echo "lock";} ?>'); return false;">
    Lesson 4 - Audience Research - TubeMasterPro TubeUniversity</p>

  </div>
  <!--
<div class="col-xs-6 col-md-4">
  <a href="#" onclick="VideonotAvailable(); return false;">
  <img src="/assets/images/university/05.png" class="<?php if($is_trial_user) { echo "blur_othervideo"; } ?>">
    <?php if($is_trial_user) { ?>
      <div class="unlock_text">UNLOCK</div>
    <?php } ?>
    </a>
    <p class="video_text"><a href="#" onclick="VideonotAvailable(); return false;">
    Lesson 5:  Adwords Remarketing</a> <br> Do NOT release your ad without having this done and dusted!</p>
  </div>
  <div class="col-xs-6 col-md-4">
  <a href="#" onclick="VideonotAvailable(); return false;">
  <img src="/assets/images/university/06.png" class="<?php if($is_trial_user) { echo "blur_othervideo"; } ?>">
    <?php if($is_trial_user) { ?>
      <div class="unlock_text">UNLOCK</div>
    <?php } ?> 
    </a>
    <p class="video_text"><a href="#" onclick="VideonotAvailable(); return false;">
    Lesson 6:  Advanced YouTube™ Ad Creation</a> <br> Ready to move to next level? Thought so - watch this then!</p>
</div>
-->
</div>

<!--
<div class="row ">
  <div class="col-xs-6 col-md-4">
  <a href="#" onclick="VideonotAvailable(); return false;">
  <img src="/assets/images/university/03.png" class="<?php if($is_trial_user) { echo "blur_othervideo"; } ?>">
  
    <?php if($is_trial_user) { ?>
      <div class="unlock_text">UNLOCK</div>
    <?php } ?>
    </a>
    <p class="video_text">
      <a href="#" onclick="VideonotAvailable(); return false;">
      Lesson 7: Adding Secret Content</a>
    </p>
  </div>
  <div class="col-xs-6 col-md-4">
  <a href="#" onclick="VideonotAvailable(); return false;">
  <img src="/assets/images/university/05.png" class="<?php if($is_trial_user) { echo "blur_othervideo"; } ?>">
    <?php if($is_trial_user) { ?>
      <div class="unlock_text">UNLOCK</div>
    <?php } ?>
    </a>
    <p class="video_text"><a href="#" onclick="VideonotAvailable(); return false;">
    Lesson 8: Adding Secret Content</a></p>
  </div>
  <div class="col-xs-6 col-md-4">
  <a href="#" onclick="VideonotAvailable(); return false;">
  <img src="/assets/images/university/06.png" class="<?php if($is_trial_user) { echo "blur_othervideo"; } ?>">
    <?php if($is_trial_user) { ?>
      <div class="unlock_text">UNLOCK</div>
    <?php } ?> 
</a>
<p class="video_text"><a href="#" onclick="VideonotAvailable(); return false;">
Lesson 9: Adding Secret Content</a></p>
     </div>
</div>
-->

<script>
function video_popup(link,type){
	if(type == 'active'){

                    $.magnificPopup.open({
                        iframe: {
                            markup: '<div class="mfp-iframe-scaler">'+
                                        '<iframe class="mfp-iframe" frameborder="0" allowfullscreen></iframe>'+
                                    '</div>',
                        },
                        items: {
                          src: 'https://www.youtube.com/watch?v=' + link
                        },
                        type: 'iframe',
                        closeOnBgClick: true,
                        closeBtnInside: true,
                        showCloseBtn: true
                    });
            
     }else{
     	  $('#UnlockTubeUniversity').modal('show');
     }
}

function VideonotAvailable(){
   $('#UnavailableVideo').modal('show');
}
</script>

<div class="modal fade" id="UnlockTubeUniversity" aria-hidden="true" data-backdrop="static" style="display: none;">
      <div class="modal-dialog">
          <div class="modal-content">
              <div class="modal-header">
                  <h4 class="modal-title text-center text-success">Unlock Video</h4>
              </div>
              <div class="modal-body">
                  To unlock the full TubeUniversity course, please click HERE to buy a full TubeMasterPro license
              </div>
              <div class="modal-footer">
                  <a href="#" class="btn btn-danger" type="button" data-dismiss="modal">Cancel</a>
                  <a href="<?php echo site_url('subscription'); ?>" class="btn btn-success" type="button" data-dismiss="modal">UPGRADE NOW</a>
                  
              </div>
          </div>
      </div>
  </div>

<div class="modal fade" id="UnavailableVideo" aria-hidden="true" data-backdrop="static" style="display: none;">
      <div class="modal-dialog">
          <div class="modal-content">
              <div class="modal-header">
                  <h4 class="modal-title text-center text-success">VIDEO BEING REMIXED</h4>
              </div>
              <div class="modal-body">
              <!--
                 Yeah, the new feature we’ve already hidden in this version of TubeMasterPro is going on it’s last testing phase. Once Nate has OK’d it, we’ll unhide it and then your jaws will be on the floor with how LITTLE you need to do on your YouTube Campaigns.
              -->
              Campaign Optimizer Coming Soon - Video About To Be Updated
              </div>
              <div class="modal-footer">
                  <a href="#" class="btn btn-danger" type="button" data-dismiss="modal">Close</a>
                  
              </div>
          </div>
      </div>
  </div>  