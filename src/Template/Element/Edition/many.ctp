<!-- Element/Edition/many.ctp -->
<?php
foreach ($editions as $edition){
	$this->set('edition', $edition);
	
	echo $this->element('Edition/full');
}
?>

			<!-- END Element/Edition/many.ctp -->
