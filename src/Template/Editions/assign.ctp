<?php
$node = clone $edition[0];
unset($node->formats);
unset($node->pieces);
$providers = [$node];

$entry = [
	'value' => get_class($node) . "\\{$node->id}",
	'text' => $node->display_title,
];
$radio_source_data = [$entry + ['disabled' => !$node->hasUnassigned()]];
$radio_destination_data = [$entry];

foreach($edition[0]->formats as $format) {
	$node = clone $format;
	unset($node->pieces);
	array_push($providers, $node);

	$entry = [
		'value' => get_class($node) . "\\{$node->id}",
		'text' => $this->Html->tag('span', $node->display_title . ' Format'),
	];
	array_push($radio_destination_data, $entry);
	
	$entry['text'] = $this->Html->tag('span', $entry['text'], ['class' => $node->hasFluid() ? '' : 'disabled']);
	array_push($radio_source_data, $entry + ['disabled' => !$node->hasFluid()]);
}
?>
  <div class="row">
    <div class="large-6 columns radio">
		<p>Sources: Pieces to assign or reassign</p>
		<?= $this->Form->radio(
			'Sources for Pieces',
			$radio_source_data,
			['escape' => FALSE]); ?>
	</div>
    <div class="large-6 columns radio">
		<p>Destinations</p>
		<?= $this->Form->radio(
			'Destinations for Pieces',
			$radio_destination_data); ?>
	</div>
  </div>
<?php

osd($providers);
osd($edition);