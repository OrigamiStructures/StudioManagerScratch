<section class="artwork">
	<div class="row">
		<div class="columns small-12 medium-3 image">
            <?= $this->Html->image($artwork->image == NULL ? "NoImage.png" : $artwork->image->fullPath); ?>
		</div>
		<div class="columns small-12 medium-9 description">
			<?= $this->element('Artwork/describe'); ?>
            <?php
                $this->set('editions', $artwork->editions);
                echo $this->element('Edition/' . $element_management['edition']);
            ?>
		</div>
	</div>
</section>