<!-- Template/Artwork/review.ctp -->

<?php 
$editing = in_array($SystemState->now(), [ARTWORK_CREATE, ARTWORK_REFINE]);
$ArtStackElement = $this->loadHelper('App\View\Helper\ArtStackElementHelper');
$this->set(compact('ArtStackElement', 'editing'));
$artworks_element = $ArtStackElement->choose('artworksContent');
?>

<div class="artworks">
	<?php
	if ($editing) : 
		echo $this->Form->create($artwork, ['type' => 'file']); 
	endif; ?>
	<?= $this->element($artworks_element);?>
	<?php if (in_array($SystemState->now(), [ARTWORK_CREATE, ARTWORK_REFINE])) : echo $this->Form->end(); endif; ?>
</div>

<?php 
$args = $SystemState->queryArg(); 
$q = [];
foreach (['artwork', 'edition', 'format'] as $crumb) {
	if (array_key_exists($crumb, $args)) {
		$q = $q +[$crumb => $args[$crumb]];
		$controller = "{$crumb}s";
		$edit_link = $this->Html->link('Edit', ['controller' => $controller, 'action' => 'refine', '?' => $q]);
		$new_link = $this->Html->link('New', ['controller' => $controller, 'action' => 'create', '?' => $q]); 
		$this->Html->addCrumb(ucwords($crumb). " [$edit_link • $new_link]", ['action' => 'review', '?' => $q], ['escape' => FALSE, 'class' => 'review']);
//		$this->Html->addCrumb('Edit', ['action' => 'refine', '?' => $q]);
	}
}

//osd($SystemState);