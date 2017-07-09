<div id="footer">
    <div class="container">
    	<p class="footer-content">
		   <!-- <a href="#" onclick="popup_support('<?php echo site_url('review/main'); ?>','iframe'); return false;">Post Review</a> -->
		</p>
    </div>
</div>

<div class="modal fade text-left" id="freemium_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">

    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="myModalLabel">Message</h4>
      </div>
      <div class="modal-body">
          
      </div>
      <div class="modal-footer">
        <button id="modal-no" type="button" class="btn btn-default" data-dismiss="modal">Ok</button>
      </div>
    </div>
  </div>
</div>

  <div class="modal fade" id="modalCheckBrowser" aria-hidden="true" data-backdrop="static" style="display: none;">
      <div class="modal-dialog">
          <div class="modal-content">
              <div class="modal-header">
                  <h4 class="modal-title text-center text-danger">ALERT</h4>
              </div>
              <div class="modal-body">
                  We <strong>ONLY SUPPORT CHROME BROWSER ON A COMPUTER</strong>. Yes, we realise you might be using Safari or whatever, but our product REQUIRES Google Chrome to work. So go get that now and you'll be in TubeMasterPro heaven
              </div>
              <div class="modal-footer">
                 <a href="#" class="btn btn-danger" type="button" data-dismiss="modal">OK</a>
              </div>
          </div>
      </div>
  </div>

<?php
if ($this->ion_auth->in_group(3) ){
?>
<div class="modal fade" id="modalBecomeAffiliate" aria-hidden="true" data-backdrop="static" style="display: none;">
      <div class="modal-dialog">
          <div class="modal-content">
              <div class="modal-header">
                  <h4 class="modal-title text-center text-success">Become an Affiliate</h4>
              </div>
              <div class="modal-body">
                  Want to become an affiliate for TubeMasterPro? Nice!
                  <br><br>
We're not like other affiliate schemes that don't pay out. Therefore we DON'T take all affiliates who apply.
<br><br>
First rule of TMP Fight Club? Yeah, you have to use our software to know what it's about. Get a paid subscription and that will unlock the ability for you to apply as an Affiliate.
              </div>
              <div class="modal-footer">
                  <a href="#" class="btn btn-danger" type="button" data-dismiss="modal">Cancel</a>
                  <a href="<?php echo site_url('subscription'); ?>" class="btn btn-success" type="button" data-dismiss="modal">UPGRADE NOW</a>
                  
              </div>
          </div>
      </div>
  </div>
<?php
}
?>  
<script>
function popup_support(link,type){
  $.magnificPopup.open({
    items: {
      src: link
    },
    type: '' + type +''
  });
}       
</script>