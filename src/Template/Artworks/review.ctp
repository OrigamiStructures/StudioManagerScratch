<!-- Template/Artwork/review.ctp -->
<?= $this->element('Artwork/'.$element_management['artwork']);?>
<?php 
$args = $SystemState->queryArg(); 
$q = [];
foreach (['artwork', 'edition', 'format'] as $crumb) {
	if (array_key_exists($crumb, $args)) {
		$q = $q +[$crumb => $args[$crumb]];
		$this->Html->addCrumb(ucwords($crumb), ['action' => 'review', '?' => $q]);
		$this->Html->addCrumb('Edit', ['action' => 'refine', '?' => $q]);
	}
}

//osd($SystemState);