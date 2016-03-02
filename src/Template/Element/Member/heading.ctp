
<!-- Element/Member/heading.ctp -->
<?php
$q = [
	'controller' => 'members', 
	'?' => [
		'member' => $member->id
	]];
?>
<h1>
    <?=$this->InlineTools->refineLink($q); ?>
    <?=h($member->name)?>
</h1>
<!-- END Element/Member/heading.ctp -->
