<!-- Template/Artwork/review.ctp -->
<?php 
//$this->loadHelper('DispositionTools');
//$this->loadHelper('ArtElement',['className' => 'ArtStackElement']);

$decorated_element = $this->ArtElement->choose('contentDecoration');

/**
 * This renders the whole enchilada for the 9 major pages (defined above). 
 * Everything is wrapped in a form if we're creating or editing
 */
?>
<div class="artworks">
	
	<?= $this->element($decorated_element);?>
	
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