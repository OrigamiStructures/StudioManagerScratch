<!-- Element/Artwork/index.ctp -->
<section class="artwork">
	<div class="row">
		<div class="columns small-12 medium-3 image">
            <?php
                osd($artwork);
                osd($artwork->image->fullPath);
                die;
            ?>
            <?= $this->Html->image($artwork->image->fullPath); ?>
		</div>
		<div class="columns small-12 medium-9 description">
			<h1><?= $artwork->title; ?></h1>
		</div>
	</div>
</section>