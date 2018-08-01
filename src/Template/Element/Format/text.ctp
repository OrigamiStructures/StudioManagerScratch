							<!-- Element/Format/text.ctp -->
<?php
/**
 * $artwork, $edition and $format are assumed to have been set by an upstream process
 * 
 */
?>
							<?= $this->Form->input(
									"editions.$edition_index.formats.$format_index.id", [
										'type' => 'hidden', 'value' => $format->id]); ?>

							<?= $this->Html->tag('p', 
								"{$this->ArtStackTools->links('format', ['refine', 'remove'])}" .
								"$format->displayTitle", 
								['class' => 'format']); ?>

							<?= $this->Html->tag('p', $format->description); ?>
							
							<section class="disposition">
								<?= $this->EditionFactory->concrete($edition->type)->
									pieceSummary($format, $edition) ?>
								<?= $this->EditionFactory->concrete($edition->type)->
									pieceTools($format, $edition) ?>
							</section>
							<!-- END Element/Format/text.ctp -->
 