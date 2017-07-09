<?php
if($o['show_log_table']){
?>
<h2>User Logs</h2>
<table>
	<tr>
		<td style="width: 200px;"><strong>Name:</strong</td>
		<td> <?php echo $o['gu']['name'];?></td>
	</tr>
	<tr>
		<td ><strong>Signup date:</strong</td>
		<td> <?php echo $o['gu']['created_on'];?></td>
	</tr>
	<tr>
		<td ><strong>First Login:</strong</td>
		<td> <?php echo $o['gu']['first_login'];?></td>
	</tr>
	<tr>
		<td ><strong>Last Login:</strong</td>
		<td> <?php echo $o['gu']['last_login'];?></td>
	</tr>
	<tr>
		<td ><strong>Total Logins:</strong</td>
		<td> <?php echo number_format($o['gu']['ccl']['count_login'],0);?></td>
	</tr>
	<tr>
		<td ><strong>Total Keyword search:</strong</td>
		<td> <?php echo number_format($o['gu']['ccl']['count_keyword_search'],0);?></td>
	</tr>
	<tr>
		<td ><strong>Total Channel search:</strong</td>
		<td> <?php echo number_format($o['gu']['ccl']['count_channel_search'],0);?></td>
	</tr>
	<tr>
		<td ><strong>Total Extract videos:</strong</td>
		<td> <?php echo number_format($o['gu']['ccl']['count_extract_videos'],0);?></td>
	</tr>
	<tr>
		<td ><strong>Total video searched:</strong</td>
		<td> <?php echo number_format($o['gu']['ccl']['count_video_search'],0);?></td>
	</tr>	
	<tr>
		<td ><strong>Total export campaign:</strong</td>
		<td> <?php echo number_format($o['gu']['ccl']['count_export_campaign'],0);?></td>
	</tr>								
</table>
<?php if($o['show_logs_table']){ ?>
<hr noshade>
<h2>Detailed logs</h2>
<table class="table">
	    <tr>
	      <td><strong>Log Type</strong> </td>
	      <td><strong>Details</strong></td>
	      <td><strong>Date</strong></td>
	    </tr>
	    <?php 
	    	$i = 0;
	    	foreach($o['show_logs'] as $spu) { 
	    		$i++;
	    ?>
	    <tr>
	      <td><?php echo $i . ". " .ucfirst($spu['log_type'])	; ?></td>
	      <td><?php echo $spu['log_desc']; ?></td>
	       <td> <?php echo $spu['date_added'];?></td>
	    </tr>
	    <?php } ?>

	  </table>
<?php
	}
}
?>
<?php
if($o['download_log_table']){
?>
User Logs

Name: <?php echo $o['gu']['name'];?>

Signup date: <?php echo $o['gu']['first_login'];?>

First Login: <?php echo $o['gu']['first_login'];?>

Last Login: <?php echo $o['gu']['last_login'];?>

Total Logins: <?php echo number_format($o['gu']['ccl']['count_login'],0);?>

Total Keyword search: <?php echo number_format($o['gu']['ccl']['count_keyword_search'],0);?>

Total Channel search: <?php echo number_format($o['gu']['ccl']['count_channel_search'],0);?>

Total Extract videos: <?php echo number_format($o['gu']['ccl']['count_extract_videos'],0);?>

Total video searched: <?php echo number_format($o['gu']['ccl']['count_video_search'],0);?>

Total export campaign: <?php echo number_format($o['gu']['ccl']['count_export_campaign'],0);?>


<?php if($o['show_logs_table']){ ?>
Detailed logs
<?php 
		$i = 0;
		foreach($o['show_logs'] as $spu) { 
		$i++;
?>

<?php echo $i . ". " .ucfirst($spu['log_type'])	; ?>  | <?php echo $spu['log_desc']; ?> | <?php echo $spu['date_added'];?>


<?php 
		} 
	}
?>
<?php
}
?>