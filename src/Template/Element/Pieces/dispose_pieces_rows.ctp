<!-- Element/Pieces/dispose_pieces_rows.ctp -->
<?php
/**
 * $providers is believed to be the Edition entities generated from 
 * the EditionStackComponent->stackQuery() - 8/2016
 */
$owners = new \Cake\Collection\Collection($providers);
$owner_title = $owners->reduce(function($accumulator, $owner) {
	$accumulator[$owner->key()] = $owner->display_title;
	return $accumulator;
}, []);

foreach($pieces as $piece) :
	osd($piece);//die;
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
		<?php // osd($piece->id);?>
		<td class="tools"><?= $standing_disposition ? $this->DispositionTools->connect($piece) : ''; ?></td>
	</tr>
	<?= $this->element('Pieces/disposition_event_rows'); ?>
<?php
endforeach;
?>
<!-- END Element/Pieces/dispose_pieces_rows.ctp -->
