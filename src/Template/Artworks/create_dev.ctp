<!-- Template/Artwork/create.ctp -->
<?php
// set $edition_index
$edition_index = $SystemState->isKnown('edition') ?
	$artwork->indexOfRelated('editions', $SystemState->queryArg('edition')) :
	0 ;
$editions = $artwork->editions;
$edition = $artwork->editions[$edition_index];
// set $format_index
$format_index = $SystemState->isKnown('format') ?
	$edition->indexOfRelated('formats', $SystemState->queryArg('format')) :
	0 ;
$formats = $edition->formats;
$format = $edition->formats[$format_index];
$this->set(compact('editions', 'edition', 'edition_index', 'formats', 'format', 'format_index'));
?>
<section class="artwork">
	<div class="row artwork">
		<div class="columns small-12 medium-5 medium-offset-1">
            <?php
            echo $this->Form->create($artwork, ['type' => 'file']);
			/**
			 * The submit button should be inserted after the last fieldset. 
			 * 
			 * Output may be 
			 *		<fieldsets> // only on controller = artworks
			 *		<sections>
			 * Or
			 *		<sections> // on controller = editions || formats
			 *		<fieldsets>
			 * Or
			 *		<sections> // only on controller = editions
			 *		<fieldsets>
			 *		<sections>
			 */
				echo $this->element('Artwork/form_layer');
				
				if ($SystemState->controller() === 'artworks' && 
						$artwork->edition_count > 1) {
					echo $this->Form->submit();
				}
				
				echo $this->element('Edition/form_layer');
				
				if ($SystemState->controller() === 'editions' && 
						$edition->format_count > 1) {
					echo $this->Form->submit();
				}
				
				echo $this->element('Format/form_layer');
				
				if ($SystemState->is(ARTWORK_CREATE) || $SystemState->controller() === 'formats' || 
						($artwork->edition_count < 2 && $edition->format_count < 2)) {
					echo $this->Form->submit();
				}
				
            echo $this->Form->end();
            ?>
        </div>
    </div>
</section>
<?php //			osd($artwork);