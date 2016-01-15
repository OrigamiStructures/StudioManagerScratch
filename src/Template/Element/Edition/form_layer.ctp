<!-- Element/Edition/form_layer.ctp -->

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
if ($SystemState->controller() === 'formats') {
	echo $this->element('Edition/describe');
} elseif ($SystemState->is(ARTWORK_REFINE) && !$SystemState->isKnown('edition')) {
	// edition_id is assumed known
	if ($artwork->edition_count > 1) {
		
		
		/**
		 * THE DOM IN THIS SECTION IS A BAD HACK
		 */
		$this->set('editions', $artwork->editions);
		echo $this->Html->tag('section', $this->element('Edition/many'), ['class' => 'editions']);
//		echo $this->element('Edition/many');
		
		
	} else {
		// format_index 0
		echo $this->element('Edition/fieldset');
	}
} elseif ($SystemState->is(ARTWORK_REFINE) && $SystemState->isKnown('edition')) {
	// edition_index = $artwork->indexOfRelated('editions', $edition_id)
	echo $this->element('Edition/fieldset');
} elseif ($SystemState->is(ARTWORK_CREATE)) {
	// edition_index = 0
	echo $this->element('edition/fieldset');
}

?>

<!-- END Element/Edition/form_layer.ctp -->
