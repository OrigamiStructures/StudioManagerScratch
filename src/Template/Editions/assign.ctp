<!-- Editions/assign.ctp -->
<?php
$helper = $this->loadHelper('Assign');
$edition = $providers['edition'];
$this->set(compact('edition', 'helper'));
?>

  <div class="row">
	<?= $this->Form->create($assign); ?>
    <div class="large-6 columns radio">
		<?= $this->element('Edition/assignment_sources'); ?>
		<div class="high">
			<?= $this->element('Edition/assignment_to_move'); ?>
			<?= $this->Form->submit('Submit', ['class' => 'button']); ?>
		</div>
	</div>
    <div class="large-6 columns radio">
		<?= $this->element('Edition/assignment_destinations'); ?>
		<div class="high">
			
		</div>
	</div>
	<?= $this->Form->end(); ?>

  </div>
	<div class="row">
		<div class="column small-8">
	<?= $this->element('Pieces/overview_table', ['caption' => 'Pieces in this edition']); ?>
		</div>
	</div>
<?php

//echo $this->element('Pieces/overview_table', ['caption' => 'Pieces in this edition']);

/**
 * MAKE BREADCRUMBS FOR PAGE
 */
$args = $SystemState->queryArg(); 
$q = [];
foreach (['artwork', 'edition', 'format'] as $crumb) {
	if (array_key_exists($crumb, $args)) {
		$q = $q +[$crumb => $args[$crumb]];
		$this->Html->addCrumb(ucwords($crumb), ['action' => 'review', '?' => $q]);
		$this->Html->addCrumb('Edit', ['action' => 'refine', '?' => $q]);
	}
}
?>

<!-- END Editions/assign.ctp -->
