<?php if($o['show_list_stat_report']) { ?>
<div class="row" style="padding-top: 15px;" >
       <div class="col-lg-offset-2 col-lg-8">
<div class="panel panel-default ">
  <!-- Default panel contents -->
  
   <div class="panel-heading"><strong>Check Users</strong></div>
  <!-- Table -->
  <table class="table">
    <tr>
      <td><strong>Total Signup Users:</strong> </td>
      <td><?php echo number_format($o['check_all_users_count'],0); ?></a></td>
    </tr>
  <tr>
      <td><strong>Total Paid Users:</strong> </td>
      <td><?php echo number_format($o['total_paid_users'],0); ?></td>
    </tr>    
    <tr>
      <td><strong>Conversion Rate:</strong> </td>
      <td><?php echo intval($o['total_conv_rate']) . "%"; ?></td>
    </tr>             
  </table>
</div>

<div class="panel panel-default ">
  <!-- Default panel contents -->
  
   <div class="panel-heading"><strong>Signup as of <?php echo $o['date_today']; ?></strong></div>
  <!-- Table -->
  <table class="table">
    <tr>
      <td><strong>Signup Today:</strong> </td>
      <td><?php echo number_format($o['signup_today_count'],0); ?></td>
    </tr>
  <tr>
      <td><strong>Signup Yesterday:</strong> </td>
      <td><?php echo number_format($o['signup_yesterday_count'],0); ?></td>
    </tr>                
  </table>
</div>

</div>
</div>
<?php 
} 
if($o['show_paid_users_name']){
?>
<div class="row" style="padding-top: 15px;" >
       <div class="col-lg-offset-2 col-lg-8">
<div class="panel panel-default ">
  <!-- Default panel contents -->
  
   <div class="panel-heading"><strong><?php echo $o['user_text']; ?></strong></div>
	  <!-- Table -->
	  <table class="table">
	    <tr>
	      <td><strong>Name</strong> </td>
	      <td><strong>Email</strong></td>
	    </tr>
	    <?php 
	    	$i = 0;
	    	foreach($o['show_paid_users'] as $spu) { 
	    		$i++;
	    ?>
	    <tr>
	      <td><?php echo $i . ". " .ucfirst($spu['first_name']) . " " . ucfirst($spu['last_name']); ?></td>
	      <td><?php echo $spu['email']; ?></td>
	    </tr>
	    <?php } ?>

	  </table>
	</div>
</div>
</div>
<?php
}
?>
