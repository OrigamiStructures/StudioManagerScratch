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
	</div>
    <div class="large-6 columns radio">
		<?= $this->element('Edition/assignment_destinations'); ?>
	</div>
	<?= $this->Form->submit(); ?>
	<?= $this->Form->end(); ?>
  </div>

<?php

echo $this->element('Pieces/overview_table');

//osd($pieces);
//osd($providers);
//osd($edition);
?>
<!-- END Editions/assign.ctp -->
