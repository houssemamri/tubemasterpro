<style type="text/css">


  #error-msg {
    text-align: center;
    margin-top: -42px;
    height: 40px;
    width: 100%;
    position: absolute;
    top: 0;
    left: 0;
  }

  #error-msg span {
    font-size: 1.6em;
    color: rgb(217, 83, 79);
  }

  span.disabled-detector {
    position: absolute;
    top: 0;
    bottom: 0;
    left: 0;
    right: 0;
    opacity: 0;
  }
</style>
<p id="error-msg" class="bg-danger"><span></span> <input id="error-focus" type="text"  readonly value="" style="position:absolute;"/></p>

<h1><?php echo $data['o']['header_title'];?></h1>
<?php

  if($data['o']['msg']){ 
  echo "<div class=\"alert alert-{$data['o']['msg_type']} animated \" role=\"alert\">{$data['o']['msg']}</div>";
}

if($data['o']['add_campaign_overview_table']){

?>
<form id="overview_form_create" class="form-horizontal" rel="async" action="" autocomplete="off">
<div style="display:none;" id="notification" class="alert alert-success" role="alert"></div>
    <div class="form-group">
          <label class="col-sm-3 control-label" for="Campaign">
              *Optimized Campaign Name 
          </label>
          <div class="col-sm-7" id="hook-twelve">
              <input class="form-control" id="overview_name" name="overview_name" type="text" placeholder="Enter Your New Optimised Campaign Name Here">
          </div>
    </div>

    <div class="form-group" style="display:none;" id="optimized-campaign-table">
          <div class="col-sm-12 container-fluid" id="hook-twelve">
              <div class="panel panel-default ">
				  <!-- Default panel contents -->
				    <!-- Table -->
				    <div class="container-fluid" style="padding-top: 10px;">
  <table id="create-optimized-campaign-list" class="table table-striped " cellspacing="0" width="100%">
            <thead>
              <tr>
                  <th style="width: 10px;">&nbsp;</th>
                  <th>Available Campaign</th>
                  <th>Target Groups</th>
                  <th>Placements</th>
              </tr>
              </thead>
  
              <tbody>
              <?php 
                $i = 0;
                foreach($data['o']['show_camp'] as $spu) { 
                  $i++;
              ?>
              <tr class="<?php if($spu['is_available'] == '0') { echo "alert-danger"; } ?>" >
                <td style="width: 1em;"><input type="checkbox" name="checked[]" id="checked_ov" value="<?php echo $spu['id'];?>"
                <?php if($spu['is_available'] == '0') { echo "disabled"; } ?> <?php if($data['o']['is_forward_cid'] == $spu['id']){ echo "checked"; }?>></td>
                <td><?php echo $spu['name']; ?></td>
                 <td><?php echo number_format($spu['total_target_list']); ?></td>
                  <td><?php echo number_format($spu['total_video_ads']); ?></td>
              </tr>
              <?php } ?>   
              </tbody> 
      </table>
</div>

				  </div>
          </div>

    </div>    
        <div class="form-group">
          <label class="col-sm-3 control-label" for="Campaign">
              &nbsp;
          </label>
          <div class="col-sm-7" id="hook-twelve">
              <a class="btn btn-default btn-lg" type="button" id="SaveOverview" data-action="">Create Optimized Campaign</a>
          </div>
    </div>

</form>

  <?php if($data['o']['auto_popup_selected_optimizer']){
  ?>
  <div class="modal fade" id="campaign-to-start" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
  
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="myModalLabel">HOLD ON!</h4>
      </div>
      <div class="modal-body" style="text-align:center;">
          Ok we're going to create an optimized campaign from the Adwords file you just uploaded
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Ok</button>
      </div>
    </div>
  </div>
</div>
  <script>
      setTimeout("open_popup_from_uploaded_csv()",1000);

  </script>
  <?php
  }
  ?>
<?php
}
if($data['o']['show_existing_optimizer']){


?>
      <!---SHOW PAYOUT TABLE -->
 <div class="row" style="padding-top: 15px;" >
        <div class="col-lg-12">
            <div class="panel panel-default">
  <!-- Default panel contents -->

  <!-- Table -->
            <div class="container-fluid" style="padding-top: 10px;">
  <table id="create-optimized-campaign-list" class="table table-striped " cellspacing="0" width="100%">
        <thead>
          <tr>
            <th>Campaign Name</th>
            <th>Total Campaign</th>
            <th>Date Created</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
            <?php
                foreach($data['o']['show_opt'] as $sp)
                 {

             ?>
          <tr scope="row">
            <td ><strong><?php echo ucwords($sp['name']); ?></strong></td>
            <td ><?php echo number_format($sp['total_campaign'],0); ?></td>
            <td ><?php echo $sp['date_added']; ?></td>
            <td><a href="#" onclick="create_the_optimized_campaign('<?php echo $sp['id'];?>');return false;" class="btn btn-success">  CREATE THE OPTIMIZED CAMPAIGN!</a>&nbsp;
<?php /*
            <a href="<?php echo site_url('/overview/main/view_opt_detail/' . $sp['id']);?>" class="btn btn-success">View Details</a>&nbsp;
<a href="<?php echo site_url('/overview/main/update_opt_detail/' . $sp['id']);?>" class="btn btn-info">Update Views</a>
-->
*/
?>
            </td>
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
 <div class="modal fade text-left" id="show_unable_generate_campaign" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">   
 <div id="show_camp_data"></div>
    </div>
  <div class="modal fade" id="campaign-upload-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="z-index:99999">
  <div class="modal-dialog">
  
    <div class="modal-content">
      <div class="modal-header text-center">
        <h4 class="modal-title" id="myModalLabel">Notice</h4>
      </div>
      <div class="modal-body" style="text-align:center;">
        <div class="alert alert-warning"><p><span id="upload-notice">Uploading CSV please wait...</span></p></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Ok</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="campaign-info-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
  
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="myModalLabel">Campaign Info</h4>
      </div>
      <div class="modal-body" style="text-align:center;">
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Ok</button>
      </div>
    </div>
  </div>
</div>

<?php 
  if($data['o']['auto_popup_selected_optimizer']){
?>
  <script>
      setTimeout("open_popup_optimized_campaign(<?php echo $data['o']['auto_popup_selected_id'];?>)",1000);

  </script>
<?php
  }
?>

<?php
}
?>