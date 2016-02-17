<!-- Element/Group/full.ctp -->
<?php
$groups_count = count($groups);
$this->set('groups_count', $groups_count);
if($editing){
    echo $this->element('Group/add');
}

if(!empty($groups)):
    echo $this->Html->tag('h4', 'Groups');
    foreach ($groups as $key => $group) {
    //            osd($group, 'single group');
        $this->set('group', $group);
        echo $this->element('Group/display');
    }
endif;
?>
<!-- END Element/Group/full.ctp -->
