<!-- Element/Address/display.ctp -->
<?php
$csv = !empty($address->city) ? 
        '<p>' . h("$address->city, $address->state $address->zip") . '</p>' :
        "";
?>
<h3><?= h(ucwords($address->label)); ?></h3>
<?= empty($address->address1) ? '' : '<p>' . h($address->address1) . '</p>'; ?>
<?= empty($address->address2) ? '' : '<p>' . h($address->address2) . '</p>'; ?>
<?= empty($address->address3) ? '' : '<p>' . h($address->address3) . '</p>'; ?>
<?= $csv ?>
<!-- END Element/Address/display.ctp -->
