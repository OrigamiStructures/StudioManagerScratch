							<!-- Element/Format/text.ctp -->
<?php
$q = [
	'controller' => 'formats', 
//	'action' => 'review', 
	'?' => [
		'artwork' => $artwork->id,
		'edition' => $edition->id,
		'format' => $format->id,
	]];
$nav = $this->Html->link('v', $q + ['action' => 'review']);
$ed = $this->Html->link('f', $q + ['action' => 'refine']);
$l = "<span class='nav'>[$nav|$ed] </span>";
?>
<?php
/**
 * $artwork, $edition and $format are assumed to have been set by an upstream process
 * 
 */
?>
							<?= $this->Form->input("editions.$edition_index.formats.$format_index.id", ['type' => 'hidden', 'value' => $format->id]); ?>

							<?= $this->Html->tag('p', "{$l}$format->displayTitle", ['class' => 'format']); ?>

							<?= $this->Html->tag('p', $format->description); ?>
							
							<section class="disposition">
								<?php $format->potential_pieces = $edition->unassigned_piece_count; ?>
								<?= $EditionHelper->pieceSummary($format, $edition) ?>
								<?= $EditionHelper->pieceTools($format, $edition) ?>
							</section>
							<!-- END Element/Format/text.ctp -->
 