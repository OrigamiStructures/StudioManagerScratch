
<!-- Element/Contact/add.ctp -->
<?php
    $label = ucfirst(\Cake\Utility\Inflector::singularize($type));
    echo $this->Form->button("Add $label", [
        'type' => 'submit', 
        'formaction' => "/members/addElement/$type",
        'class' => 'button tiny round info'
            ]);
?>
<!-- END Element/Contact/add.ctp -->
