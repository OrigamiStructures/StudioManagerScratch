<!-- Element/Artwork/many.ctp -->

<?php
// https://github.com/OrigamiStructures/StudioManagerScratch/issues/70
// //echo $this->Paginator->counter();
//echo $this->Paginator->numbers();

/* @var \App\Model\Lib\StackSet $artworks */
/* @var \App\Model\Entity\ArtStack $artwork */

foreach ($artworks->getData() as $artwork_index => $artwork){

	echo $this->Html->tag('h1',$artwork->title());
	echo $this->Html->para(null, $artwork->description());

}
?>
