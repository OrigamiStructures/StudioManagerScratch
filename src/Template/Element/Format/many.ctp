<!-- Element/Format/many.ctp -->
<?php
foreach ($formats as $format) {
	$this->set('format', $format);
	echo $this->element('Format/full'); 
}
?>
						<!-- END Element/Format/many.ctp -->
