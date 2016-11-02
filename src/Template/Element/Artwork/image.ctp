<!-- Element/Artwork/image.ctp -->
		<?= $this->Html->image($artwork->image == NULL ? "NoImage.png" : $artwork->image->fullPath('medium')); ?>

		<!-- END Element/Artwork/image.ctp -->
