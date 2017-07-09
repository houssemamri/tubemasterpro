<h1>
    Monetize Check
</h1>
<h3>
    Find Monetize Videos in YouTube
</h3>
<form rel="async" action="<?php echo site_url('dashboard/dashboard_ajax/butong'); ?>" autocomplete="off">
    <div class="form-group">
        <div class="input-group">
            <input id="ytlink" name="ytlink" class="form-control" type="text" placeholder="Enter YouTube link">
            <span class="input-group-btn">
                <button id="monetize_check" class="btn btn-primary" type="submit">Check</button>
            </span>
        </div>
    </div>
</form>
<div id="search_result"></div>

<div class="modal fade" id="upgrade_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
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
</div>
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