<h1>Update Optimized Campaign</h1>
<?php if($data['o']['msg']){ 
  echo "<div class=\"alert alert-{$data['o']['msg_type']} animated \" role=\"alert\">{$data['o']['msg']}</div>";
}
if($data['o']['show_update_optimize_table']){
?>
<form id="overview_form_update" class="form-horizontal" rel="async" action="" autocomplete="off">
<div style="display:none;" id="notification" class="alert alert-success" role="alert"></div>
    <div class="form-group">
          <label class="col-sm-3 control-label" for="Campaign">
              *Youtube Campaign Name
          </label>
          <div class="col-sm-6" id="hook-twelve">
              <input class="form-control" id="overview_name" name="overview_name" type="text" placeholder="Optimization Name" value="<?php echo $data['o']['cd']['name'];?>">
              <input class="form-control" id="overview_name_dup" name="overview_name_dup" type="hidden" placeholder="Optimization Name" value="<?php echo $data['o']['cd']['name'];?>" readonly>
              </div>
                    <div class="col-sm-3" id="hook-twelve"><a class="btn btn-danger  pull-right" type="button" id="DeleteOverview" data-action="">Delete This Optimized Campaign</a>
          </div>
    </div>
        <div class="form-group" id="optimized-campaign-table">
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
                  if($spu['is_available'] == 1){
              ?>
              <tr class="<?php if($spu['is_available'] == '0') { echo "alert-danger"; } ?>" >
                <td style="width: 1em;"><input type="checkbox" name="checked[]" id="checked_ov" value="<?php echo $spu['id'];?>"
                <?php if($spu['is_available'] == '0') { echo "disabled"; }  if($spu['is_checked'] == '1') { echo "checked"; }?> ></td>
                <td><?php echo $spu['name']; ?> <?php if($spu['is_available'] == '0') { echo "<small><br>(already added to other optimizer)</small>"; } ?></td>
                <td><?php echo number_format($spu['total_target_list']); ?></td>
                <td><?php echo number_format($spu['total_video_ads']); ?></td>
              </tr>
<?php 
                    }
                  } 
              ?>  
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
          <div class="col-sm-12" id="hook-twelve">
          <!-- 'overview/main/overview/existing');?> -->
          <span class="pull-right">
            <a class="btn btn-default btn-lg" type="button" id="cancelUpdate" data-action="" >Back</a>&nbsp;
            <a class="btn btn-default btn-lg " type="button" id="UpdateOverview" data-action="">Update This Campaign</a>
          </span>    

              <input class="form-control" id="oid" name="oid" type="hidden" value="<?php echo $data['o']['cd']['id'];?>">
          </div>
    </div>

</form>
<?php
}
?>

<div class="modal fade text-left" id="show_backupdate" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">

    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="myModalLabel">Message</h4>
      </div>
      <div class="modal-body">
          You want to save the changes <span id="changes_name"></span>?
      </div>
      <div class="modal-footer">
       <a class="btn btn-default" type="button" id="" data-action="" href="<?php echo site_url('overview/main/overview/existing');?>">Don't Save</a>
        <button  type="button" class="btn btn-info" id="UpdateOverviewSaveExit" data-dismiss="modal">Save and Exit</button>
      </div>
    </div>
  </div>
</div>