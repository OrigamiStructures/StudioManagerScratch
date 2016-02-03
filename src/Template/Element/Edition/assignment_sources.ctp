<!-- Element/Edition/assignment_sources.ctp -->
<?php 
$radio_source_data = [];

// $providers: a standard product of EditionStackComponent
foreach($providers as $provider) {
	$key_value = get_class($provider) . "\\{$provider->id}";
	$entry = [
		'value' => $key_value,
		'text' => $this->Html->tag('span', trim($provider->display_title)),
	];
	$range = $provider->range($provider->assignablePieces(PIECE_COLLECTION_RETURN), $edition->type);
	$entry['text'] = $this->Html->tag(
			'span', 
			$entry['text'] . ((boolean) $range ? " (Numbers: $range)" : ' (None Avaialble)'), 
			['class' => $provider->hasAssignable() ? '' : 'disabled']);
	array_push($radio_source_data, $entry + ['disabled' => !$provider->hasAssignable()]);
}
?>
		<p>Sources: Pieces to assign or reassign</p>
		<?= $this->Form->radio(
			'Sources for Pieces',
			$radio_source_data,
			['escape' => FALSE]); ?>
<!-- END Element/Edition/assignment_sources.ctp -->
