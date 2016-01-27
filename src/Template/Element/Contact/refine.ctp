<!-- Element/Contact/refine.ctp -->
<section class="row contact">
<?php
    $contacts = $member['contacts'];
    foreach ($contacts as $key => $contact) {
        $this->set(compact('contact', 'key'));
        echo $this->Html->div('contactFieldset', $this->element('Contact/fieldset'));
    }
    $this->set('type', 'contacts');
    echo $this->element('Contact/add');
?>
</section>
<!-- END Element/Contact/refine.ctp -->
