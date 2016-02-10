			<!-- Element/Edition/full.ctp -->
			
<?php 
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
