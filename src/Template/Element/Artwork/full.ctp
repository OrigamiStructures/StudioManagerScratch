<!-- Element/Artwork/full.ctp -->

<?php 
// Gets the name of the element based on SystemState->now()
$artwork_element = $ArtStackElement->choose('artworkContent');
?>

<section class="artwork<?= $editing ? ' editing' : ''; ?>">
	<?= $this->element($artwork_element) ?>

	<div class="editions">
		<?php $this->set('editions', $artwork->editions); ?>
		<?= $this->element('Edition/many'); ?>
	</div>
</section>
<!-- END Element/Artwork/full.ctp -->
