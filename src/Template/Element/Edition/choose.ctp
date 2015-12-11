<?php
$series_default = ['n' => 'New Series'];
$series = !isset($series) ? $series_default : $series + $series_default;
$fully_qualified = isset($fully_qualified) ? $fully_qualified : [];
?>
<fieldset>
	<legend>'choose' elements (edition)</legend>
	<?= $this->Form->select('series', $series, ['empty' => 'Belongs to a series?']); ?>
	<?= $this->Form->input('edition', ['empty' => 'Choose the edition for this new format']) ?>
</fieldset>