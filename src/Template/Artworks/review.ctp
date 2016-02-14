<!-- Template/Artwork/review.ctp -->

<?php 
$editing = in_array($SystemState->now(), [ARTWORK_CREATE, ARTWORK_REFINE, ARTWORK_CREATE_UNIQUE]);
$ArtStackElement = $this->loadHelper('App\View\Helper\ArtStackElementHelper');
$this->set(compact('ArtStackElement', 'editing'));
$artworks_element = $ArtStackElement->choose('artworksContent');
?>

<div class="artworks">
	<?php
	if ($editing) : 
		if ($SystemState->is(ARTWORK_CREATE_UNIQUE)) {
			echo $this->Form->create($artwork, ['type' => 'file', 'action' => 'create']);
		} else {
			echo $this->Form->create($artwork, ['type' => 'file']);
		}	
	endif; ?>
	<?= $this->element($artworks_element);?>
	<?php if (in_array($SystemState->now(), [ARTWORK_CREATE, ARTWORK_REFINE, ARTWORK_CREATE_UNIQUE])) : echo $this->Form->end(); endif; ?>
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
		$tools = " <span>[$edit_link • $new_link]</span>";
		$this->Html->addCrumb(ucwords($crumb). $tools, ['action' => 'review', '?' => $q], ['escape' => FALSE, 'class' => 'review']);
//		$this->Html->addCrumb('Edit', ['action' => 'refine', '?' => $q]);
	}
}

//osd($SystemState);