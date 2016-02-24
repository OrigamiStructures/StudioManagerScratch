<!-- Element/Group/fieldset.ctp -->
<?= $this->Form->input("groups.$key.id", [
    'options' => $groups, 
    'type' => 'select',
    'label' => FALSE,
    'empty' => 'Choose a group'
    ]);?>

<!-- END Element/Group/fieldset.ctp -->
