<div class="panel panel-default ">
  <!-- Default panel contents -->
  <!-- Table -->
  <table class="table">
    <tr>
        <td><strong>Names </strong></td>
    </tr>
    <?php $i = 1; foreach($o['sr'] as $su) { ?>
    <tr>
        <td><?php echo $i . ". " . $su['user_aff']; ?></td>
    </tr> 
    <?php  $i++; } ?>      
  </table>
</div>