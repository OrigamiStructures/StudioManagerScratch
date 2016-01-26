
<!-- Element/Contact/add.ctp -->
<?php
    $label = ucfirst(\Cake\Utility\Inflector::singularize($type));
    echo $this->Html->link("Add $label", ['action' => 'addElement', $type]);
?>
<!-- END Element/Contact/add.ctp -->
