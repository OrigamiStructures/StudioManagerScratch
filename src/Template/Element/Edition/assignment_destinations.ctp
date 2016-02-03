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
?>
		<p>Destinations</p>
		<?= $this->Form->radio(
			'Destinations for Pieces',
			$radio_destination_data,
			['escape' => FALSE]); ?>
<!-- END Element/Edition/assignment_destinations.ctp -->
