<?php use Cake\Cache\Cache; ?>
<!-- Element/Address/full.ctp -->
<h2>Addresses</h2>

<?php
foreach ($member->addresses as $key => $address) :
    $this->set('address', $address);
?>
    <?= $this->element('Address/display'); ?>

<?php endforeach; ?>
<!-- END Element/Address/full.ctp -->
