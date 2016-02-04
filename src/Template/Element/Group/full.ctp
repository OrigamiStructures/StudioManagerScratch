
<!-- Element/Group/full.ctp -->
<?php
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
        foreach ($groups as $key => $group) {
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
