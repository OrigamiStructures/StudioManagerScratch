<?php 
	if (isset($piece->disposition_count) && (boolean) $piece->disposition_count) : 

	$q = [
		'controller' => 'dispositions', 
		'?' => [
			'disposition' => NULL
		]];

?>
<!-- Element/Pieces/disposition_event_rows.ctp -->
<?php 
	$dispositions = $piece->dispositions;

	foreach($dispositions as $disposition) : 
		$q['?'] = ['disposition' => $disposition->id];
		$l = $this->InlineTools->inlineReviewRefineDelete($q);
?>
	<p class="disposition-detail" style="width: 100%;">
		<span><?= $l; ?></span>
		<?= $disposition->label . ' ' . $disposition->name . ' ' . $disposition->member_name; ?>
		<span style="float: right;"><?= $disposition->complete ? '' : 'Open' ?></span>
	</p>
<?php endforeach; ?>
<!-- END Element/Pieces/disposition_event_rows.ctp -->
<?php endif; ?>
