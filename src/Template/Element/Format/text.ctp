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
<!-- Element/Format/text.ctp -->
<?php
/**
 * $artwork, $edition and $format are assumed to have been set by an upstream process
 * 
 * Edition Helper concrete classes are called on to administer 
 * display and tool-availability rulings.
 */
$factory = $this->loadHelper('EditionFactory');
$helper = $factory->load($edition->type);
?>
							<?= $this->Form->input($format->id, ['type' => 'hidden']); ?>

							<?= $this->Html->tag('p', "{$l}$format->displayTitle", ['class' => 'format']); ?>

							<?= $this->Html->tag('p', $format->description); ?>
							
							<section class="disposition">
								<?php $format->potential_pieces = $edition->unassigned_piece_count; ?>
								<?= $helper->pieceSummary($format, $edition) ?>
								<?= $helper->pieceTools($format, $edition) ?>
							</section>
							<!-- END Element/Format/text.ctp -->
 