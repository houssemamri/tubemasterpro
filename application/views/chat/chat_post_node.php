<li  class="<?php if($o['cc']['is_me'] == 1) { echo "self"; } else { echo "other"; }?> clearfix convo_row_<?php echo $o['cc']['convo_id'];?>" data-id="<?php echo $o['cc']['convo_id'];?>" data-user-id="<?php echo $o['cc']['user_id'];?>">
  <div class="avatar <?php if($o['cc']['is_me'] == 1) { echo "pull-right"; } else { echo "pull-left"; }?>" >
    <?php if($o['is_admin'] == '1') { ?>
            <a href="#" onclick="show_user_info('<?php echo $o['cc']['convo_id'];?>','<?php echo $o['cc']['user_id'];?>'); return false;" class="tooltip_user_<?php echo $o['cc']['convo_id'];?>">
                <img src="<?php echo assets_url('avatar/thumbnail/' . $o['cc']['user_img']);?>" alt="<?php echo $o['cc']['user_name'];?>" class="img-circle" />
            </a>
        <?php } else { ?>
            <img src="<?php echo assets_url('avatar/thumbnail/' . $o['cc']['user_img']);?>" alt="<?php echo $o['cc']['user_name'];?>" class="img-circle" />
        <?php } ?>
        
          
  </div>
    <div class="<?php if($o['cc']['is_me'] == 1) { echo "bubble2"; } else { echo "bubble"; }?> messages"> 
      <span><span class="usernotif_<?php echo $o['cc']['user_id'];?> usernotification" style="margin-right:-8px;">
       <span class="glyphicon glyphicon-certificate" style="color:grey;font-size: 0.7em;"></span>
                </span>&nbsp;<span class="chat_name"><?php echo $o['cc']['user_name'];?></a> 
                <?php if($o['cc']['is_me'] == 1) { ?>
                <small  class="sending_seen_icon sending_<?php echo $o['cc']['convo_id'];?> msg_is_read">  
                </small>
                <?php 
            	} 
				if($o['is_admin'] == "1"){ 
            	?>
                <small class="delete_convo pull-right">&nbsp;&nbsp;<a href="#" onclick="delete_convo('<?php echo $o['cc']['convo_id'];?>'); return false;">delete</a></small>
				<?php } ?>
                </span> <br>
      			<p class="personSay"><?php echo stripslashes($o['cc']['text']);?></p> 
      			<small><i class="timesent" data-time="<?php echo $o['cc']['moment_date_sent'];?>"></i></small><br><small class="seen_by_<?php echo $o['cc']['convo_id'];?> seen_by"></small>
    </div>
</li>    
