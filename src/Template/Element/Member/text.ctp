
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
    <?=h($member->name); ?>
</p>
<!-- END Element/Member/text.ctp -->
