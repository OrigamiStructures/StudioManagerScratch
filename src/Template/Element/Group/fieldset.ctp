<!-- Element/Member/fieldset.ctp -->
<?= $this->Form->input('groups._ids', [
    'options' => $groups, 
    'type' => 'select',
    'label' => FALSE
    ]);?>
