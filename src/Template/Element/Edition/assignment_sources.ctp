<!-- Element/Edition/assignment_sources.ctp -->
<?php 
$assignment_source_data = [];

$source = new \Cake\Collection\Collection($providers);
$source_output = $source->reduce(function($accumulator, $provider) use($helper, $edition) {
	$key = $provider->key();
	$acumulator[$key] = [];
	$range = $helper->rangeText($provider, $edition);
	$text = $this->Html->tag('span', $provider->display_title, ['class' => 'source']);
	$attributes = $provider->hasAssignable() ? ['escape' => FALSE] : ['class' => 'disabled', 'escape' => FALSE, 'disabled' => TRUE];
	
	$accumulator[$key] = [
		'range' => $range,
		'text' => $text,
		'label' => $this->Html->tag('p',  "$text $range", $attributes),
		'value' => get_class($provider) . '\\' . $provider->id,
		'attributes' => $attributes,
	];
	return $accumulator;
}, []);



//osd($source_output);

if (in_array($edition->type, App\Lib\SystemState::limitedEditionTypes())) {
	// checkboxes, all checked by default
	foreach($source_output as $source) {
		echo $source['label'];
	}

} else {
	// labels
	foreach($source_output as $index => $source) {
//		osd($source);
//		echo($source['text']);
//		echo($source['range']);
//		echo($source['value']);
		$l = $source['text'];
		$v = $source['value'];
		echo $this->Form->label("source_for_pieces_$index", $source['label'], ['escape' => FALSE]);
		$attr = ['label' => FALSE, 'type' => 'checkbox', 'value' => $v] + $source['attributes'];
//		osd($attr);
		echo $this->Form->input("source_for_pieces_$index", $attr);
	}
}
?>
<!-- END Element/Edition/assignment_sources.ctp -->
