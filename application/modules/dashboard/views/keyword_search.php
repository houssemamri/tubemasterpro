<?php
  $main    = $this->session->flashdata('video_search_main');
  $page    = $this->session->flashdata('video_search_page');
  $entries = $this->session->flashdata('video_search_entries');
  $keyword = $this->session->flashdata('video_search_keyword');
  if ( empty($main) ) {
    $main = $this->session->flashdata('channel_search_main');
  }
  if ( empty($page) ) {
    $page = $this->session->flashdata('channel_search_page');
  }
  if ( empty($entries) ) {
    $entries = $this->session->flashdata('channel_search_entries');
  }
  if ( empty($keyword) ) {
    $keyword = $this->session->flashdata('channel_search_keyword');
  }
?>
<h1>
    Keyword & Keyphrase Search
</h1>
<h3>
    Find Related Keywords and Keyphrases Searched in YouTube
</h3>
<form id="keyword_form" class="form-horizontal" rel="async" action="<?php echo site_url('dashboard/dashboard_ajax/keywords'); ?>" autocomplete="off">
    <input name="main" value="<?php echo $main; ?>" type="hidden" />
    <input name="page" value="<?php echo $page; ?>" type="hidden" />
    <input name="entries" value="<?php echo $entries; ?>" type="hidden" />
    <input name="sug" value="<?php echo $keyword; ?>" type="hidden" />
    <div id="hook-one" class="form-group">
        <div class="col-sm-8">
            <input id="keyword_input" name="keyword" class="form-control" type="text" value="<?php echo $main; ?>" placeholder="Enter Keyword">
        </div>

        <div class="col-sm-4">
            <button id="keyword_search" class="btn btn-primary col-sm-12" type="submit">Search</button>
        </div>
    </div>
</form>
<div id="search_result"></div>

<!-- <div class="modal fade" id="upgrade_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">

    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="myModalLabel">Message</h4>
      </div>
      <div class="modal-body" style="text-align:center;">
        <p>
                Please upgrade your account to access this feature 
                <a href="http://veeroll.com/pricing" class="btn btn-success  upgrade_free">Upgrade Now</a>
            </p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div> -->
<!-- <div class="panel panel-primary">
    <div class="panel-heading">Keyword</div>
    <table class="table">
        <tbody>
            <tr>
                <td style="width:50%;">Data</td>
                <td><button class="btn btn-primary" type="button">Video Search</button></td>
                <td><button class="btn btn-primary" type="button">Channel Search</button></td>
            </tr>
        </tbody>
    </table>
</div> -->