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
	<tr class="disposition-detail">
		<td><?= $l; ?></td>
		<td colspan="2"><?= $disposition->label . ' ' . $disposition->name; ?></td>
		<td colspan="2"><?= $disposition->member_name; ?></td>
	</tr>
<?php endforeach; ?>
<!-- END Element/Pieces/disposition_event_rows.ctp -->
<?php endif; ?>
