        <link rel="stylesheet" href="<?php echo assets_url('css/bootstrap.min.css'); ?>">
        <!-- Font Awesome CSS -->
        <link rel="stylesheet" href="<?php echo assets_url('css/font-awesome.min.css'); ?>">
        <!-- Custom styles -->
        <link rel="stylesheet" href="<?php echo assets_url('css/main.css'); ?>">
        <link rel="stylesheet" href="<?php echo assets_url('css/chat.css'); ?>">
<div style="background-color: #FFFFFF;">
  <div class="well">
  <h4>Message Seen</h4>
  <div class="notifications" role="alert"></div>
   <ul class="discussion chat" id="chat-items">
      <li class="self clearfix">
      <div class="avatar">
        <img src="<?php echo assets_url('avatar/thumbnail/' . $o['ss']['user_img']);?>" alt="<?php echo $o['ss']['user_name'];?>" class="img-circle" />
      </div>
      <div class="bubble2 messages"> 
      <span><span class="chat_name"><?php echo $o['ss']['user_name'];?></a></span> <br>
      <p class="personSay"><?php echo $o['ss']['text'];?></p> 
       <small><i class="timesent" data-time="{$o.ss.time_human}"><?php echo $o['ss']['time_human'];?></i></small>
    </div>
    </li>
    </ul>
  <label>Read by</label>
  <table class="table table-condensed">
  <tr class="info">
      <th>Name</th>
      <th>Date read</th>
    <tr>
    <?php
      foreach($o['show_reader'] as $sr){
    ?>
  <tr class="success">
      <th><?php echo $sr['seen_name'];?></th>
      <th><?php echo $sr['date_seen'];?></th>
    <tr>    
   <?php
    }
    ?>
  </table>
</div>
</div>
