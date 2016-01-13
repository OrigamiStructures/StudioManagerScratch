<!-- Element/Format/form_layer.ctp -->

<?php
/**
 * May be included on any of 6 pages
 * - controller = artworks
 *		- refine
 *		- create
 * - controller = editions
 *		- refine
 *		- create
 * - controller = formats
 *		- refine
 *		- create
 */
if ($SystemState->is(ARTWORK_REFINE) && !$SystemState->isKnown('format')) {
	// edition_id is assumed known
	if ($artwork->editions[$edition_index]->format_count > 1) {
		// wrap and add a header?
		echo $this->element('Format/many');
	} else {
		// format_index 0
		echo $this->element('Format/fieldset');
	}
} elseif ($SystemState->is(ARTWORK_REFINE) && $SystemState->isKnown('format')) {
	// edition_id is assumed known
	// format_index = $artwork->edtions[$edition_index]->indexOfRelated('formats', $edition_id)
	echo $this->element('Format/fieldset');
} elseif ($SystemState->is(ARTWORK_CREATE)) {
	// format_index = 0
	echo $this->element('Format/fieldset');
}

?>

<!-- END Element/Format/form_layer.ctp -->
