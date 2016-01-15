<!-- Element/Address/revise.ctp -->
<?php
    $addresses = $member['addresses'];
    foreach ($addresses as $key => $address) {
        $this->set('address', $address);
        $this->set('addressKey', $key);
//            echo $this->Html->div('addressDisplay', $this->element('Address/display'), ['display' => 'hidden']);
        echo $this->Html->div('addressFieldset', $this->element('Address/fieldset'));
    }
?>
<!-- END Element/Address/revise.ctp -->
