<!-- Template/Members/review.ctp -->
<?php
/**
 * set values that amend tag classes for css refinement
 */
$editing = FALSE;
if (in_array($SystemState->now(), [MEMBER_CREATE, MEMBER_REFINE])):
    $editing = TRUE;
    $element = "Member/refine";
    $url = (!empty($member->id)) 
            ? ['action' => 'refine', '?' => ['member' => $member->id]] 
            : ['action' => "create", $member->member_type];
elseif($SystemState->urlArgIsKnown('member')):
    $element = "Member/refine";
    if(!isset($member)){
        $members = $members->toArray();
        $member = array_shift($members);
    }
    $this->set('member', $member);
else:
    $element = "Member/many";
endif;
    $this->set('editing', $editing);

/**
 * Setup breadcrumbs
 */
$this->Html->addCrumb('All Members', ['action' => 'review']);
if($SystemState->urlArgIsKnown('member')){
    $this->Html->addCrumb($member->name(), ['action' => 'review', '?' => ['member' => $member->id]]);
}
if($SystemState->now() == MEMBER_REFINE){
    $this->Html->addCrumb('Edit ' . $member->name(), ['action' => 'refine', '?' => ['member' => $member->id]]);
}
if($SystemState->now() == MEMBER_CREATE){
    $this->Html->addCrumb('Create Member', ['action' => 'create']);
}
?>


<div class="members">
    <?php if ($editing){echo $this->Form->create($member, ['url' => $url]);} ?>
    <?= $this->element($element); ?>
    <?php if ($editing){ echo $this->Form->end();}?>
</div>