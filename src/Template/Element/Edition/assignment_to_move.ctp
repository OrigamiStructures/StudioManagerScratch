			<!-- Element/Edition/assignment_to_move.ctp -->
			<?php 
			if (\App\Lib\EditionTypeMap::isNumbered($edition->type)) {
				$label = 'Pieces to move. Enter numbers (13) or ranges (5-10) separated by commas (, )';
			} else {
				$label = 'Enter the quantity of pieces to move.';
			}
			?>
			<?= $this->Form->input('to_move', ['label' => $label]); ?>
			<?= $helper->validationError('to_move', $errors); ?>
			<!-- END Element/Edition/assignment_to_move.ctp -->
