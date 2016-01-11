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
					$pieces = isset($edition->pieces) ? count($edition->pieces) : 0;
					$piece_lable = $pieces === 1 ? 'piece' : 'pieces';
					if ($pieces === 0) {
						echo $this->Html->tag('p', "There are $pieces $piece_lable for this edition");
					} else {
						echo $this->Html->link("Details about the $pieces $piece_lable in this edition",
								['controller' => 'pieces', 'action' => 'review', '?' => [
									'artwork' => $artwork->id,
									'edition' => $edition->id,
								]]);
					}
					// report on unmade pieces
					?>

					<!-- END Element/Edition/text.ctp -->
