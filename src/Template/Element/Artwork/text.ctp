<!-- Element/Artwork/text.ctp -->
		<?= $this->Form->input('id', ['type' => 'hidden']); ?>

		<?= $this->Html->tag('h1', 
				$this->ArtStackTools->links('artwork', ['review', 'refine']) . 
				$artwork->title); ?>
		<?php
		if (!empty($artwork->description)) {
			echo $this->Html->tag('p', $artwork->description);
		}	
		?>

		<!-- END Element/Artwork/text.ctp -->
