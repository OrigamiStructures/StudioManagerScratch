<!-- Element/Artwork/many.ctp -->
	
<?php
foreach ($artworks as $artwork_index => $artwork){
	$this->set(compact('artwork_index', 'artwork'));
	echo $this->element('Artwork/full');
}
?>
