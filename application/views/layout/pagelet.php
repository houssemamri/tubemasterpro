<?php echo $header; ?>

<div class="container">
    <div class="row">
    	
        <div class="<?php if($o['page'] == "support"){	echo "col-lg-12"; } else{ echo "col-lg-offset-3 col-lg-6"; } ?>">
            <?php echo $main_content; ?>
        </div>
    </div>
</div><!-- /.container -->

<?php echo $footer; ?>