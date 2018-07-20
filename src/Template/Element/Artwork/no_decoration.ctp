<?php
/**
 * One of three content wrapper versions. 
 * 
 * form, createunique, and no _decoration.ctp place nothing or a form 
 * around the page element. ArtStackElement::contentDecorationRule() chooses.
 * 
 * 'action' attribute is the one difference between the two form versions.
 */
//$artworks_element = $this->ArtElement->choose('artworksContent');
//$artworks_element = $elements[ARTWORK_LAYER]($artwork);
?>

	<!-- Template/Artwork/no_decoration.ctp -->
	
	<?= $this->element($elements[ARTWORK_LAYER]($artwork));?>
	
	<!-- END Template/Artwork/no_decoration.ctp -->
