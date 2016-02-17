<!-- Element/Group/full.ctp -->
<?php
$groups_count = count($member->groups);
$this->set('groups_count', $groups_count);
if($editing){
    echo $this->element('Group/add');
}

if(!empty($member->groups)):
    echo $this->Html->tag('h4', 'Groups');
    foreach ($member->groups as $key => $group) {
        $this->set('group', $group);
        echo $this->element('Group/display');
    }
endif;
?>
<!-- END Element/Group/full.ctp -->
