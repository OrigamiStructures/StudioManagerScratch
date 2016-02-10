<!-- Element/Edition/many.ctp -->
<?php
foreach ($editions as $edition_index => $edition){
	$this->set(compact('edition_index', 'edition'));
	
	echo $this->element('Edition/full');
}
?>

			<!-- END Element/Edition/many.ctp -->
