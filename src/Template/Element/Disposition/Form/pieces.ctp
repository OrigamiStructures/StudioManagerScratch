
<fieldset>
	<?= $this->Form->input('multiple_peices', []); ?>
	<?=  $this->Form->select('pieces', $pieces, ['mulitple' => true, 'empty' => 'Add a Piece']); ?>
</fieldset>