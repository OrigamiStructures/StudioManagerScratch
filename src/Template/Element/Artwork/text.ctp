<?php 
$q = [
	'controller' => 'artworks', 
//	'action' => 'review', 
	'?' => [
		'artwork' => $artwork->id
	]];
$nav = $this->Html->link('v', $q + ['action' => 'review']);
$ed = $this->Html->link('f', $q + ['action' => 'refine']);
$l = "<span class='nav'>[$nav|$ed] </span>"
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
