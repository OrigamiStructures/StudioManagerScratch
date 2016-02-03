<!-- Element/Address/display.ctp -->
<?php
$csv = !empty($address->city) ? 
        h("$address->city, $address->state $address->zip") :
        "";
?>
<h5><?= h($address->label); ?></h5>
<p><?= h($address->address1); ?></p>
<p><?= h($address->address2); ?></p>
<p><?= h($address->address3); ?></p>
<p><?= $csv ?></p>
<!-- END Element/Address/display.ctp -->
