<!-- Element/Edition/text.ctp -->
<?php
/**
 * $edition is assumed to have been set by an upstream process
 * 
 * All form inputs are Artwork rooted, so $edition_index is required. 
 * If it was not set in an upstream process, it is assumed to be 0.
 * ($edition could be 'assumed' too I suppose)
 */
$edition_index = isset($edition_index) ? $edition_index : 0 ; 
?>
					<?= $this->Form->input("editions.$edition_index.id", [
						'type' => 'hidden', 'value' => $edition->id]); ?>

					<?php
					if (!empty($edition->series_id)) {
						echo $this->Html->tag('h3', "Part of the {$edition->series->title} Series");
					}
					?>

					<?= $this->Html->tag('h2', 
						$this->ArtStackTools->links('edition', ['refine', 'remove']) . 
						"$edition->displayTitle"); ?>
					<section class="assignment">
						<?= $this->EditionFactory->concrete($edition->type)->pieceSummary($edition); ?>
						<?= $this->EditionFactory->concrete($edition->type)->pieceTools($edition); ?>
					</section>
<!-- END Element/Edition/text.ctp -->
