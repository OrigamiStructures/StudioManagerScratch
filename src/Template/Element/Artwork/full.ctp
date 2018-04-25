<!-- Element/Artwork/full.ctp -->

<?php 
// Gets the name of the element based on SystemState->now()
$artwork_element = $this->ArtElement->choose('artworkContent');
?>

<section class="artwork">
	<?= $this->element($artwork_element) ?>

	<div class="editions">
		<?php $this->set('editions', $artwork->editions); ?>
		<?= $this->element('Edition/many'); ?>
	</div>
</section>
<!-- END Element/Artwork/full.ctp -->
