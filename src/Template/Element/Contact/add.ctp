
<!-- Element/Contact/add.ctp -->
<?php
    $label = ucfirst(\Cake\Utility\Inflector::singularize($type));
    echo $this->Form->button("Add $label", [
        'type' => 'submit', 
        'formaction' => "/members/addElement/$type",
        'class' => 'button tiny round info'
            ]);
//    echo $this->Html->link("Add $label", ['action' => 'addElement', $type, '?' => ['member' => $SystemState->queryArg('member'), 'url' => urlencode($referrer)]]);
?>
<!-- END Element/Contact/add.ctp -->
