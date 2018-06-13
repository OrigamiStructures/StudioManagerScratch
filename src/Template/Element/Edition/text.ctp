<?php
$q = [
	'controller' => 'editions', 
	'?' => [
		'artwork' => $artwork->id,
		'edition' => $edition->id,
	]];
$l = $this->InlineTools->inlineReviewRefine($q);
?>
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
					<?= $this->Form->input("editions.$edition_index.id", ['type' => 'hidden', 'value' => $edition->id]); ?>

					<?php
					if (!empty($edition->series_id)) {
						echo $this->Html->tag('h3', "Part of the {$edition->series->title} Series");
					}
					?>

					<?= $this->Html->tag('h2', "{$l}$edition->displayTitle"); ?>
					<section class="assignment">
<?php if ($this->SystemState->hasFocus('edition', $edition->id)) : ?>
						<?= $this->EditionFactory->concrete($edition->type)->pieceSummary($edition); ?>
<?php endif; ?>
						<?= $this->EditionFactory->concrete($edition->type)->pieceTools($edition); ?>
					</section>
<!-- END Element/Edition/text.ctp -->
