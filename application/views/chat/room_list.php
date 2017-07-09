<!-- Tab panes -->
    <!-- Pane comments -->
    <div class="sidebar-menu" id="comments" style="font-size:10px; padding-top: 10px;">
    <!-- Begin scroll wrappper -->
      <ul class="media-list">
        <?php
          foreach($o['show_result'] as $sr)
          {
             //{cycle values='room_list_bg01,room_list_bg02' assign=liCSS} 
        ?>
        
          <li class="media room_list_<?php echo $sr['room_id'];?>" style="">  
            <?php if($o['is_admin'] == 1) { ?>
            <small class="pull-right"><a href="#" onclick="delete_thread('<?php echo $sr['room_id'];?>'); return false;">Delete</a></small>
            <?php } ?>
          <a class="pull-left" href="#" onclick="show_chat_content('<?php echo $sr['room_id'];?>'); return false;" style="padding-left: 10px;
padding-top: 10px;">
            <?php if($sr['user_img'] == '0') {?>
  
                  <span class="fa-stack fa-2x ">  
                  <i class="fa fa-circle fa-stack-2x"></i>
                  <i class="fa fa-group fa-stack-1x fa-inverse"></i>
                  <?php if($sr['notification'] > 0) {?>
                    <i class="badge pull-left chatlist_<?php echo $sr['chat_id'];?> chat_room_notif_<?php echo $sr['room_id'];?>" style="position:relative;background-color:#d9534f;top: -6px;
right: 10px;"><?php echo number_format($sr['notification'],0); ?> </i>
                 <?php } ?>
                  </span>
              <?php
                }else{
                ?>
              <span class="fa-stack fa-2x ">  
                <img src="<?php echo site_url() ."assets/avatar/thumbnail/" . $sr['user_img'];?>" alt="<?php echo $sr['room_name'];?>" class="fa-stack-1x img-circle" style="width:52px;height:52px;"  />   
                 <?php if($sr['notification'] > 0) {?>
             
                <i class="badge pull-left chatlist_<?php echo $sr['chat_id'];?> chat_room_notif_<?php echo $sr['room_id'];?>" style="position:relative;background-color:#d9534f;top: -6px;
right: 10px;"><?php echo number_format($sr['notification'],0); ?></i>
                <?php } ?>
                </span> 
              <?php
                }
              ?>
          </a>
          <div class="media-body" style="padding-left: 12px;">
            <h4 class="media-heading" style="font-size: 12px;"><a href="#" onclick="show_chat_content('<?php echo $sr['room_id'];?>'); return false;"><span style="color: #fff;"><?php echo $sr['room_name'];?></span></a>  <small class="list_typing_anim_<?php echo $sr['room_id'];?>"></small> </h4>
            <p style="font-size:10px;color: #fff;"><?php echo $sr['latest_sent'];?></p>
            <p style="color: #fff;"><?php echo substr(stripslashes($sr['latest_text']), 0,30);?></p>
            
          </div>
          
          </li>
         <?php 
          }
          ?>
</ul>
      </ul>