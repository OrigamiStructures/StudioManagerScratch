<!-- Element/Contact/refine.ctp -->
<section class="row contact">
<?php
if(!empty($member['contacts'])): 
    $contacts = $member['contacts'];
    foreach ($contacts as $key => $contact) {
        $this->set(compact('contact', 'key'));
        echo $this->Html->div('contactFieldset', $this->element('Contact/fieldset'));
    }
endif;

$this->set('type', 'contacts');
echo $this->element('Contact/add');
?>
</section>
<!-- END Element/Contact/refine.ctp -->
