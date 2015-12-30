<!-- Element/Edition/text.ctp -->
					<?= $this->Form->input($edition->id, ['type' => 'hidden']); ?>

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
