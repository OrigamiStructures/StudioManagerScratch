<!-- Element/Artwork/describe.ctp -->
<div class="display-artwork-layer">
	<?= $this->Form->input($artwork->id, ['type' => 'hidden']); ?>
	<?= $this->Html->tag('h1', $artwork->title); ?>
	<?php
	if (!empty($artwork->description)) {
		echo $this->Html->tag('p', $artwork->description);
	}	
	?>
</div>
