<?php if(!$targets['approved']){
	echo "<script> window.location.replace('".$targets['uri']."dashboard');</script>";
}	
?>
<style type="text/css">
</style>

<input type="hidden" name="baseurl" id="baseurl" value="<?php echo site_url(''); ?>">
<input type="hidden" name="upload_id" id="upload_id" value="<?php echo $targets['uv_id']; ?>">
<input type="hidden" name="action_path" id="action_path" value="<?php echo site_url('dashboard/uploadvid_ajax'); ?>">
<?php if(!$targets['pending']){   ?>

<div style="width:450px; background: #fff; margin: auto; padding: 50px;">
	<h1 style="display: inline-block; margin:0px; vertical-align:middle; padding-right: 20px;">USD <font>$147</font></h1> <button type="button" style="display: inline-block; vertical-align:middle" class="btn btn-success btn-lg" onclick="window.location.replace(<?php echo site_url(''); ?>.'/subscription/video_upload_payment');">BUY NOW</button>
</div>

<?php 

}
else { 

?>

<h1>
    Upload Raw Video 
</h1>
<form id="upload_form" rel="async" action="" autocomplete="off">
 <!-- <h3>Upload raw video</h3>       -->
  <div id="notification"></div>
  <!-- <p>Valid format: *.mp4, *.avi</p> -->
<p>
    <label for="file">Upload Raw Video (Max 100Mb):</label> <br />
    <input type="hidden" name="new_filename" value="" id="new_filename" class="form-control" /> 
    <input type="hidden" name="original_filename" value="" id="original_filename" class="form-control" /> 
    <div class="attach_video_file_notif"></div>
    <p class="attach_video_file"><input type="file" name="afile1" id="afile1" accept=".mts, .avi, .mp4, .mov"/> </p> 
                <div class="progress progress-sm progress-striped"  style="display:none;">
                <div class="progress progress-bar progress-bar-success" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 80%">
                <span class="sr-only">0% Complete</span>

                </div>
              </div>
              <p class="text-center cancel_button" style="display:none;"><button type="button" class="btn btn-warning btn-xs" id="cancel_upload">Cancel Upload</button></p>

   <p style="clear:both;"></p>
</p>
</form>

<?php } ?>

<div class="table-responsive">
    <table id="video-table" class="table">
        <thead>
            <tr>
                <th>Original Filename</th>
                <th>Date Uploaded</th>
                <th>Status</th>
                <th>Date Changed</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>




<?php

if($targets['vid_data']) {
  foreach ($targets['vid_data'] as $key => $value) {
    if(count($value) > 0) {
      echo "<tr>";
      //echo "<pre>";

      $row = array();

      foreach ($value as $key2 => $value2) {
        //echo $key2."=>".$value2;
        $row[$key2] = $value2;
      }

      $status = "";

      if($row['upload_status'] == 1) {
        $status = "Pending";
      }
      else if($row['upload_status'] == 2) {
        $status = "Processing";
      }
      else if($row['upload_status'] == 3) {
        $status = "Completed";
      }

      $date = date('d M Y', $row['date_uploaded']);
      $date2 = ($row['date_update'] != "") ? date('d M Y', $row['date_update']) : "";

      echo '<td>'.$row['orig_filename'].'</td><td>'.$date.'</td><td>'.$status.'</td><td>'.$date2.'</td><td><a class="view_details" data-vid-details="'.$row['id'].'" href="javascript:void(0)"><span class="glyphicon glyphicon-info-sign"></span> View Details</a></td>';


      //echo "</pre>";
      echo "</tr>";
    }
  }
}

?>

        </tbody>
    </table>
</div>

<?php if($targets['pending']){   ?>

<script>

    document.querySelector('#afile1').addEventListener('change', function(e) {
        var baseurl     = $("#baseurl").val();
        var cancel_upload = document.getElementById('cancel_upload');
        var file_name = $("#afile1").val();
        var file = this.files[0];

      if(file_name == ''){
        return;
      }
      var fd = new FormData();
      fd.append("afile1", file);

      var xhr = new XMLHttpRequest();
      xhr.open('POST', baseurl + 'uploadvid_ajax/upload_file', true);
     
      function detach() {
            // remove listeners after they become irrelevant
        cancel_upload.removeEventListener('click', canceling, false);
      }
     function canceling() {
          detach();
          xhr.abort();
          console.log('Cancel upload');
          disable_progress_disaply();
      }

      xhr.upload.onprogress = function(e) {
        if (e.lengthComputable) {
          var percentComplete = (e.loaded / e.total) * 100;

          $(".progress").css('display' , 'block');
          $(".cancel_button").css('display' , 'block');
          
          $(".progress-bar").attr('aria-valuenow' , percentComplete);
          $(".progress-bar").css('width' , percentComplete +'%');
          $(".progress-bar").text(Math.round(percentComplete) + "%");
          console.log(percentComplete + '% uploaded');
        }
      };


      xhr.onload = function() {
        if (this.status == 200) {
          setTimeout("disable_progress_disaply()", 1500);
          var resp = JSON.parse(this.response);
          console.log('Server got:', resp);

            if(resp.error == 1){
                  $(".attach_video_file_notif").html("<div class=\"alert alert-danger\" role=\"alert\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button> " + resp.error_msg +"</div>");
            }else{
                  $("#original_filename").val(resp.orig_filename);
                  $("#new_filename").val(resp.filename);

                  $(".attach_video_file").css('display' , 'none');
                  
                  updateUploadVideo();

            }
        };
      };

      xhr.send(fd);

        // and, of course, cancel if "Cancel" is clicked
        cancel_upload.addEventListener('click', canceling, false);

    }, false);

    function disable_progress_disaply(){
        $(".progress-bar").css('width' ,  '0%');
        $(".progress").css('display' , 'none');
        $(".cancel_button").css('display' , 'none');
    }
     function remove_uploaded_video(){
        $(".attach_video_file_notif").css('display' , 'none');
        $(".attach_video_file").css('display' , 'block');
        $("#original_filename").val('');
        $("#new_filename").val('');    


     }

</script>

<?php } ?>



