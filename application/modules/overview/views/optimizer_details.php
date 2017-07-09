<h3>Optimised Campaign Details</h3>
<?php if($data['o']['msg']){ 
  echo "<div class=\"alert alert-{$data['o']['msg_type']} animated \" role=\"alert\">{$data['o']['msg']}</div>";
}

if($data['o']['show_details_optimize_table']){
?>

<div class="panel panel-default">
  <div class="panel-heading"><?php echo $data['o']['cd']['name']; ?></div>
<div class="panel-body">
    <p><h4>Campaigns include: <?php echo $data['o']['show_list']['included_campaign']; ?></h4>
    <h5>Summary: <span class="label label-success">Total Videos: <?php echo $data['o']['show_list']['total_video_list']; ?></span> &nbsp;
    <span class="label label-warning">Unique Videos: <?php echo $data['o']['show_list']['total_unique_videos']; ?></span>&nbsp;
    <span class="label label-danger">Duplicate Videos: <?php echo $data['o']['show_list']['total_duplicate_videos']; ?></span></h5>
    </p>
    <?php
      if($data['o']['no_accurate_data_text']){
        echo "<div class=\"alert alert-warning\" role=\"alert\">No Accurate data as of the moment... try again later</div>";
      }
    ?>
    <div id="notification"></div>
  </div>

<?php
if($data['o']['show_data_table']){
?>
<div class="panel-body">
    <p>Showing 10% of best performing youtube url as of <strong><?php echo $data['o']['date_today'];?></strong></p>
    </div>
<table class="table">
          <thead>
          <tr>
           <th>&nbsp;</th>
            <th>Youtube Title</th>
            <th>Total Ad Views</th>
          </tr>
        </thead>

  <?php 
    foreach($data['o']['show_data'] as $st){
  ?>
        <tbody>
          <tr>
            <td scope="row"><a href="#" onclick="popup_video('<?php echo $st['youtube_id']?>'); return false;" ><img src="<?php echo $st['thumbnail']; ?>" width="120" height="90"></a></td>
            <td ><?php echo $st['video_title']; ?></td>
            <td><?php echo number_format($st['views'],0);?></td>
          </tr>
        </tbody>
  <?php
  }
  ?>
      </table>
<?php
}
?>
      <div class="panel-heading" align="left">
        <a class="btn btn-default btn-md" type="button" id="BackDetails" data-action="" href="<?php echo site_url('overview/main/overview/existing');?>">Back</a>&nbsp;
       <!--
        <a class="btn btn-warning btn-md" type="button" id="ForceGenerateData" data-action="" >Update NOW!</a>&nbsp;
        -->
        <a class="btn btn-success btn-md" type="button" id="GenerateCampaign" data-action="">Generate Campaign</a>&nbsp;
        <!--
        <a class="btn btn-info btn-md" type="button" id="addNewCampaign" data-action="" href="<?php echo site_url('dashboard/adwords_export/') . "/opt/" . $data['o']['cd']['token_key'];?>">Create new Campaign <br> <small>Under this optimizer</small></a>&nbsp;
        -->
<input type='hidden' value="<?php echo $data['o']['opt_id'];?>" id='opt_id' />
<input type='hidden' value="<?php echo $data['o']['cd']['token_key'];?>" id='token_key' />
      </div>
</div>
<?php
}
?>