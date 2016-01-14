<!-- Element/Contact/revise.ctp -->
<?php
    $contacts = $member['contacts'];
    foreach ($contacts as $key => $contact) {
        $this->set('contact', $contact);
        $this->set('contactKey', $key);
//            echo $this->Html->div('addressDisplay', $this->element('Address/display'), ['display' => 'hidden']);
        echo $this->Html->div('contactFieldset', $this->element('Contact/fieldset'));
    }
?>
<!-- END Element/Contact/revise.ctp -->
