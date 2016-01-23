<?php
?>
<table>
	<caption>
		<?= $edition->display_title; ?>
	</caption>
	<thead>
		<tr>
			<th>Quantity</th>
			<th>hasAssigned</th>
			<th>Assigned</th>
			<th>hasUnassigned</th>
			<th>Unassigned</th>
			<th>hasDisposed</th>
			<th>Disposed</th>
			<th>hasFluid</th>
			<th>Fluid</th>
			<th>hasCollected</th>
			<th>Collected</th>
			<th>hasSalable</th>
			<th>Salable</th>
		</tr>
	</thead>
	<tbody>
		<tr class='edition'>
			<td><?= $edition->quantity; ?></td>
			<td><?= tf($edition->hasAssigned()); ?></td>
			<td><?= $edition->assigned_piece_count; ?></td>
			<td><?= tf($edition->hasUnassigned()); ?></td>
			<td><?= $edition->unassigned_piece_count; ?></td>
			<td><?= tf($edition->hasDisposed()); ?>&nbsp</td>
			<td><?= $edition->disposed_piece_count; ?></td>
			<td><?= tf($edition->hasFluid()); ?></td>
			<td><?= $edition->fluid_piece_count; ?></td>
			<td><?= tf($edition->hasCollected()); ?></td>
			<td><?= $edition->collected_piece_count; ?></td>
			<td><?= tf($edition->hasSalable()); ?></td>
			<td><?= $edition->salable_piece_count; ?></td>
		</tr>
		
<?php 
foreach($edition->formats as $format) {
	$this->set('format', $format);
	echo $this->element('Artwork/validation/format_row');
}
?>
		
	</tbody>
</table>