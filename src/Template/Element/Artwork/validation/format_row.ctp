<?php 
$links = $this->Html->link($artwork->id, ['controller' => 'pieces', '?' => ['artwork' => $artwork->id]]) .
		'/' .
		$this->Html->link($edition->id, ['controller' => 'pieces', '?' => ['artwork' => $artwork->id, 'edition' => $edition->id]]) .
		'/' .
		$this->Html->link($format->id, ['controller' => 'pieces', '?' => ['artwork' => $artwork->id, 'edition' => $edition->id, 'format' => $format->id]]);
?>
		<tr class='format'>
			<td><?= 'Format '.$links; ?></td>
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
			<td><?= tf($format->hasSalable($edition->undisposed_piece_count)); ?></td>
			<td><?= $format->salable_piece_count($edition->undisposed_piece_count); ?></td>
		</tr>
		<?php 
			if ($format->hasAssigned()) : 
				$this->set('pieces', $format->pieces);
		?>
		<tr>
			<td>Fluid Pieces</td>
			<td colspan="13">
				<?= $this->element('Artwork/validation/piece_table'); ?>
			</td>
		</tr>
		<?php endif; ?>
