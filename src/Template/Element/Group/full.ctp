
<!-- Element/Group/full.ctp -->
<?php
//osd($groups, 'groups');
$groups_count = count($groups);
$this->set('groups_count', $groups_count);
if(in_array($SystemState->now(), [MEMBER_CREATE, MEMBER_REFINE])){
    echo $this->element('Group/add');
}

if(!empty($groups)):
?>
<section class="group">
    <div class="columns small-12 medium-9 description">
        <?php
        echo $this->Html->tag('h4', 'Groups');
        foreach ($groups as $key => $group) {
//            osd($group, 'single group');
            $this->set('group', $group);
            echo $this->element('Group/display');
        }
        ?>
    </div>
</section>
<?php
endif;
?>
<!-- END Element/Group/full.ctp -->
