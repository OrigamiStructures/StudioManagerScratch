<?= 
$this->Form->create(NULL, [
	'url' => [
		'controller' => 'dispositions',
		'action' => 'refine',
	]
]); 
?>
<?= $this->Form->input("piece.$piece->id.quantity", ['value' => 1, 'label' => $label, 'div' => FALSE]); ?>
<?= $this->Form->submit(); ?>
<?= $this->Form->end(); ?>
