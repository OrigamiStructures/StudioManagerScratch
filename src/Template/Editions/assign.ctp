<!-- Editions/assign.ctp -->
<?php
$helper = $this->loadHelper('Assign');
$edition = $providers['edition'];
$this->set(compact('edition', 'helper'));
?>

  <div class="row">
	<?= $this->Form->create(); ?>
    <div class="large-6 columns radio">
		<?= $this->element('Edition/assignment_sources'); ?>
		<div class="high">
			<?= $this->Form->input('to_move', ['label' => 'Pieces to move', 'error' => 'some error']); ?>
			<?= $helper->validationError('to_move', $errors); ?>
			<?= $this->Form->submit(); ?>
		</div>
	</div>
    <div class="large-6 columns radio">
		<?= $this->element('Edition/assignment_destinations'); ?>
		<div class="high">
			
		</div>
	</div>
	<?= $this->Form->end(); ?>

  </div>
<?php

echo $this->element('Pieces/overview_table');

//osd($pieces);
//osd($providers);
//osd($edition);
?>

<!-- END Editions/assign.ctp -->
