<!-- Element/Artwork/many.ctp -->
<div class="artworks">
	
<?php
foreach ($artworks as $artwork){
	$this->set('artwork', $artwork);
	echo $this->element('Artwork/full');
}
?>
</div>
