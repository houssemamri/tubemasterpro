<h4>Update Pixels</h4>
<p class="label label-warning">Paste your Facebook, Twitter, Google and other Pixels</p>
<?php
if($o['session_msg_table']){
?>
    <div class="row">
        <div class="col-lg-12">
      <div class="alert alert-<?php echo $o['msg_type_sess']; ?>" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
        <span class="sr-only">Error:</span>
        <?php echo $o['msg_sess']; ?>
      </div>
    </div>
  </div>
<?php
}
?>

<?php echo form_open("affiliate/uppixels", array("id" => "signup_form", "autocomplete" => "off"));?>

       <div class="form-group has-error has-feedback">
        <label for="pixels">Pixels Handle:</label>
      
        <br />
         <?php echo bs_form_textarea($user_pixel);?>
      <span style="display:none;" class="glyphicon glyphicon-ok form-control-feedback" aria-hidden="true"></span>
      <span style="display:none;" class="glyphicon glyphicon-remove form-control-feedback" aria-hidden="true"></span>
      </div>
   
    <p><?php echo bs_form_submit('submit', 'Update Pixel', 'class="validify_button"');?> 
      <?php echo bs_form_input($update_button);?></p>

<?php echo form_close();?>