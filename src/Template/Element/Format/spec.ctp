<!-- Element/Format/spec.ctp -->
<fieldset>
	<legend>'spec' elements (format)</legend>
	<?php
		echo $this->Form->input('user_id', ['type' => 'hidden']);
		echo "\n\t". $this->Form->input('Format.title', ['placeholder' => 'optional', 'label' => 'Format Name']);
		echo "\n\t". $this->Form->input('Format.description') . "\n";
		echo "\n\t". 'Image upload'; //$this->Form->input('image_id', []);
	?>
</fieldset>
