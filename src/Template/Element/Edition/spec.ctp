<!-- Element/Edition/spec.ctp -->
<?= $this->Form->select('series', $series, ['empty' => 'Part of a series?']); ?>
<fieldset>
	<legend>'spec' elements (edition)</legend>
	<?php
		echo "\n\t". $this->Form->input('user_id', ['type' => 'hidden']);
		echo "\n\t". $this->Form->input('Edition.type', ['empty' => 'Choose an edition type']);
		echo "\n\t". $this->Form->input('Edition.title', ['placeholder' => 'optional', 'label' => 'Edition Name']);
		echo "\n\t". $this->Form->input('Edition.quantity', ['label' => 'Edition size']) . "\n";
	?>
</fieldset>