<html>
<body>
    <h1>Hi <?php echo $first_name;?></h1>
    <p>Please click the following link to complete your account setup with us</p>
    <p><?php echo anchor('auth/activate/'. $id .'/'. $activation, lang('email_activate_link'));?></p>
    <p>TubeMasterPro Team</p>
</body>
</html>