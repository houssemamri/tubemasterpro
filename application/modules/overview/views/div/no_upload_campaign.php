<div class="modal-dialog">

    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="myModalLabel">HOLD ON!</h4>
      </div>
      <div class="modal-body">
      <div class="panel panel-default">
  <!-- Default panel contents -->
  <div class="panel-heading" align="center"><strong style="color:#a94442;">Hey! We can’t create until you upload your csv file to us that you downloaded from Adwords.</strong></div>
  <!-- Table -->
  <table class="table">
</table><table class="table">
        <thead>
          <tr>
            <th>Name</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($this->o as $sa){ 
             if($sa['is_available'] == '0'){
              echo "<td style=\"width:250px;padding-top:10px;\"><i class=\"fa fa-check\" style=\"color:#5cb85c;font-size: 1.5em;\"></i> &nbsp;{$sa['name']}</td><td style=\"width:470px;padding-top:10px;\"><button data-oc_id=\"{$sa['oid']}\" data-cid=\"{$sa['cid']}\" type=\"button\" class=\"btn btn-warning btn-md btn-info\" style=\"font-size:12px\"><span class=\"glyphicon glyphicon-info-sign\"></span> Campaign Information</button>
              <span class=\"btn btn-md btn-primary fileinput-button\" style=\"font-size:12px\"><i class=\"glyphicon glyphicon-upload\"></i><input class=\"btn-upload\" data-oc_id=\"{$sa['oid']}\" data-cid=\"{$sa['cid']}\" type=\"file\" name=\"campaign_file\"><span> Upload Adwords CSV File</span></span></td>";
            }else{
              echo "<td style=\"width:250px;padding-top:10px;\"><i class=\"fa fa-times\" style=\"color:#a94442;font-size: 1.5em;  \"></i> &nbsp;{$sa['name']} </td> <td style=\"width:470px;padding-top:10px;\"><button data-oc_id=\"{$sa['oid']}\" data-cid=\"{$sa['cid']}\" type=\"button\" class=\"btn btn-warning btn-md btn-info\" style=\"font-size:12px\"><span class=\"glyphicon glyphicon-info-sign\"></span> Campaign Information</button>
              <span class=\"btn btn-md btn-primary fileinput-button\" style=\"font-size:12px\"><i class=\"glyphicon glyphicon-upload\"></i><input class=\"btn-upload\" data-oc_id=\"{$sa['oid']}\" data-cid=\"{$sa['cid']}\" type=\"file\" name=\"campaign_file\"><span> Upload Adwords CSV File</span></span></td>";
            }
            echo "</tr>";
          } 
          ?>
          
                </tbody>
      </table>
  
</div>
      </div>
      <div class="modal-footer">
      	<div align="left">
       <a class="btn btn-default" type="button" id="" data-action="" href="<?php echo site_url('overview/main/overview/existing');?>">Back</a>
      	</div>
      </div>
    </div>
  </div>

<script src="<?php echo site_url('/assets/js/modules/compare-optimizer.js?v=') . time();?>"></script>    