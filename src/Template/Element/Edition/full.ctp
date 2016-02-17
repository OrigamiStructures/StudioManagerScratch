			<!-- Element/Edition/full.ctp -->
			
<?php 
/**
 * Edition Helper concrete classes are called on to administer 
 * display and tool-availability rulings. This helper handles output 
 * of specific edition data. ArtStackElementHelper determines which 
 * elements to render. That's how this element was chosen.
 */
if (!is_null($edition->type)) { // CREATE doesn't know type
	$factory = $this->loadHelper('EditionFactory');
	$this->set('EditionHelper', $factory->load($edition->type));
}

/**
 * This helper choose which elements to use but has nothing to do with 
 * more detailed output of specific data.
 */
$edition_element = $ArtStackElement->choose('editionContent');
?>
			
			<section class="edition">
				<?= $this->element("$edition_element"); ?>
				
				<div class="formats">
					<?php $this->set('formats', $edition->formats); ?>
					<?= $this->element('Format/many'); ?>
				</div>
			</section>
			<!-- END Element/Edition/full.ctp -->
