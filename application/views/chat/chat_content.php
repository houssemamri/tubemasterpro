<section class="module" id="chat-container" class="panel-body"> 
  <ul class="discussion chat" id="chat-items">
    <?php if($o['status'] == "no_chat_found") {?>
    <li><div class="alert alert-info" role="alert"><i class="fa fa-warning faa-vertical animated fa-2x" style="color:#045FB4"></i> &nbsp;YAY! Start talking!</div></li>
   <?php 
    }else
    {
      foreach($o['show_content'] as $sc)
      {
      ?>
    <li class="<?php if($sc['is_me'] == 1) { echo "self"; } else { echo "other"; }?> clearfix convo_row_<?php echo $sc['convo_id'];?>" data-id="<?php echo $sc['convo_id'];?>" data-user-id="<?php echo $sc['user_id'];?>">
      <div class="avatar <?php if($sc['is_me'] == 1) { echo "pull-right"; } else { echo "pull-left"; }?>" >
       <?php if($o['is_admin'] == '1') { ?>
            <a href="#" onclick="show_user_info('<?php echo $sc['convo_id'];?>','<?php echo $sc['user_id'];?>'); return false;" class="tooltip_user_<?php echo $sc['convo_id'];?>">
                <img src="<?php echo assets_url('avatar/thumbnail/' . $sc['user_img']);?>" alt="<?php echo $sc['user_name'];?>" class="img-circle" />
            </a>
        <?php } else { ?>
            <img src="<?php echo assets_url('avatar/thumbnail/' . $sc['user_img']);?>" alt="<?php echo $sc['user_name'];?>" class="img-circle" />
        <?php } ?>
       
      </div>
      	<?php
			if ( $this->ion_auth->in_group(3,$sc['user_id']) ) {
			    $group_name = ' - <font style="color:#843534">Trial</font>';
			}
			else if ( $this->ion_auth->in_group(2,$sc['user_id']) ) {
			    $group_name = ' - <font style="color:#449d44">Paid</font>';
			}
			else if ( $this->ion_auth->in_group(1,$sc['user_id']) ) {
			    $group_name = '';
			}
		?>
        <div class="<?php if($sc['is_me'] == 1) { echo "bubble2"; } else { echo "bubble"; }?> messages"> 
          <span><span class="usernotif_<?php echo $sc['user_id'];?> usernotification" style="margin-right:-8px;">
           <span class="glyphicon glyphicon-certificate" style="color:grey;font-size: 0.7em;"></span>
                    </span>&nbsp;<span class="chat_name"><?php echo $sc['user_name'] . $group_name;?></a> 
                    <?php if($sc['is_me'] == 1) { ?>
                    <small  class="sending_seen_icon sending_<?php echo $sc['convo_id'];?> msg_is_read">
                        <?php if($sc['reader'] != "") { ?>
                        <span class="glyphicon glyphicon-ok" style="color:green">
                        <?php } else { ?>
                         <span class="glyphicon glyphicon-ok" style="color:#FF7E00">
                        <?php } ?>  
                    </small>
                    <?php 
                  }
                  if($o['is_admin'] == 1){ 
                  ?> 
                  <smal class="delete_convo pull-right" >&nbsp;&nbsp;<a href="#" onclick="delete_convo('<?php echo $sc['convo_id'];?>'); return false;">delete</a></small>
                  <?php 
                    }
                  ?>
                  </span> <br>
          <p class="personSay"><?php echo stripslashes($sc['text']); ?></p> 
          <small><i class="timesent" data-time="<?php echo $sc['date_sent'];?>"><?php echo $sc['time_human'];?></i></small><br>
          <small class="seen_by_<?php echo $sc['convo_id'];?> seen_by"><?php if($sc['reader'] != "" && $sc['is_me'] == 1) { ?><a href="#" onclick="popup_seen('<?php echo $sc['convo_id'];?>'); return false;"><?php echo $sc['text_seen']; ?></a><?php } ?></small>
        </div>
    </li>
    <?php
      }
    }
    ?>
  </ul>
  <!--
      <div class="col-md-12" id="notif" style="position:absolute;bottom:3em;display:none;">
        <div class="bubble messages animated fadeIn">
          <p class="personSay"><span id="istyping"></span> <span id="istyping_text"></span></p> 
        </div>
    </div>  
    -->
</section>
<script>
$('.slimscrollerChat').slimscroll({
    height: 'auto',
    size: '3px',
    railOpacity: 0.3,
    wheelStep: 5,
    start: 'bottom'
  });

</script>