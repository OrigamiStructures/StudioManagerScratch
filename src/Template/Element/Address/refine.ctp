<!-- Element/Address/refine.ctp -->
<?php
if(!empty($member['addresses'])):
    $addresses = $member['addresses'];
    foreach ($addresses as $key => $address) {
        $this->set(compact('address', 'key'));
        echo $this->Html->tag('section', $this->element('Address/fieldset'), ['class' => 'address']);
    }
endif;

$this->set('type', 'addresses');
echo $this->element('Contact/add');
?>
<!-- END Element/Address/refine.ctp -->
