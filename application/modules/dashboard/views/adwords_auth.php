<h1 id="target_header">
    Authorization Needed
</h1>
<?php if($auth_url):?>
<div class="col-sm-12">
    <p> Please visit <a href="<?php echo $auth_url?>">this link</a> to authorize your account.</p>
</div>
<?php endif;?>