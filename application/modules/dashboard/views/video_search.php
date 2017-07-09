<?php $main    = $this->session->flashdata('video_search_main'); ?>
<?php $page    = $this->session->flashdata('video_search_page'); ?>
<?php $entries = $this->session->flashdata('video_search_entries'); ?>
<ol class="breadcrumb">
  <li <?php echo (isset($main) && !empty($main)) ? '' : 'class="active"'; ?>><a href="<?php echo site_url('dashboard/keyword_search'); ?>">Keyword Search</a></li>
  <?php if (isset($main) && !empty($main)): ?>
  <li id="hook-four"><a id="v_breadcrumb" class="active" href="#" data-keyword="<?php echo $keyword; ?>" data-main="<?php echo $main; ?>" data-page="<?php echo $page; ?>" data-entries="<?php echo $entries; ?>"><?php echo $main; ?></a></li>
  <?php endif; ?>
</ol>
<h1>
    Video Search
</h1>
<input id="search_type" type="hidden" value="video" />
<form id="search_form" data-action="<?php echo site_url('dashboard/dashboard_ajax/video_search'); ?>" autocomplete="off" class="form-horizontal">
    <div class="col-sm-6">
        <div id="search-group" class="form-group">
            <div class="input-group">
                <input id="search_input" name="keyword" class="form-control" type="text" placeholder="Search Video" value="<?php echo $keyword; ?>">
                <span class="input-group-btn">
                    <button id="search_button" class="btn btn-primary" type="submit">Search</button>
                </span>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-3" style="padding-right:2px;">
                <p style="margin:7px 0;"><strong>Max Results</strong></p>
            </div>
            <div class="col-sm-3" style="padding-left:0">
                <select id="max-results" name="search_filter[max]" class="form-control">
                    <option value="10" selected="">10</option>
                    <?php if(!$this->ion_auth->in_group(3)): ?>
                    <option value="50">50</option>
                    <option value="100">100</option>
                    <option value="500">500 and more!</option>
                    <?php endif; ?>
                </select>
            </div>
            <?php
              $is_logged_in = $this->ion_auth->logged_in();
              if ( $is_logged_in && $this->ion_auth->in_group(3) ) { ?>
                <div class="col-sm-6">
                  <div class="alert alert-danger">NOTE: Full verion will allow you to find up to 7,000 monetized videos!</div>
                </div>
            <?php } ?>
        </div>
        <div class="form-group">
            <div class="col-sm-12">
                <button id="resume-search-button" class="btn btn-primary col-sm-12" type="submit" data-action="<?php echo site_url('dashboard/dashboard_ajax/video_search'); ?>">Resume Search</button>
            </div>
        </div>
    </div>
    <div id="right-side" class="col-sm-6">
        <div id="hook-three" class="form-group">
            <div class="col-sm-6">
                <input id="save-target-input" name="target_name" class="form-control" type="text" placeholder="" value="">
            </div>
            <div class="col-sm-6">
                <button id="save-target-button" class="btn btn-disabled col-sm-12" disabled="disabled" type="button" data-action="<?php echo site_url('dashboard/dashboard_ajax/save_target'); ?>">Save As New Target</button>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-6">
                <select id="targets" name="targets" class="form-control">
                    <option value="0" selected="">Select Target</option>
                    <?php if ( $targets ): ?>
                        <?php echo $targets; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div class="col-sm-6">
                <button id="add-target-button" class="btn btn-disabled col-sm-12" disabled="disabled" type="button" data-action="<?php echo site_url('dashboard/dashboard_ajax/update_target'); ?>">Add To Existing Target</button>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-12">
                <button id="get-ytlinks-button" class="btn btn-disabled col-sm-12" disabled="disabled" type="button">Get Video Links</button>
				<textarea class="form-control" id="links" rows="10"></textarea>
            </div>
        </div>
    </div>
</form>

<div id="search_result">
    <p></p>
    <table class='stupid table table-bordered'>
        <thead>
            <tr>
                <th><input id="check_all" type="checkbox" /></th>
                <th></th>
                <th>Title</th>
                <!-- <th>Channel</th> -->
                <th>Link Url</th>
                <th>Views</th>
                <th>Likes</th>
                <th>Dislikes</th>
                <th>Comments</th>
                <th>Published At</th>
                <!-- <th>Monetized</th> -->
            </tr>
        </thead>
        <tbody>
        	
        </tbody>
    </table>
</div>

<div class="modal fade" id="video_search_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">

    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="myModalLabel">Message</h4>
      </div>
      <div class="modal-body" style="text-align:center;">
          <p id="notice-content">
          </p>
          
      </div>
      <div class="modal-footer">
        <!-- <a id="copy-description" type="button" class="btn btn-default">Copy</a> -->
        <button type="button" class="btn btn-default" data-dismiss="modal">Ok</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="search_notice_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">

    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title text-center" id="myModalLabel">SUCCESS</h4>
      </div>
      <div class="modal-body text-center">
      	
      </div>
      <div class="modal-footer">
        <!-- <a id="copy-description" type="button" class="btn btn-default">Copy</a> -->
        <button type="button" class="btn btn-default" data-dismiss="modal">Ok</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="temp_search_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">

    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="myModalLabel">Get 500 monetized videos <small>( We will notify you through email when done. )</small></h4>
      </div>
      <div class="modal-body">
	      <form class="form-horizontal">
	          <div class="form-group">
		      	<div class="col-sm-6 has-error">
		      		<input id="temp_videos_new_input" name="target_name" class="form-control" type="text" data-placement="top" data-toggle="popover" data-trigger="manual" data-content="Target Name Exists!">
		      	</div>
		      	<div class="col-sm-6">
			      	<button id="temp_videos_new" class="btn btn-success col-sm-12" type="button" data-action="<?php echo site_url('dashboard/dashboard_ajax/bulk_video_search'); ?>">Save As New Target</button>
		      	</div>
	          </div>
	          <div class="form-group">
		      	<div class="col-sm-6">
			      	<select id="temp_targets" name="search_filter[max]" class="form-control">
		                <?php if ( $targets ): ?>
		                    <?php echo $targets; ?>
		                <?php endif; ?>
		            </select>
		      	</div>
		      	<div class="col-sm-6">
				  	<button id="temp_videos" data-action="<?php echo site_url('dashboard/dashboard_ajax/bulk_video_search'); ?>" type="button" class="btn btn-success col-sm-12">Save To Target List</button>
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

<div class="modal fade" id="recent_search_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">

    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title text-center" id="myModalLabel">Message</h4>
      </div>
      <div class="modal-body text-center">
      
      </div>
      <div class="modal-footer">
        <!-- <a id="copy-description" type="button" class="btn btn-default">Copy</a> -->
        <button id="continue_search" type="button" class="btn btn-success" data-action="<?php echo site_url('dashboard/dashboard_ajax/continue_search'); ?>">Continue</button>
        <button id="reset_search" type="button" class="btn btn-danger" data-action="<?php echo site_url('dashboard/dashboard_ajax/reset_search'); ?>">Start Over</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="video_confirmation_modal" tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="myModalLabel" aria-hidden="true">
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
        <button id="video-confirmation-ok" type="button" class="btn btn-success" data-dismiss="modal">Ok</button>
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