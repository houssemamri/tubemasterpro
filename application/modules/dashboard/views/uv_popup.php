<?php
	
	if($targets['approved']) {
		echo "<script> window.location.replace('".$targets['uri']."dashboard/upload_videos');</script>";
	}
	else {
?>

<div style="width:450px; background: #fff; margin: auto; padding: 50px;">
	<h1 style="display: inline-block; margin:0px; vertical-align:middle; padding-right: 20px;">USD <font>$397</font></h1> <button type="button" style="display: inline-block; vertical-align:middle" class="btn btn-success btn-lg" onclick="window.location.replace('<?php echo $targets['uri']; ?>subscription/video_upload_payment');">BUY NOW</button>
</div>

<?php } ?>