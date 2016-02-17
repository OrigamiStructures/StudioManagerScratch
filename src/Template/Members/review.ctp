<!-- Template/Member/review.ctp -->
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
elseif($SystemState->isKnown('member')):
    $element = "Member/refine";
    $members = $members->toArray();
    $member = array_shift($members);
    $this->set('member', $member);
else:
    $element = "Member/many";
endif;
    $this->set('editing', $editing);
?>

<div class="members">
    <?php if ($editing){echo $this->Form->create($member, ['url' => $url]);}?>
    <?= $this->element($element); ?>
    <?php if ($editing){ echo $this->Form->end();}?>
</div>