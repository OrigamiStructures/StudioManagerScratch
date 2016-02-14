<div class="training">
	<h1>Welcome to ClearStudio</h1>
	<p>Are you ready to make your first artwork or would you like to see a video about the process?</p>
	<?= $this->Html->link('New Artwork', ['action' => 'create'], ['class' => 'button']); ?>
	<?= $this->Html->link('New Person', ['controller' => 'members', 'action' => 'create', 'Person'], ['class' => 'button']); ?>
	<?= $this->Html->link('Video', ['?' => ['video' => 'unique_create.mp4']], ['class' => 'button']); ?>

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
	
	if ($SystemState->isKnown('video')) {
		echo $this->Html->media($SystemState->queryArg('video'), ['pathPrefix' => 'files/training_videos/', 'controls' => true]);
	}
		?>
	
</div>
