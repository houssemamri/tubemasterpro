    <?php if($o['msg']){ ?>
    <div class="row" style="padding-top: 15px;" >
        <div class="col-lg-12">
            <div class="alert alert-<?php echo $o['msg_type']; ?> alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <p><?php echo $o['msg']; ?></p>
            </div>
        </div>
    </div>

    <!---SHOW PAYOUT TABLE -->
<?php 
}
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
            <td><a href="<?php echo site_url('/affiliate/view_payout_date/' . $sp['date_transaction']);?>"><i class="fa fa-cc-paypal fa-3"></i> View Details</a></td>
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
if($o['show_payout_user_transaction_det']){
?>
<input type="hidden" value="<?php echo site_url(); ?>" id="baseurl" name="baseurl">
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
            <td width="10%"><strong>Affiliate Name</strong></td>
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
             <td><strong> <?php echo ucfirst($sp['aff_name']); ?></strong></td>
            <td><?php echo $sp['payout_status']; ?></td>
            <td  align='right'>$<?php echo number_format($sp['amt'],2); ?> USD</td>
            
          </tr>
        <?php
            }
        ?>
                  <tr scope="row">
            <td colspan="2"><strong>Total transcation(s): <?php echo number_format($o['total_transaction']);?></strong></td>
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

