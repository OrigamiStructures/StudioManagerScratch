
<!-- Element/Group/add.ctp -->
<?php
    echo $this->Form->select("Members.Groups.{$groups_count}.id", $groups_list, [
        'empty' => TRUE
    ]);
?>
<!-- END Element/Group/add.ctp -->
