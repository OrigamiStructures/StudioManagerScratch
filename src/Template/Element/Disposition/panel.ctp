<?php
$this->loadHelper('DispositionTools');
if(empty($standing_disposition->label)) {
	$DispositionTable = Cake\ORM\TableRegistry::getTableLocator()->get('Dispositions');
	$disposition_label = $DispositionTable->disposition_label;
//	osd($disposition_label);
}
?>
<div class="dispositions_panel">
	<div class="dispositions">
		<div class="disposition">

			<?= $this->element('Disposition/reference_panel_sections'); ?>

		</div>
	</div>
</div>
