<?php
/**
 * One of three content wrapper versions. 
 * 
 * form, createunique, and no _decoration.ctp place nothing or a form 
 * around the page element. ArtStackElement::contentDecorationRule() chooses.
 * 
 * 'action' attribute is the one difference between the two form versions.
 */
$artworks_element = $this->ArtElement->choose('artworksContent');
?>

	<!-- Template/Artwork/form_decoration.ctp -->
	<?=  $this->Form->create($artwork, 
		['type' => 'file',  'class' => 'droppzone',  'id' => 'artwork_stack']); ?>
	
		<?= $this->element($artworks_element);?>
	
	<?= $this->Form->end(); ?>
	<!-- END Template/Artwork/form_decoration.ctp -->
