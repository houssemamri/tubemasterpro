<div id="pc_notification"></div>
<?php 
if($o['enter_promocode_table']){
?>
<br>
<p><input class="" type="text" name="promo_code" id="promo_code" placeholder = "Enter Promo Code here" style="width: 200px;"> &nbsp;
<button type="button" name="p" id="submit_button" class="btn  btn-sm" value="confirm" onclick="submit_promo_code(); return false;">SUBMIT</button></p>
<br>
<?php
}
?>
<script>
$('#submit_button').addClass('disabled');

$('#promo_code').keyup(function(event){
    var Length = $("#promo_code").val().length;
    if(Length > 3){
        $('#submit_button').removeClass('disabled');
        $('#submit_button').addClass('btn-primary');
    }else{
        $('#submit_button').addClass('disabled');
        $('#submit_button').removeClass('btn-primary');
    }
});

</script>
