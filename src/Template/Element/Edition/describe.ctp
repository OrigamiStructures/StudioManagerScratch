<!-- Element/Edition/describe.ctp -->
<?php
/**
 * THE UNIVERSAL FULL EDITION DESCRIPTION
 * Swaps places with 'fieldset' (and a future simple version?)
 * 
 * Edition Helper concrete classes are called on to administer 
 * display and tool-availability rulings. This helper handles output 
 * of specific edition data. ArtStackElementHelper determines which 
 * elements to render. That's how this element was chosen.
 */
$factory = $this->loadHelper('EditionFactory');
$this->set('EditionHelper', $factory->load($edition->type));
?>

	<div class="text">
		<?= $this->element('Edition/text'); ?>
	</div>

	<div class="pieces">
		<?= $this->element('Edition/pieces'); ?>
	</div>

<!-- END Element/Edition/describe.ctp -->
