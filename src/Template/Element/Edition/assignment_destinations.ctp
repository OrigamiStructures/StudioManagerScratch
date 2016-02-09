<!-- Element/Edition/assignment_destinations.ctp -->
<?php 
$radio_destination_data = [];
foreach($providers as $provider) {
	$key_value = get_class($provider) . "\\{$provider->id}";
	$entry = [
		'value' => $key_value,
		'text' => $this->Html->tag('span', trim($provider->display_title)),
	];
	array_push($radio_destination_data, $entry);
}
$field_name = 'destinations_for_pieces';
?>
		<p>Destinations</p>
		<?= $this->Form->radio(
			$field_name,
			$radio_destination_data,
			['escape' => FALSE]); ?>
		
		<?= $helper->validationError($field_name, $errors); ?>
<?php 
//if (isset($errors[$field_name])) {
//	echo $this->Html->div('error-message', $errors[$field_name]);
//}
//osd($this->request->data);
//osd($errors);
?>
<!--<div class="error-message">The quantity was set lower than the allowed minimum</div>-->

<!-- END Element/Edition/assignment_destinations.ctp -->
