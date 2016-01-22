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

							<?= $this->Html->tag('p', $format->displayTitle, ['class' => 'format']); ?>

							<?php
							if (!empty($format->title)) {
								echo $this->Html->tag('p', $format->description);
							}
							?>
							
							<section class="disposition">
								<?php $format->potential_pieces = $edition->unassigned_piece_count; ?>
								<?= $helper->pieceSummary($format, $edition) ?>
								<?= $helper->pieceTools($format, $edition) ?>
							</section>
							<?php
							/**
							 * ============================================================
							 * This retains piece assignment reporting AND dispostion tools
							 */
							
//							$piece_lable = $format->assigned_piece_count === 1 ? 'piece' : 'pieces';
//							if ($format->assigned_piece_count === 0) {
//								echo $this->Html->tag('p', "There no pieces assigned to this format || tool link? ||");
//							} else {
//								echo $this->Html->link("Details about the $format->assigned_piece_count $piece_lable assigned to this format",
//									['controller' => 'pieces', 'action' => 'review', '?' => [
//											'artwork' => $artwork->id,
//											'edition' => $edition->id,
//											'format' => $format->id
//										]]);
//							}
							// also provide information about pieces, 
							// including range, and subscriptions
							?>

							<!-- END Element/Format/text.ctp -->
