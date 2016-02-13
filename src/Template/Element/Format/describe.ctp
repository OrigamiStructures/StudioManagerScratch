<!-- Element/Format/describe.ctp -->
<?php
/**
 * THE UNIVERSAL FULL FORMAT DESCRIPTION
 * Swaps places with 'fieldset' (and a future simple version?)
 */
?>

							<div class="image">
								<?= $this->element('Format/image') ?>
							</div>
							<div class="text">
								<?= $this->element('Format/text'); ?>
							</div>
							<div class="pieces">
								<?php $piece_element = $ArtStackElement->choose('formatPieceTable'); ?>
								<?= $this->element($piece_element); ?>
							</div>
<!-- END Element/Format/describe.ctp -->
