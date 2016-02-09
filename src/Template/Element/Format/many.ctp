<!-- Element/Format/many.ctp -->
<?php
foreach ($formats as $format_index => $format) {
	$this->set(compact('format_index', 'format'));
	echo $this->element('Format/full'); 
}
?>
						<!-- END Element/Format/many.ctp -->
