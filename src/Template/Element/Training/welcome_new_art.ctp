<div class="training">
	<h1>Welcome to ClearStudio</h1>
	<p>Are you ready to make your first artwork or would you like to see a video about the process?</p>
	<?= $this->Html->link('New Edition', ['action' => 'create'], ['class' => 'button']); ?>
	<?php // echo $this->Form->postLink('New Artwork', 
//		['action' => 'create_unique'], 
//		[
//			'class' => 'button',
//			'data' => [
//				'id' => '',
//				'editions.0.id' => '',
//				'editions.0.type' => 'Unique',
//				'editions.0.formats.0.id' => '']
//		]); 
		?>
</div>
