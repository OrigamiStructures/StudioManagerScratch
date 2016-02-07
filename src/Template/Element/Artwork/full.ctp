<!-- Element/Artwork/full.ctp -->
<section class="artwork">
	<div class="image">
		<?= $this->element('Artwork/image') ?>
	</div>
	<div class="text">
		<?= $this->element('Artwork/text'); ?>
	</div>
	<div class="editions">
	<?php $this->set('editions', $artwork->editions); ?>
		<?= $this->element('Edition/' . $element_management['edition']); ?>
	</div>
</section>
<!-- END Element/Artwork/full.ctp -->
