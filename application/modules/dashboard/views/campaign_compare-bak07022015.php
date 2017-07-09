<input type="hidden" name="baseurl" id="baseurl" value="<?php echo site_url(''); ?>"/>
<input type="hidden" name="videoCount" id="videoCount" value=""/>
<h1></h1>
<div class="container-fluid">
	<table id="campaign-table" class="table table-striped table-bordered" cellspacing="0" width="100%">
	    <thead>
	        <tr>
	            <th>Campaign Name</th>
	            <th>Ads</th>
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
			<div class="modal-header">
				<h4 class="modal-title" id="myModalLabel">Upload AdWords CSV</h4>
			</div>
			<div class="modal-body" style="text-align:center;">
				<!-- The fileinput-button span is used to style the file input field as button -->
			    <span class="btn btn-success fileinput-button">
			        <i class="glyphicon glyphicon-plus"></i>
			        <span>Upload AdWords CSV</span>
			        <!-- The file input field used as target for the file upload widget -->
			        <input id="campaign-upload" type="file" name="campaign_file">
			    </span>
			    <br>
			    <br>
			    <!-- The global progress bar -->
			    <div id="progress" class="progress" style="display:none;">
			        <div class="progress-bar progress-bar-success progress-bar-striped active"></div>
			    </div>
			    <!-- The container for the uploaded files -->
			    <div id="files" class="files"></div>
			    <div id="compare-div" class="row" style="margin-top:20px;">
			    </div>
			</div>
			<div class="modal-footer">
				<button style="display:none;" id="campaign-save-btn" type="button" class="btn btn-success">Save Changes</button>
				<button id="campaign-error-btn" type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
			</div>
		</div>
	</div>
</div>