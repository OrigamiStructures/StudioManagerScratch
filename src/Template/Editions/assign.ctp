<?php
/**
 * make 3 array
 *		one with edition and format entity nodes (stripped of pieces)
 *		one is basic 'destination' radio button data array (for helper)
 *		one is basic 'source' radio buttons (with disabled's set) for helper
 */

// INIT ALL 3 ARRAYS WITH EDITION NODE
$node = clone $edition[0];
unset($node->formats);
unset($node->pieces);
$key_value = get_class($node) . "\\{$node->id}";

$providers[$key_value] = $node;

$entry = [
	'value' => $key_value,
	'text' => $node->display_title,
];
$radio_source_data = [$entry + ['disabled' => !$node->hasUnassigned()]];
$radio_destination_data = [$entry];

// POPULATE ALL 3 ARRAYS WITH THE FORMAT NODES
foreach($edition[0]->formats as $format) {
	$node = clone $format;
	$key_value = get_class($node) . "\\{$node->id}";
	unset($node->pieces);
	$providers[$key_value] = $node;

	$entry = [
		'value' => $key_value,
		'text' => $this->Html->tag('span', trim($node->display_title . ' Format')),
	];
	array_push($radio_destination_data, $entry);
	
	$entry['text'] = $this->Html->tag('span', $entry['text'], ['class' => $node->hasFluid() ? '' : 'disabled']);
	array_push($radio_source_data, $entry + ['disabled' => !$node->hasFluid()]);
}
/**
 * DONE WITH MAKE 3 ARRAYS
 */

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
			$radio_destination_data,
			['escape' => FALSE]); ?>
	</div>
  </div>

<?php

echo $this->element('Pieces/overview_table');

osd($pieces);
osd($providers);
//osd($edition);