<!-- Element/Artwork/many.ctp -->
	
<?php
foreach ($artworks as $artwork){
	$this->set('artwork', $artwork);
	echo $this->element('Artwork/full');
}
?>
