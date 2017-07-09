<?php $main    = $this->session->flashdata('channel_search_main'); ?>
<?php $page    = $this->session->flashdata('channel_search_page'); ?>
<?php $entries = $this->session->flashdata('channel_search_entries'); ?>
<ol class="breadcrumb">
  <li <?php echo (isset($main) && !empty($main)) ? '' : 'class="active"'; ?>><a href="<?php echo site_url('dashboard/keyword_search'); ?>">Keyword Search</a></li>
  <?php if (isset($main) && !empty($main)): ?>
  <li ><a id="c_breadcrumb" class="active" href="#" data-keyword="<?php echo $keyword; ?>" data-main="<?php echo $main; ?>" data-page="<?php echo $page; ?>" data-entries="<?php echo $entries; ?>"><?php echo $main; ?></a></li>
  <?php endif; ?>
</ol>
<h1>
    Channel Search
</h1>
<input id="search_type" type="hidden" value="channel" />
<form id="search_form" data-action="<?php echo site_url('dashboard/dashboard_ajax/channel_search'); ?>" autocomplete="off" class="form-horizontal">
    <div class="col-sm-12">
        <div id="search-group" class="form-group">
            <div class="col-sm-6 input-group" style="float:left;">
                <input id="search_input" name="keyword" class="form-control" type="text" placeholder="Search Channel" value="<?php echo $keyword; ?>">
                <span class="input-group-btn">
                    <button id="search_button" class="btn btn-primary" type="submit">Search</button>
                </span>
            </div>
            <div class="col-sm-3 text-right" style="padding-right:2px;">
                <p style="margin:7px 0;"><strong>Max Results</strong></p>
            </div>
            <div class="col-sm-3">
                <select id="max-results" name="search_filter[max]" class="form-control">
                    <option value="10" selected="">10</option>
                    <?php if(!$this->ion_auth->in_group(3)): ?>
                    <option value="50">50</option>
                    <option value="100">100</option>
                    <option value="500">500</option>
                    <?php endif; ?>
                </select>
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <button id="resume-search-button" class="btn btn-primary col-sm-12" type="submit" data-action="<?php echo site_url('dashboard/dashboard_ajax/channel'); ?>">Resume Search</button>
    </div>
</form>
<div id="search_result">
	<p></p>
    <table class='stupid table table-bordered'>
        <thead>
            <tr>
                <th></th>
                <th>Channel Name</th>
                <th>Subscribers</th>
                <th>Views</th>
                <th>Videos Uploaded</th>
                <th>Recent Video</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
        	
        </tbody>
    </table>
</div>

<div class="modal fade" id="channel_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" aria-hidden="true">
  <div class="modal-dialog">

    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="myModalLabel">Get all monetized videos from that channel (limited to <?php echo ($this->ion_auth->in_group(3)) ? 10 : 500; ?>)</h4>
      </div>
      <div class="modal-body">
	      <form class="form-horizontal">
	          <div id="hook-seven" class="form-group">
  		      	<div class="col-sm-6">
  		      		<input id="extract_videos_new_input" name="target_name" class="form-control" type="text" placeholder="" value="">
  		      	</div>
  		      	<div class="col-sm-6">
  			      	<button id="extract_videos_new" data-videos="" data-playlist-id="" data-channel-title="" class="btn btn-success col-sm-12" type="button" data-action="<?php echo site_url('dashboard/dashboard_ajax/extract_videos_new'); ?>">Save As New Target</button>
  		      	</div>
	          </div>
	          <div class="form-group">
  		      	<div class="col-sm-6">
  			      	<select id="channel_targets" name="search_filter[max]" class="form-control">
  		                <?php if ( $targets ): ?>
  		                    <?php echo $targets; ?>
  		                <?php endif; ?>
  		            </select>
  		      	</div>
		      	<div class="col-sm-6">
				  	 <button id="extract_videos" data-videos="" data-target-id="" data-playlist-id="" data-channel-title="" data-action="<?php echo site_url('dashboard/dashboard_ajax/extract_videos'); ?>" type="button" class="btn btn-success col-sm-12">Save To Target List</button>
		      	</div>
	          </div>
	      </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="search_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">

    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="myModalLabel">Message</h4>
      </div>
      <div class="modal-body" style="text-align:center;">
          <p id="notice-content">
            Please type a Channel to search
          </p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="confirmation_modal" tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">

    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="myModalLabel">Message</h4>
      </div>
      <div class="modal-body" style="text-align:center;">
          <p id="confirmation_message">
            OK, we'll email you once this task is done as it can take ages. Go ahead and do something else in the mean time!
          </p>
      </div>
      <div class="modal-footer">
        <button id="confirmation-ok" type="button" class="btn btn-success" data-dismiss="modal">Ok</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="videoModal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header text-right" style="padding:5px 15px;">
        <button type="button" style="float:none;font-size:30px;" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <!-- <h4 class="modal-title">BC Winegrowers Series</h4> -->
      </div>
      <div class="modal-body text-center">
      </div>
    </div>
  </div>
</div> 
