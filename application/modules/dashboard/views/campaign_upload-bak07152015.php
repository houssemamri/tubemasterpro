<input type="hidden" name="baseurl" id="baseurl" value="<?php echo site_url(''); ?>"/>
<input type="hidden" name="videoCount" id="videoCount" value=""/>
<h1></h1>
<div class="container-fluid">
	<table id="campaign-table" class="table table-striped table-bordered" cellspacing="0" width="100%">
	    <thead>
	        <tr>
	            <th>Campaign Name</th>
	            <th>Date Uploaded</th>
	            <th>Manage</th>
	        </tr>
	    </thead>
	
	    <tbody>
	        <?php echo $campaigns; ?>
	    </tbody>
	</table>
</div>

<div class="modal fade" id="campaign-upload-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
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
				<button type="button" class="btn btn-success" data-dismiss="modal">Ok</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="campaign_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">

    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="myModalLabel">Message</h4>
      </div>
      <div class="modal-body" style="text-align:center;">
          <p>
            Are you sure you want to delete this Campaign
          </p>
      </div>
      <div class="modal-footer">
        <button id="campaign-delete-btn" type="button" class="btn btn-danger">Delete</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
      </div>
    </div>
  </div>
</div>