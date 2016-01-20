<!-- Element/Edition/text.ctp -->
<?php
$edition_index = isset($edition_index) ? $edition_index : 0 ; 
$this->helper = $this->Factory->load($edition->type);
?>
					<?= $this->Form->input("editions.$edition_index.id", ['type' => 'hidden']); ?>

					<?php
					if (!empty($edition->series_id)) {
						echo $this->Html->tag('h3', "Part of the {$edition->series->title} Series");
					}
					?>

					<?= $this->Html->tag('h2', $edition->displayTitle); ?>
					<?= $this->helper->editionPieceSummary($edition); ?>
					<?= $this->helper->editionPieceTools($edition); ?>
<!-- END Element/Edition/text.ctp -->
