<!-- Element/Artwork/text.ctp -->
		<?= $this->Form->input($artwork->id, ['type' => 'hidden']); ?>

		<?= $this->Html->tag('h1', $artwork->title); ?>
		<?php
		if (!empty($artwork->description)) {
			echo $this->Html->tag('p', $artwork->description);
		}	
		?>

		<!-- END Element/Artwork/text.ctp -->
