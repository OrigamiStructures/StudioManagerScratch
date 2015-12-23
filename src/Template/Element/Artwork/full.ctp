<!-- Element/Artwork/full.ctp -->
<?php foreach ($artworks as $artwork): ?>
<section class="artwork">
	<div class="row">
		<div class="columns small-12 medium-3 image">
            <?= $this->Html->image($artwork->image == NULL ? "NoImage.png" : $artwork->image->fullPath); ?>
		</div>
		<div class="columns small-12 medium-9 description">
			<h1><?= $artwork->title; ?></h1>
            <?php
                $this->set('editions', $artwork->editions);
                echo $this->element('Edition/' . $element_management['edition']);
            ?>
		</div>
	</div>
</section>
<?php        endforeach;?>