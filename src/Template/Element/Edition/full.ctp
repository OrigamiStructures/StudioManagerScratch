			<!-- Element/Edition/full.ctp -->
			
<?php 
/**
 * Edition Helper concrete classes are called on to administer 
 * display and tool-availability rulings. This helper handles output 
 * of specific edition data. ArtStackElementHelper determines which 
 * elements to render. That's how this element was chosen.
 */
/**
 * The helper needs to change so it is composed with all the 
 * specific Edition type helpers because we are in a loop and 
 * don't want to keep swapping like this. What to call the 
 * master helper though (EditionElement? like ArtElement). 
 * Calls something like this:
 * <code>
 * $this->EditionElement->numbered->choose('editionContent);
 * </code>
 * where numbered is a property containg the concrete helper class.
 * 
 * But we're abstracted in a loop, so:
 * <code>
 * $this->EditionElement->{$edition->type}->choose('editionContent');
 * //or
 * $this->EditionElement->choose($edition->type, 'editionContent');
 * </code>
 * 
 */
if (!is_null($edition->type)) { // CREATE doesn't know type
	$factory = $this->loadHelper('EditionFactory');
	$this->set('EditionHelper', $factory->load($edition->type));
}

/**
 * This helper choose which elements to use but has nothing to do with 
 * more detailed output of specific data.
 */
$edition_element = $this->ArtElement->choose('editionContent');
?>
			
			<section class="edition">
				<?= $this->element("$edition_element"); ?>
				
				<div class="formats">
					<?php $this->set('formats', $edition->formats); ?>
					<?= $this->element('Format/many'); ?>
				</div>
			</section>
			<!-- END Element/Edition/full.ctp -->
