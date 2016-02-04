<!-- Element/Edition/assignment_sources.ctp -->
<?php 
$assignment_source_data = [];

$source = new \Cake\Collection\Collection($providers);
$source_output = $source->reduce(function($accumulator, $provider) use($helper, $edition) {
	$key = $provider->key();
	$acumulator[$key] = [];
	$range = $helper->rangeText($provider, $edition);
	$text = $this->Html->tag('span', $provider->display_title, ['class' => 'source']);
	$attributes = $provider->hasAssignable() ? ['escape' => FALSE, 'checked' => TRUE] : ['class' => 'disabled', 'escape' => FALSE, 'disabled' => TRUE];
	
	$accumulator[$key] = [
		'range' => $range,
		'text' => $text,
		'label' => $this->Html->tag('p',  "$text $range", $attributes),
		'value' => get_class($provider) . '\\' . $provider->id,
		'attributes' => $attributes,
		'disabled' => !$provider->hasAssignable(),
	];
	return $accumulator;
}, []);



//osd($source_output);

if (App\Lib\SystemState::isNumberedEdition($edition->type)) {
	// checkboxes, all checked by default
	$count = 0;
	foreach($source_output as $source) {
		if (!$source['disabled']) {
			echo $this->Form->input("source_for_pieces_$count", ['type' => 'hidden', 'value' => $source['value']]);
		}
		echo $source['label'];
		$count++;
	}

} else {
	// labels
	$count = 0;
	foreach($source_output as $index => $source) {
		/**
		 * MAKE A PROPER TAG TEMPLATE TO CLEAN THIS SHIT UP
		 */
		$attr = ['label' => FALSE, 'type' => 'checkbox', 'value' => $source['value']] + $source['attributes'];
		$input = $this->Form->input("source_for_pieces_$count", $attr);
		$input = str_replace(['<div class="input checkbox">', '</div>'], ['', ''], $input);
		
		echo '<div class="input checkbox">';
		echo $this->Form->label(
				"source_for_pieces_$count", 
				$input . $source['text'] . ' ' . $source['range'], 
				['escape' => FALSE] + $source['attributes']
		);
		echo '</div>';
		
		$count++;

	}
	echo $helper->validationError('source_for_pieces_0', $errors);
}
?>
<!-- END Element/Edition/assignment_sources.ctp -->
