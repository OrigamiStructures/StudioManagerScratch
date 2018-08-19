<!-- Element/Edition/piece_assignment_decoration.ctp -->
<?php
$helper = $this->loadHelper('Assign');
$edition = $providers['edition'];
$edition_index = $edition->index;
$this->set(compact('edition', 'helper', 'artwork'));
$edition_index = isset($edition_index) ? $edition_index : 0 ; 
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


  </div>
	<div class="row">
		<div class="column small-8">
	<?= $this->element('Pieces/overview_table', ['caption' => 'Pieces in this edition']); ?>
		</div>
	</div>

<?= $this->element('Artwork/full');?>
<?= $this->Form->end(); ?>		

<!-- END Element/Edition/piece_assignment_decoration.ctp -->
