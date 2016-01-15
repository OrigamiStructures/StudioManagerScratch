<!-- Element/Artwork/form_layer.ctp -->

<?php
/**
 * May be included on any of 6 pages
 * - controller = artworks
 *		- refine
 *		- create
 * Past this point, display is shown
 * - controller = editions
 *		- refine
 *		- create
 * - controller = formats
 *		- refine
 *		- create
 */

if ($SystemState->controller() !== 'artworks') {
	echo $this->element('Artwork/describe');
} else {
	echo $this->element('Artwork/fieldset');
}
?>
<!-- END Element/Artwork/form_layer.ctp -->
