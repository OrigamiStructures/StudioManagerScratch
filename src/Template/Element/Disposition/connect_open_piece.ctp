<?= 
$this->Form->create(NULL, [
	'url' => [
		'controller' => 'dispositions',
		'action' => 'refine',
		'?' => $SystemState->queryArg() + ['piece' => $piece->id],
	]
]); 
?>
<?= $this->Form->input('piece_id', ['type' => 'hidden', 'value' => $piece->id]); ?>
<?= $this->Form->input('to_move', ['value' => 1, 'label' => $label]); ?>
<?= $this->Form->submit(); ?>
<?= $this->Form->end(); ?>
