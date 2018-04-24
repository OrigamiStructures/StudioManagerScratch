<!-- Template/Artwork/review.ctp -->
<?php 
use App\View\Helper\ArtStackElementHelper;
/**
 * Set values that amend tag classes for css refinement
 */
$this->loadHelper('DispositionTools');
$focus = '';
// centralized here? how about in the controllers. I might forget this location for logic set-up
$editing = in_array($SystemState->now(), [ARTWORK_CREATE, ARTWORK_REFINE, ARTWORK_CREATE_UNIQUE]);
if ($SystemState->urlArgIsKnown('format')) {
	$focus = 'format_focus';
}

/**
 * Store the newly created variables (and helper) and choose an element
 */
$ArtStackElement = $this->loadHelper('ArtStackElementHelper');
$this->set(compact('ArtStackElement', 'editing', 'focus'));
$artworks_element = $ArtStackElement->choose('artworksContent');

/**
 * This renders the whole enchilada for the 9 major pages (defined above). 
 * Everything is wrapped in a form if we're creating or editing
 */
?>
<div class="artworks">
	<?php
	if ($editing) : 
	$options = ['type' => 'file', 'class' => 'droppzone', 'id' => 'artwork_stack'];
		if ($SystemState->is(ARTWORK_CREATE_UNIQUE)) { 
			$options['action'] = 'create'; 
		}
		echo $this->Form->create($artwork, $options);
	endif; ?>
	
	<?= $this->element($artworks_element);?>
	
	<?php if ($editing) : echo $this->Form->end(); endif; ?>
</div>

<?php 
/**
 * This section creates the breadcrums. 
 * A lot of complication if this is going to go on many pages. 
 * As it stands, this is the one template for the 9 main page actions: 
 * Artworks, Editions, Formats; create, review, refine
 */
//$args = $SystemState->queryArg(); 
//$q = [];
//foreach (['artwork', 'edition', 'format'] as $crumb) {
//	if (array_key_exists($crumb, $args)) {
//		$q = $q +[$crumb => $args[$crumb]];
//		$controller = "{$crumb}s";
//		$edit_link = $this->Html->link('Edit', ['controller' => $controller, 'action' => 'refine', '?' => $q]);
//		$new_link = $this->Html->link('New', ['controller' => $controller, 'action' => 'create', '?' => $q]); 
//		$tools = " <span>[$edit_link • $new_link]</span>";
//		$this->Html->addCrumb(ucwords($crumb), ['action' => 'review', '?' => $q], ['escape' => FALSE, 'class' => 'review']);
//	}
//}