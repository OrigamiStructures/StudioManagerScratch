<div class="training">
	<h1>Welcome to ClearStudio</h1>
	<p>Are you ready to make your first artwork or would you like to see a video about the process?</p>
	<?= $this->Html->link('New Edition', ['action' => 'create'], ['class' => 'button']); ?>
	<?= $this->Html->link('New Unique Work', ['action' => 'create_unique'], ['class' => 'button']); ?>
	<?= $this->Html->link('New Person', ['controller' => 'members', 'action' => 'create', 'Person'], ['class' => 'button']); ?>
	<?= $this->Html->link('Video', ['?' => ['video' => 'unique_create.mp4']], ['class' => 'button']); ?>

	<?php
    $video = \Cake\Utility\Hash::get($this->request->getQueryParams(), 'video', FALSE);
	if ($video !== FALSE) {
		echo $this->Html->media($video, ['pathPrefix' => 'files/training_videos/', 'controls' => true, 'autoplay' => true]);
	}
	?>

</div>
