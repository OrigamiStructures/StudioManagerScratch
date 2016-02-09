<!-- Template/Artwork/review.ctp -->

<?php 
$ArtStackElement = $this->loadHelper('App\View\Helper\ArtStackElement');
$this->set('ArtStackElement', $ArtStackElement);
$artworks_element = $ArtStackElement->choose('artworksContent');
?>

<div class="artworks">
<?= $this->element($artworks_element);?>
</div>

<?php 
$args = $SystemState->queryArg(); 
$q = [];
foreach (['artwork', 'edition', 'format'] as $crumb) {
	if (array_key_exists($crumb, $args)) {
		$q = $q +[$crumb => $args[$crumb]];
		$this->Html->addCrumb(ucwords($crumb), ['action' => 'review', '?' => $q]);
		$this->Html->addCrumb('Edit', ['action' => 'refine', '?' => $q]);
	}
}

//osd($SystemState);