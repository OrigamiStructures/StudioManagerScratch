<!-- Element/Edition/text.ctp -->
<?php
/**
 * $edition is assumed to have been set by an upstream process
 * 
 * All form inputs are Artwork rooted, so $edition_index is required. 
 * If it was not set in an upstream process, it is assumed to be 0.
 * ($edition could be 'assumed' too I suppose)
 * 
 * Edition Helper concrete classes are called on to administer 
 * display and tool-availability rulings.
 */
$edition_index = isset($edition_index) ? $edition_index : 0 ; 
$factory = $this->loadHelper('EditionFactory');
$helper = $factory->load($edition->type);
?>
					<?= $this->Form->input("editions.$edition_index.id", ['type' => 'hidden']); ?>

					<?php
					if (!empty($edition->series_id)) {
						echo $this->Html->tag('h3', "Part of the {$edition->series->title} Series");
					}
					?>

					<?= $this->Html->tag('h2', $edition->displayTitle); ?>
					<?= $helper->pieceSummary($edition); ?>
					<?= $helper->pieceTools($edition); ?>
<!-- END Element/Edition/text.ctp -->
