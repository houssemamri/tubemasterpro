<h4>Affiliate Dashboard</h4>
<input type="hidden" value="<?php echo site_url(); ?>" id="baseurl" name="baseurl">
<?php

if($o['pending_account_table']){
?>
    <div class="row">
        <div class="col-lg-12">
			<div class="alert alert-<?php echo $o['msg_type']; ?>" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			  <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
			  <span class="sr-only">Error:</span>
			  <?php echo $o['msg']; ?>
			</div>
		</div>
	</div>
<?php
}
if($o['session_msg_table']){
?>
    <div class="row">
        <div class="col-lg-12">
      <div class="alert alert-<?php echo $o['msg_type_sess']; ?>" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
        <span class="sr-only">Error:</span>
        <?php echo $o['msg_sess']; ?>
      </div>
    </div>
  </div>
<?php
}
if($o['show_affiliate_table']){
?>

<div class="panel panel-default ">
  <!-- Default panel contents -->
  
   <div class="panel-heading"><strong>Affiliate Account Overview</strong></div>
  <!-- Table -->
  <table class="table">
    <tr>
      <td><strong>paypal email:</strong> </td>
      <td><?php echo $u->paypal_email; ?></td>
    </tr>    
        <tr>
      <td><strong>Affiliate ID:</strong> </td>
      <td><?php echo $u->is_aff; ?></td>
    </tr>

 	<tr>
    	<td><strong>Affiliate Referral Link:</strong> </td>
    	<td><a href="<?php echo site_url("/signup/aff/{$o['is_aff']}"); ?>" class="alert-link"><?php echo site_url("/signup/aff/{$o['is_aff']}"); ?></a> </td>
    </tr>    
    <tr>
      <td><strong>Signups:</strong> </td>
      <td><?php echo $o['affiliate_count']; if($o['affiliate_count'] > 0) { ?> 
        <a href="#" data-toggle="modal" data-target="#ModalViewActive" data-trans-id="<?=$u->id;?>" data-search-type="Signups">View names</a>
        <?php } ?>
      </td>
    </tr>  
    <tr>
      <td><strong>Active:</strong> </td>
      <td><?php echo $o['active_count_users']; if($o['active_count_users'] > 0) { ?> 
        <a href="#" data-toggle="modal" data-target="#ModalViewActive" data-trans-id="<?=$u->id;?>" data-search-type="Active">View names  </a>
     <?php
      }
      ?>
      </td>
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
        <td><strong>Name </strong></td>
    	<td><strong>Total Comission</strong></td>
    	<td><strong>Status</strong></td>
    </tr>
    <?php foreach($o['show_ud'] as $su) { ?>
    <tr>
      <td><?php echo $su['date_confirmed']; ?></td>
        <td><a href ="#" data-toggle="modal" data-target="#ModalViewUserTransaction" data-affuser-id="<?=$su['user_id_aff'];?>" data-affuser-name="<?=$su['user_aff'];?>"><?php echo $su['user_aff']; ?></a></td>
      <td><?php echo '$' .number_format($su['total_payout_user'],2) . ' ' . $su['curr']; ?></td>
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
<?php
}
}
?>

<!-- Modal -->
<div class="modal fade" id="ModalViewActive" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
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

<div class="modal fade" id="ModalViewUserTransaction" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Modal title</h4>
      </div>
      <div class="modal-body">
        <div id="show_user_trans_data"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

