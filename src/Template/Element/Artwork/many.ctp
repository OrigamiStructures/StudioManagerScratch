<!-- Element/Artwork/many.ctp -->
	
<?php
// https://github.com/OrigamiStructures/StudioManagerScratch/issues/70
// //echo $this->Paginator->counter();
//echo $this->Paginator->numbers();
foreach ($artworks as $artwork_index => $artwork){
	$this->set(compact('artwork_index', 'artwork'));
	echo $this->element('Artwork/full');
}
?>
