<!-- Element/Artwork/spec.ctp -->
<fieldset>
	<legend>'spec' elements (artwork)</legend>
	<?php
		echo $this->Form->input('user_id', ['type' => 'hidden']);
		echo "\n\t". 'Image upload'; //$this->Form->input('image_id', []);
		echo "\n\t". $this->Form->input('Artwork.title');
		echo "\n\t". $this->Form->input('Artwork.description') . "\n";
	?>
</fieldset>
