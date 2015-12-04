
<fieldset>
	<?= $this->Form->input('multiple_venues', []); ?>
	<?=  $this->Form->select('venues', $venues, ['mulitple' => true, 'empty' => 'Add a Venue']); ?>
	<?=  $this->Form->input('date'); ?>
</fieldset>