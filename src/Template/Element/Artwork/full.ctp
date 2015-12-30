<!-- Element/Artwork/full.ctp -->
<section class="artwork row">
	<div class="columns small-12 medium-3 image">
		<?= $this->element('Artwork/image') ?>
	</div>
	<div class="columns small-12 medium-9 text">
		<?= $this->element('Artwork/text'); ?>
		<?php
			$this->set('editions', $artwork->editions);
			echo $this->element('Edition/' . $element_management['edition']);
		?>
	</div>
</section>
<!-- END Element/Artwork/full.ctp -->
