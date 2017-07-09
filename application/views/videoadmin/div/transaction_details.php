<label for="name">Transaction Status: <span class="label label-success"><?=$pp['batch_header']->batch_status;?> </span></label> </label> <br>
<label for="name">Time Created: </label> <?=$pp['batch_header']->time_created;?> </label> <br> 
<label for="name">Time Completed: </label> <?=$pp['batch_header']->time_completed;?> </label> <br> 
<hr noshade>
<label for="name">Amount: <?php  echo $pp['batch_header']->amount->value . " " . $pp['batch_header']->amount->currency;?> </label> <br>
<label for="name">Fees: <?php  echo $pp['batch_header']->fees->value . " " . $pp['batch_header']->fees->currency;?> </label> <br>
<label for="name">Total paid to user: <? echo number_format($pp['batch_header']->amount->value + $pp['batch_header']->fees->value,2) . " " . $pp['batch_header']->amount->currency;?> </label> <br>