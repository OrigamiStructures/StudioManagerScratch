<!-- Element/Contact/revise.ctp -->
<?php
    $contacts = $member['contacts'];
    $count = count($contacts);
    foreach ($contacts as $key => $contact) {
        $this->set(compact('contact', 'key', 'count'));
        echo $this->Html->div('contactFieldset', $this->element('Contact/fieldset'));
    }
?>
<!-- END Element/Contact/revise.ctp -->
