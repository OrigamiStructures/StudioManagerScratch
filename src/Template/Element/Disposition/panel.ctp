<div class="dispositions_panel">
<?php 
$this->loadHelper('DispositionTools');
if(empty($standing_disposition->label)) {
	$DispositionTable = Cake\ORM\TableRegistry::get('Dispositions');
	$disposition_label = $DispositionTable->disposition_label;
//	osd($disposition_label);
}
?>	
	<div class="disposition">
		
		<?= $this->element('Disposition/reference_panel_sections'); ?>
		
	</div>
</div>
