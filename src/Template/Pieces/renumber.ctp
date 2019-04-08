<?php
/**
 * @todo $edition is only created and stored to support the 
 *		ArtStackTools->links() method which does detailed validation 
 *		of current conditions. This is pretty questionable because it is 
 *		now clear that I've locked in the requirement for specifically 
 *		named variable to exist before the helper will run. Current 
 *		circumstances (with $providers->edition in place) show the coupling.
 */
 $edition = $providers->edition;
 /**
 * @todo $artwork will be available more directly (from ArtworkStack->stackquery() 
 *		so this wont be necessary later.
 */
$artwork = $providers->edition->artwork;
$this->set(compact('edition', 'artwork'));
$this->loadHelper('Edition');

// from Edition/text.ctp
$edition_index = isset($edition_index) ? $edition_index : 0 ; 

?>
<div class="artworks">
	
	<section class="artwork">
		<?= $this->element('Artwork/describe'); ?>

		<div class="editions">
			<section class="edition focus">
				<div class="text">
					<!--Edition/text.ctp-->
					<?= $this->Form->input("editions.$edition_index.id", 
							['type' => 'hidden', 'value' => $providers->edition->id]); ?>

					<?php
					if (!empty($providers->edition->series_id)) {
						echo $this->Html->tag('h3', "Part of the {$providers->edition->series->title} Series");
					}
					?>

					<?= $this->Html->tag('h2', 
							$this->ArtStackTools->links('edition', ['review', 'refine']) . 
							$providers->edition->displayTitle); ?>
					<!--END Edition/text.ctp-->
				</div>

				<div class="pieces">
					
					<!--Original page content-->
					<?= $this->element('Pieces/renumber_table', 
						['caption' => 'Pieces in this edition']); ?>
					<!--END Original page content-->
					<?php //echo $this->element('Edition/pieces'); ?>
				</div>
				
				<div class="formats">
					<?php //$this->set('formats', $edition->formats); ?>
					<?php //echo $this->element('Format/many'); ?>
				</div>
			</section>
		</div>
	</section>
	
</div>

