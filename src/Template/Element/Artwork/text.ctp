<?php 
$q = [
	'controller' => 'artworks', 
	'?' => [
		'artwork' => $artwork->id
	]];
$l = $this->InlineTools->inlineReviewRefine($q);
?>
<!-- Element/Artwork/text.ctp -->
		<?= $this->Form->input('id', ['type' => 'hidden']); ?>

		<?= $this->Html->tag('h1', "{$l}$artwork->title"); ?>
		<?php
		if (!empty($artwork->description)) {
			echo $this->Html->tag('p', $artwork->description);
		}	
		?>

		<!-- END Element/Artwork/text.ctp -->
