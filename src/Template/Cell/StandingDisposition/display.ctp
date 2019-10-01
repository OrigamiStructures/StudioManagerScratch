<?php
$this->loadHelper('DispositionTools');
if($standing_disposition) :
	$DispositionTable = Cake\ORM\TableRegistry::getTableLocator()->get('Dispositions');
	$disposition_label = $DispositionTable->disposition_label;

?>
<div class="dispositions_panel">
	<div class="dispositions">
		<div class="disposition">

			<?= $this->element('Disposition/reference_panel_sections'); ?>

		</div>
	</div>
</div>
<?php endif ?>
