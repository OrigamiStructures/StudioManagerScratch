<!-- Element/Contact/refine.ctp -->
<?php
if(!empty($member['contacts'])):
    $contacts = $member['contacts'];
    foreach ($contacts as $key => $contact) {
        $this->set(compact('contact', 'key'));
        echo $this->Html->tag('section', $this->element('Contact/fieldset'), ['class' => 'contact']);
    }
endif;

$this->set('type', 'contacts');
echo $this->element('Contact/add');
?>
<!-- END Element/Contact/refine.ctp -->
