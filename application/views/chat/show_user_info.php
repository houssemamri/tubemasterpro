<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title><?php echo $o['title']; ?></title>
        <meta name="description" content="<?php echo $description; ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- Extra metadata -->
        <?php echo $metadata; ?>
        <!-- / -->

        <!-- favicon.ico and apple-touch-icon.png -->

        <!-- Bootstrap core CSS -->
        <link rel="stylesheet" href="<?php echo assets_url('css/bootstrap.min.css'); ?>">
        <!-- Font Awesome CSS -->
        <link rel="stylesheet" href="<?php echo assets_url('css/font-awesome.min.css'); ?>">
        <!-- Custom styles -->
        <link rel="stylesheet" href="<?php echo assets_url('css/main.css'); ?>">
         <link rel="stylesheet" href="<?php echo assets_url('js/font-awesome/css/font-awesome.min.css'); ?>">
         
        <?php echo $css; ?>
        <!-- / -->

        <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
            <script src="<?php echo assets_url('js/html5shiv.min.js'); ?>"></script>
            <script src="<?php echo assets_url('js/respond.min.js'); ?>"></script>
        <![endif]-->
  
    </head>
<body>
	<div class="panel panel-default ">
  <!-- Default panel contents -->
  
   <div class="panel-heading"><strong>User Details of <?php echo $o['sr']['first_name'] . " " . $o['sr']['last_name']; ?></strong></div>
  <!-- Table -->
  <table class="table">
 
        <tr>
      <td><strong>Last Subscription date:</strong> </td>
      <td><?php echo $o['sr']['start_date']; ?></td>
    </tr>

 	<tr>
    	<td><strong>Next Subscription date:</strong> </td>
    	<td><?php echo $o['sr']['next_billing_date']; ?></td>
    </tr>    
    <tr>
      <td><strong>Payment Cycle:</strong> </td>
      <td><?php echo number_format($o['sr']['cycles_completed'],0); ?></td>
    </tr> 
    <?php if($o['sr']['aff_status'] == 'approved') { ?> 
    <tr>
      <td><strong>Affiliate Since:</strong> </td>
      <td><?php echo $o['sr']['aff_added'] . " (". $o['sr']['start_date_human'] .")"; ?></td>
    </tr>     
    <tr>
      <td><strong>Current paying Subscribers:</strong> </td>
      <td><?php echo number_format($o['sr']['active_count_users'],0) . " / " . number_format($o['sr']['affiliate_count'],0); ?></td>
    </tr>
    <tr>
      <td><strong>Chargeback percentage:</strong> </td>
      <td><?php echo $o['sr']['chargeback']; ?></td>
    </tr>
    <?php
    	}
    	else{
    ?>
        <tr>
      <td><strong>Not an Affiliate user</strong> </td>
      <td><?php echo $o['sr']['aff_status'];  ?></td>
    </tr>    
    <?php 
    }
    ?>   
    <?php if($o['show_logs_table']){
    ?>
    <tr>
      <td colspan="2">
<div class="panel panel-default">
  <!-- Default panel contents -->
  <div class="panel-heading">User Logs</div>

  <!-- Table -->
  <table class="table">
        <thead>
          <tr>
            <th>#</th>
            <th>Log Type</th>
            <th>Description</th>
            <th>Date</th>
          </tr>
        </thead>
                  <?php
            $count = 1;
            foreach($o['show_logs'] as $sl){
          ?>
        <tbody>
          <tr>
            <th scope="row"><?php echo $count; ?></th>
            <td><?php echo $sl['log_type'];?></td>
            <td><?php echo $sl['log_desc'];?></td>
            <td><?php echo $sl['date_added'];?></td>
          </tr>
           <?php
              $count++;
            }
          ?>
        </tbody>
      </table>
</div>
      </td>
    </tr>      
    <?php
    }
    else{
    ?>    
    <tr>
      <td colspan="2">No logs found on this user.</td>
    </tr>       
    <?php
    }
    ?>    
  </table>
</body>
</html>