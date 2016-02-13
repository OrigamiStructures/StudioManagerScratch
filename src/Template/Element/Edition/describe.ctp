<!-- Element/Edition/describe.ctp -->
<?php
/**
 * THE UNIVERSAL FULL EDITION DESCRIPTION
 * Swaps places with 'fieldset' (and a future simple version?)
 */
?>

	<div class="text">
		<?= $this->element('Edition/text'); ?>
	</div>

	<div class="pieces">
		<?php $piece_element = $ArtStackElement->choosePieceTable($edition); ?>
		<?= $this->element($piece_element); ?>
	</div>

<!-- END Element/Edition/describe.ctp -->
