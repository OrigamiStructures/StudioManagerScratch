<?php 
if (count($standing_disposition->addresses) > 1) {
	$collection = new \Cake\Collection\Collection($standing_disposition->addresses);
	$output = $collection->reduce(function($accumulator, $value){
		$accumulator[] = ['value' => $value->id, 'text' => $value->address_line];
		return $accumulator;
	}, []);
} else {
	$value = array_shift($standing_disposition->addresses);
	$output = $this->Html->tag('p', $value->address_line);
}
?>

		<?= is_string($output) ? $output : $this->Form->radio('address', $output);?>
		
<?php
//endforeach;
?>