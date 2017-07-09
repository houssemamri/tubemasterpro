<div style="width:450px; background: #fff; margin: auto; padding: 50px;">
	<?php	

	if($targets['vid_data']) {
	 foreach ($targets['vid_data'] as $key => $value) {
	    if(count($value) > 0) {
	      $row = array();

	      foreach ($value as $key2 => $value2) {
	        //echo $key2."=>".$value2;
	        $row[$key2] = $value2;
	      }
	    }
	  }
	}
	?>



	<p>
	    <label for="name">Filename: </label> 
	     <?php echo $row['orig_filename']; ?> <?php if($row['update_status'] == 3) { ?> | <a href="<?php echo $row['video_path_done'] ?>"><span class="glyphicon glyphicon-download"></span> Download file</a> <?php } ?>
	</p>
	<p>
	    <label for="name">Notes: </label> 
	     <?php if($row['notes'] == "") echo "None"; else echo $row['notes']; ?>
	</p>

	<?php if($row['upload_status'] == 3) { ?> <p> <a href="<?php echo $targets['uri'].'dashboard/download_file/'.$targets['vid_id']; ?>" class="btn btn-success"><span class="glyphicon glyphicon-download"></span> Download file</a> </p><?php } ?>



</div>
