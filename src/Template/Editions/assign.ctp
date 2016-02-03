<?php
$edition = $providers['edition'];
$this->set('edition', $edition);

/**
 * make 3 array
 *		one with edition and format entity nodes (stripped of pieces)
 *		one is basic 'destination' radio button data array (for helper)
 *		one is basic 'source' radio buttons (with disabled's set) for helper
 */

$radio_destination_data = $radio_source_data = [];

// INIT ALL 3 ARRAYS WITH EDITION NODE

// POPULATE ALL 3 ARRAYS WITH THE FORMAT NODES
foreach($providers as $provider) {
	$key_value = get_class($provider) . "\\{$provider->id}";

	$entry = [
		'value' => $key_value,
		'text' => $this->Html->tag('span', trim($provider->display_title)),
	];
	array_push($radio_destination_data, $entry);
	
	$range = $provider->range($provider->assignablePieces(PIECE_COLLECTION_RETURN), $edition->type);

	$entry['text'] = $this->Html->tag(
			'span', 
			$entry['text'] . ((boolean) $range ? " (Numbers: $range)" : ' (None Avaialble)'), 
			['class' => $provider->hasAssignable() ? '' : 'disabled']);
	array_push($radio_source_data, $entry + ['disabled' => !$provider->hasAssignable()]);
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

//osd($pieces);
//osd($providers);
//osd($edition);