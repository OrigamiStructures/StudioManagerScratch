<!-- Element/Edition/text.ctp -->
<?php $edition_count = isset($edition_count) ? $edition_count : 0 ; ?>
					<?= $this->Form->input("editions.$edition_count.id", ['type' => 'hidden']); ?>

					<?php
					if (!empty($edition->series_id)) {
						echo $this->Html->tag('h3', "Part of the {$edition->series->title} Series");
					}
					?>

					<?= $this->Html->tag('h2', $edition->displayTitle); ?>

					<?php
					// report on unmade pieces
					?>

					<!-- END Element/Edition/text.ctp -->
