
<!-- Element/Address/full.ctp -->
<?php
echo $this->Html->tag('h4', 'Addresses');
foreach ($member->addresses as $key => $address) {
    $this->set('address', $address);
    echo $this->element('Address/display');
}
?>
<!-- END Element/Address/full.ctp -->
