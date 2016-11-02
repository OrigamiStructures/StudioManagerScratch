
<!-- Element/Member/text.ctp -->
<?php
$q = [
	'controller' => 'members', 
	'?' => [
		'member' => $member->id
	]];
?>
<p>
    <?=$this->InlineTools->inlineReviewRefine($q); ?>
    <?=$this->MemberView->identifier($member); ?>
</p>
<!-- END Element/Member/text.ctp -->
