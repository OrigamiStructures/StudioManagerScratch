			<!-- Element/Edition/full.ctp -->
			
<?php 
/**
 * EditionFactory concrete classes are called on to administer 
 * display and tool-availability rulings. This helper handles output 
 * of specific edition data. LayersComponent determines which 
 * elements to render. That's how this element was chosen.
 */
?>
			
			<section class="edition">
				<?= $this->element($elements[EDITION_LAYER]($edition)); ?>
				
				<div class="formats">
					<?php $this->set('formats', $edition->formats); ?>
					<?= $this->element('Format/many'); ?>
				</div>
			</section>
			<!-- END Element/Edition/full.ctp -->
