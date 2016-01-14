<!-- Element/Format/text.ctp -->
							<?= $this->Form->input($format->id, ['type' => 'hidden']); ?>

							<?= $this->Html->tag('p', $format->displayTitle, ['class' => 'format']); ?>

							<?php
							if (!empty($format->title)) {
								echo $this->Html->tag('p', $format->description);
							}
							$pieces = isset($format->pieces) ? count($format->pieces) : 0;
							$piece_lable = $pieces === 1 ? 'piece' : 'pieces';
							if ($pieces === 0) {
								echo $this->Html->tag('p', "There are $pieces $piece_lable for this format");
							} else {
								echo $this->Html->link("Details about the $pieces $piece_lable in this format",
									['controller' => 'pieces', 'action' => 'review', '?' => [
											'artwork' => $artwork->id,
											'edition' => $edition->id,
											'format' => $format->id
										]]);
							}
							// also provide information about pieces, 
							// including range, and subscriptions
							?>

							<!-- END Element/Format/text.ctp -->
