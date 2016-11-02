
<!-- Element/Contact/full.ctp -->
<?php
echo $this->Html->tag('h2', 'Contacts');
foreach ($member->contacts as $key => $contact) {
    $this->set('contact', $contact);
    echo $this->element('Contact/display');
}
?>
<!-- END Element/Contact/full.ctp -->
