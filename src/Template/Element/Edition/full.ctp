			<!-- Element/Edition/full.ctp -->
			
<?php 
/**
 * EditionFactory concrete classes are called on to administer 
 * display and tool-availability rulings. This helper handles output 
 * of specific edition data. ArtStackElementHelper determines which 
 * elements to render. That's how this element was chosen.
 */

/**
 * This helper chooses which elements to use but has nothing to do with 
 * more detailed output of specific data.
 */
//$edition_element = $this->ArtElement->choose('editionContent');
//$edition_element = $elements[EDITION_LAYER];

?>
			
			<section class="edition">
				<?= $this->element($elements[EDITION_LAYER]($edition)); ?>
				
				<div class="formats">
					<?php $this->set('formats', $edition->formats); ?>
					<?= $this->element('Format/many'); ?>
				</div>
			</section>
			<!-- END Element/Edition/full.ctp -->
