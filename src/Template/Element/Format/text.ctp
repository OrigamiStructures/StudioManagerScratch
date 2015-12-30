<!-- Element/Format/text.ctp -->
							<?= $this->Form->input($format->id, ['type' => 'hidden']); ?>

							<?= $this->Html->tag('p', $format->displayTitle, ['class' => 'format']); ?>

							<?php
							// also provide information about pieces, 
							// including range, and subscriptions
							?>

							<!-- END Element/Format/text.ctp -->
