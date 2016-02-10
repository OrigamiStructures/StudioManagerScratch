<!-- Element/Address/refine.ctp -->
<section class="row address">
<?php
if(!empty($member['addresses'])):
    $addresses = $member['addresses'];
    foreach ($addresses as $key => $address) {
        $this->set('address', $address);
        $this->set('addressKey', $key);
        echo $this->Html->div('addressFieldset', $this->element('Address/fieldset'));
    }
endif;

$this->set('type', 'addresses');
echo $this->element('Contact/add');
?>
</section>
<!-- END Element/Address/refine.ctp -->
