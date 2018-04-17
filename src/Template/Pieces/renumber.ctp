<?php
$edition = $providers['edition'];
$this->set(compact('edition'));

if ($renumber_summary) :
?>
	<?php 
	if ($error) { 
	 foreach ($error as $message) {
		 echo "<p class='error'>$message</p>";
	 }
	} 
	; 
	 foreach ($renumber_summary as $message) {
		 echo "<p>$message</p>";
	 }
?>

	<?= $this->Form->create('Pieces', ['id' => 'confirm']); ?>

<?php if(!$error) : ?>
		<?= $this->Form->button('approve', ['type' => 'submit']); ?>
		<?= $this->Form->input('do_move', ['value' => TRUE, 'type' => 'hidden']); ?>
<?php endif; ?>

	<?= $this->Form->end(); ?>
	
<?php endif ?>
<?= $this->Form->create('Pieces', ['id' => 'request']); ?>

	<?= $this->element('Pieces/renumber_table', ['caption' => 'Pieces in this edition']); ?>

<?= $this->Form->end(); ?>