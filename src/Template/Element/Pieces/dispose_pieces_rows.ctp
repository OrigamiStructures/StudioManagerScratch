<!-- Element/Edition/dispose_pieces_rows.ctp -->
<?php
$owners = new \Cake\Collection\Collection($providers);
$owner_title = $owners->reduce(function($accumulator, $owner) {
	$accumulator[$owner->key()] = $owner->display_title;
	return $accumulator;
}, []);

foreach($pieces as $piece) :
?>
	<tr>
		<?php 
		if (in_array($edition->type, \App\Lib\SystemState::limitedEditionTypes())) : ;
		?>
		<td><?= $piece->number; ?></td>
		<?php 
		 endif;
		 ?>
		<td><?= $piece->quantity; ?></td>
		<td><?= $owner_title[$piece->key()] ?></td>
		<td><?= (boolean) $piece->disposition_count ? $piece->disposition_count . ' events' : '-'; ?></td>
		<td><?= $piece->collected ? 'Yes' : '-'; ?></td>
		<td><?= $this->Html->link(
				'Add to ' . (empty($standing_disposition->label) ? 'disposition' : $standing_disposition->label) , [
					'controller' => 'dispositions',
					'action' => 'create',
					'?' => $SystemState->queryArg() + ['piece' => $piece->id]
				]); ?></td>
	</tr>
<?php
endforeach;
?>
<!-- END Element/Edition/dispose_pieces_rows.ctp -->
