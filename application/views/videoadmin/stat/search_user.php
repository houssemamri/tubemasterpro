<?php
	if($o['show_search_table']){
?>
<div class="row" style="padding-top: 15px; padding-left: 20px;" >
       <div class="col-lg-10">
<div class="panel panel-default ">
  <!-- Default panel contents -->
  
   <div class="panel-heading"><strong>Search Users</strong></div>
  <!-- Table -->
  <table class="table">
    <tr>
      <td><input type="text" name="search_users" value="" id="search_users" class="form-control" placeholder="search users"></td>
    </tr>    
    <tr>
    <tr>
      <td><div id="search_notification">&nbsp;</div></td>
    </tr>    
    <tr>    
      <td>
		
      	<div id="search_result_here">&nbsp;</div></td>
    </tr>               
  </table>

<script>
$('#search_users').keyup(function(event){
    var Length = $("#search_users").val().length;
    var search_val = $("#search_users").val();
    if(Length > 2){
        $.ajax
        ({
        type: "POST",
        url: baseurl + "admin/search_result",
        data: ({"search_val" : search_val}),
        cache: false,
        beforeSend: function()
        {
        	//$("#search_notification").show();
        	$("#search_notification").html("Searching...");    

        },
        success: function(result)
        {
        	if(result == 'no_result_found'){
	        	$("#search_notification").html("No result found on search..."); 
	          	$("#search_result_here").html("");   
        	}else{
	        	//$("#search_notification").hide();
	        	$("#search_notification").html("Search Results"); 
	          	$("#search_result_here").html(result);    
	          }      
        }        
      }); 
    }
});

</script>
  
</div>
<?php
}

if($o['show_search_result_table']){
?>
 <table class="table">
	    <tr>
	      <td><strong>Name</strong> </td>
	      <td><strong>Email</strong></td>
	      <td><strong>Action</strong></td>
	    </tr>
	    <?php 
	    	$i = 0;
	    	foreach($o['show_res'] as $spu) { 
	    		$i++;
	    ?>
	    <tr>
	      <td><?php echo $i . ". " .ucfirst($spu['first_name']) . " " . ucfirst($spu['last_name']); ?></td>
	      <td><?php echo $spu['email']; ?></td>
	       <td> <a href="<?php echo site_url('admin/view_user_log') . '/' . $spu['id'];?>" target="_blank">View logs</a>&nbsp;<a href="<?php echo site_url('admin/view_user_log') . '/' . $spu['id'] . '/1';?>" target="_blank">Download logs</a></td>
	    </tr>
	    <?php } ?>

	  </table>
<?php
}
?>