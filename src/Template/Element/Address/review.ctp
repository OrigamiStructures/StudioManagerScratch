
<!-- Element/Address/full.ctp -->
<h4>Addresses</h4>

<?php
foreach ($member->addresses as $key => $address) :
    $this->set('address', $address);
?>
    <?= $this->element('Address/display'); ?>
	<?= $standing_disposition ? $this->DispositionTools->connect($address) : ''; ?>

<?php endforeach; ?>
<!-- END Element/Address/full.ctp -->
