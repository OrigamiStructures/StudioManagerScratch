<!-- Template/Artwork/complete_review.ctp -->
<?php $artworks_element = $this->ArtElement->choose('artworksContent'); ?>
<div class="artworks">
<?php
if (!isset($artworks)) {
	$artworks = [$artwork];
}
// https://github.com/OrigamiStructures/StudioManagerScratch/issues/70
foreach ($artworks as $artwork_index => $artwork) :
	$this->set(compact('artwork_index', 'artwork'));
	$artwork_element = $this->ArtElement->choose('artworkContent');
?>
	<section class="artwork">
		<?= $this->element($artwork_element) ?>
		<div class="editions">
<?php
foreach ($artwork->editions as $edition_index => $edition) :
	$this->set(compact('edition_index', 'edition'));
	$edition_element = $this->ArtElement->choose('editionContent');
?>
			<section class="edition">
				<?= $this->element("$edition_element"); ?>
				<div class="formats">
<?php
foreach ($edition->formats as $format_index => $format) :
	$this->set(compact('format_index', 'format'));
	$format_element = $this->ArtElement->choose('formatContent');
	 /* make an isFocus() method for this, edition and artwork? */
    $formatId = \Cake\Utility\Hash::get($this->request->getQueryParams(), 'format', FALSE);
	$class = $formatId ? ' focus' : '';
?>
					<section class="format<?= $class; ?>">
						<?= $this->element($format_element); ?>
					</section> <!-- END section format -->
<?php endforeach; // end foreach formats ?>
				</div> <!-- END div formats -->
			</section> <!-- END section edition -->
<?php endforeach; // end foreach editions ?>
		</div> <!-- END div editions -->
	</section>
<?php endforeach; // end foreach artworks?>
</div> <!-- END div artworks -->
<?php
