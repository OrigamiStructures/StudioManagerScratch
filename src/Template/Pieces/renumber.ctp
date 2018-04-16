<?php
$edition = $providers['edition'];
$this->set(compact('edition'));

if ($renumber_summary) :
?>
	<?= $renumber_summary; ?>

	<?= $this->Form->create('Pieces', ['id' => 'confirm']); ?>

		<?= $this->Form->button('approve', ['type' => 'submit']); ?>
		<?= $this->Form->input('do_move', ['value' => TRUE, 'type' => 'hidden']); ?>

	<?= $this->Form->end(); ?>
	
<?php endif ?>
<?= $this->Form->create('Pieces', ['id' => 'request']); ?>

	<?= $this->element('Pieces/renumber_table', ['caption' => 'Pieces in this edition']); ?>

<?= $this->Form->end(); ?>