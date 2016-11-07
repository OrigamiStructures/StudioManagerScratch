<!-- Element/Pieces/overview_rows.ctp -->
<?php
$owners = new \Cake\Collection\Collection($providers);
$owner_title = $owners->reduce(function($accumulator, $owner) {
	$accumulator[$owner->key()] = $owner->display_title;
	return $accumulator;
}, []);

foreach($pieces as $piece) :
	if ((boolean) $piece->disposition_count) {
//		osd($piece);
//		die;
	}
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
		<td> <!-- <?= $owner_title[$piece->key()] ?></td>
		<td><?= (boolean) $piece->disposition_count ? $piece->disposition_count . ' events' : '-'; ?></td>
		<td><?= $piece->collected ? 'Yes' : '-'; ?> -->
			<?= $this->element('Disposition/disposition_event_descriptions', ['piece' => $piece]); ?>
		</td>
	</tr>
<?php
endforeach;
?>
<!-- END Element/Pieces/overview_rows.ctp -->
