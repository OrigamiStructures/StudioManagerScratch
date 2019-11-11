<!-- Element/Artwork/create_unique.ctp -->
	<div class="image">
		<?= $this->element('Image/artwork_fieldset'); ?>
	</div>
	<div class="text">
		<fieldset>
			<?= $this->Form->input('id'); ?>
			<?= $this->Form->input('title', ['label' => 'Artwork Title']); ?>
			<?= $this->Form->input('image_id',
					['type' => 'hidden']); ?>
		</fieldset>
		<?php
			if ($SystemState->controller() === 'artworks' && 
					$artwork->edition_count > 1) {
				echo $this->Form->submit('Submit', ['class' => 'button']);
			}
		?>
	</div>
<!-- END Element/Artwork/create_unique.ctp -->
