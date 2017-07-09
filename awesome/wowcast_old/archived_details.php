<?php 

session_start();
require_once("server.php");

print_r($video_arch);

$vid_url = $video_arch['url'];	
	
//echo $vid_url;

?>

<!DOCTYPE html>
<html lang="en">
	<head prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb# video: http://ogp.me/ns/video#">
    <meta charset="UTF-8">
    <!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
	<link rel="stylesheet" href="<?php echo $baseurl; ?>assets/css/font-awesome.css">
	
	<link rel="stylesheet" href="<?php echo $baseurl; ?>assets/fonts/wc-fonts.css">
	<link rel="stylesheet" href="<?php echo $baseurl; ?>assets/css/wc.css">
	
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
	
	<style>
		.dib {
			display: inline-block;
		}
		
		.leftside-vid {
			width:68%;
		}
		
		.rightside-vid {
			width:30%;
		}
		
		.vat {
			vertical-align: top;
		}
		
		.vam {
			vertical-align: middle;
		}
		
		.wc-player-body {
			overflow-x: auto;
		}
		
		.wc-player-container {
			width:640px;
			height:480px;
			background-color: #101010;
		}
	</style>
	
  </head>

  <body role="document">

    <div class="container">

    </div>
    
    <!-- Trigger the modal with a button -->
<button type="button" class="btn btn-info btn-lg" data-toggle="modal" data-target="#myModal">Open Modal</button>

<!-- Modal -->
<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Modal Header</h4>
      </div>
      <div class="modal-body wc-player-body">
    	<div class="row wc-player-container">
    		<div class="leftside-vid dib vat">
		       <video width="100%" height="100%" controls>
		        <source src="<?php echo $vid_url; ?>" type="video/mp4">
		        Your browser does not support HTML5 video.
		      </video>
    		</div>
    		<div class="rightside-vid dib vat">
				<p>Chat</p>
		    </div>
    	</div>
      </div>
    </div>

  </div>
</div>
    
    <!--<iframe style='max-width: 100%;' src='https://blab.im/australiawow' frameborder='0' height='480' scrolling='none' width='640'></iframe> -->

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="dist/js/bootstrap.min.js"></script>
  </body>
</html>
