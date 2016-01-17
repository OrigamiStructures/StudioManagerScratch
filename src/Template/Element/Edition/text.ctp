<!-- Element/Edition/text.ctp -->
<?php $edition_index = isset($edition_index) ? $edition_index : 0 ; ?>
					<?= $this->Form->input("editions.$edition_index.id", ['type' => 'hidden']); ?>

					<?php
					if (!empty($edition->series_id)) {
						echo $this->Html->tag('h3', "Part of the {$edition->series->title} Series");
					}
					?>

					<?= $this->Html->tag('h2', $edition->displayTitle); ?>

					<?php
					$piece_count = $edition->quantity - $edition->assigned_piece_count;
					$piece_label = $piece_count === 1 ? 'piece' : 'pieces';
					if ($piece_count !== 0) {
//						echo $this->Html->tag('p', "There are no unassigned pieces for this edition");
//					} else {
						echo $this->Html->link("Details about the $piece_count unassigned $piece_label in this edition",
								['controller' => 'pieces', 'action' => 'review', '?' => [
									'artwork' => $artwork->id,
									'edition' => $edition->id,
								]]);
					}
					// report on unmade pieces
					?>

					<!-- END Element/Edition/text.ctp -->
