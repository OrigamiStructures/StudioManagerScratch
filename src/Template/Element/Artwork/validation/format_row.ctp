<?php 
$links = $this->Html->link($artwork->id, ['controller' => 'pieces', '?' => ['artwork' => $artwork->id]]) .
		'/' .
		$this->Html->link($edition->id, ['controller' => 'pieces', '?' => ['edition' => $edition->id]]) .
		'/' .
		$this->Html->link($format->id, ['controller' => 'pieces', '?' => ['format' => $format->id]]);
?>
		<tr class='format'>
			<td><?= $links; ?></td>
			<td><?= $format->quantity; ?></td>
			<td><?= tf($format->hasAssigned()); ?></td>
			<td><?= $format->assigned_piece_count; ?></td>
			<td><?= tf($format->hasUnassigned()); ?></td>
			<td><?= $format->unassigned_piece_count; ?></td>
			<td><?= tf($format->hasDisposed()); ?>&nbsp</td>
			<td><?= $format->disposed_piece_count; ?></td>
			<td><?= tf($format->hasFluid()); ?></td>
			<td><?= $format->fluid_piece_count; ?></td>
			<td><?= tf($format->hasCollected()); ?></td>
			<td><?= $format->collected_piece_count; ?></td>
			<td><?= tf($format->hasSalable($edition->fluid_piece_count)); ?></td>
			<td><?= $format->salable_piece_count($edition->fluid_piece_count); ?></td>
		</tr>
