<div class="panel panel-default ">
  <!-- Default panel contents -->
  <!-- Table -->
  <table class="table">
    <tr>
        <td><strong>Date </strong></td>
        <td><strong>Amount </strong></td>
        <td><strong>Status </strong></td>
    </tr>
    <?php $i = 1; foreach($o['show_trans'] as $su) { ?>
    <tr <?php if($su['payout_status'] == "CHARGEDBACK") { echo 'class="alert alert-danger"'; } ?>>
        <td><?php echo $su['date_transaction']; ?></td>
        <td><?php echo number_format($su['amt'],2); ?></td>
        <td><?php echo $su['payout_status']; ?></td>
    </tr> 
    <?php  $i++; } ?>      

    <tr>
        <td colspan="3"> <strong>Total: $<?php echo number_format($o['total_trans'],2); ?> USD </strong></td>
    </tr>    
  </table>
</div>