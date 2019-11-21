<!-- Element/Artwork/many.ctp -->

<?php
// https://github.com/OrigamiStructures/StudioManagerScratch/issues/70
// //echo $this->Paginator->counter();
//echo $this->Paginator->numbers();
foreach ($artworks->load() as $artwork_index => $artwork){
//	$this->set(compact('artwork_index', 'artwork'));
//	echo $this->element('Artwork/full');
	

	echo $this->Html->tag('h1',$artwork->title());
	echo $this->Html->para(null, $artwork->description());

}
?>
