<?php include('includes/header.php');?>

<!---SHOW PAYOUT TABLE -->
<?php 
	if($o['show_payout_table']){
?>
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
            <th>Date Transaction</th>
            <th>Total Payout</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
            <?php
                foreach($o['show_payout'] as $sp)
                 {

             ?>
          <tr scope="row">
            <td ><strong><?php echo ucwords($sp['date_transaction']); ?></strong></td>
            <td ><?php echo number_format($sp['total_transaction'],0); ?></td>
            <td><a href="<?php echo site_url('/affiliateadmin/view_payout_date/' . $sp['date_transaction']);?>"><i class="fa fa-cc-paypal fa-3"></i> View Details</a></td>
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
    <!-- END SHOW PAYOUT TABLE -->
<?php
	}
	if($o['show_payout_date_table']){
?>
<!-- START SHOW PAYOUT DATE TABLE -->
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
            <td width="40%"><strong>Payment to</strong></td>
            <td width="30%"  align='right'><strong>Transaction</strong></td>
            <td width="15%"  align='right'><strong>Amount</strong></td>
            <td width="15%"><strong>Status</strong></td>
          </tr>
        </thead>
        <tbody>
            <?php
                foreach($o['show_payout'] as $sp)
                 {s
             ?>
          <tr scope="row">
            <td ><strong><?php echo ucwords($sp['aff_name']); ?></strong></td>
            <td  align='right'><?php echo number_format($sp['total_trans'],0); ?></td>
            <td  align='right'>$<?php echo number_format($sp['total_paid'],2); ?> USD</td>
            <td><a href="<?php echo site_url('/affiliateadmin/view_payout_details/' .  $sp['receiver_id'].'/' . $sp['date_transaction']);?>"><i class="fa fa-cc-paypal fa-3"></i> View Details</a></td>
          </tr>
        <?php
            }
        ?>
                  <tr scope="row">
            <td >&nbsp;</td>
            <td align='right'><strong>Total transcation ( <?php echo number_format($o['total_transaction']); ?> ) paid: </strong></td>
            <td  align='right'><strong> $<?php echo number_format($o['total_paid'],2); ?> USD </strong></td>
            <td></td>
          </tr>
        </tbody>
      </table>
  </table>
</div>
            </div>
        </div>
    </div>
<!-- END SHOW PAYOUT DATE TABLE  -->
<?php
}
if($o['show_payout_user_transaction_det']){
?>
<!-- SHOW PAYOUT USER DETAILS  -->
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
            <td width="10%"><strong>Payment From</strong></td>
            <td width="10%" ><strong>Status</strong></td>
            <td width="10%"  align='right'><strong>Amount</strong></td>
          </tr>
        </thead>
        <tbody>
            <?php
                foreach($o['show_payout'] as $sp)
                 {
             ?>
          <tr scope="row" <?php if($sp['payout_status'] == "CHARGEDBACK") { echo 'class="alert alert-danger"'; } ?>>
            <!--
            <td ><strong><a href="#" data-toggle="modal" data-target="#myModal" data-trans-id="<?=$sp['payout_id'];?>"><?php echo ucwords($sp['payout_id']); ?></a></strong></td>
            -->
             <td><strong><?php echo $sp['aff_name']; ?></strong></td>
            <td><?php echo $sp['payout_status']; ?></td>
            <td  align='right'>$<?php echo number_format($sp['amt'],2); ?> USD</td>
            
          </tr>
        <?php
            }
        ?>
                  <tr scope="row">
            <td >&nbsp;</td>
            <td><strong>Total transcation ( <?php echo number_format($o['total_transaction']); ?> ) paid: </strong></td>
            <td  align='right'><strong> $<?php echo number_format($o['total_paid'],2); ?> USD </strong></td>
          </tr>
        </tbody>
      </table>
  </table>
</div>
            </div>
        </div>
    </div>
<!-- END SHOW PAYOUT USER DETAILS  -->
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
<script>
	$('#myModal').on('show.bs.modal', function (event) {
		var button = $(event.relatedTarget); // Button that triggered the modal
		var transaction_id = button.data('trans-id'); // Extract info from data-* attributes

		var modal = $(this)
	  	modal.find('.modal-title').text('PayPal Transaction: ' + transaction_id);
	 $.ajax
		({
			type: "POST",
			url:  "<?=site_url();?>affiliateadmin/paypaltransaction_details",
			data: ({"transaction_id" : transaction_id}),
			cache: false,
			beforeSend: function()
			{
				$("#show_trans_data").html("<div class=\"alert alert-info\" role=\"alert\"><i class=\"fa fa-cog fa-spin fa-3x fa-fw margin-bottom\"></i> Loading, please wait...</div>");
			},
			success: function(result)
			{
				transaction_id = "";
				$("#show_trans_data").html(result);

			}					
		});	
})
</script>
<?php include('includes/footer.php');?>
