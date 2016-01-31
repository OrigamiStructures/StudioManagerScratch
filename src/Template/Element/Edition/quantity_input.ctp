<?php if ($SystemState->is(ARTWORK_CREATE)) : ?>
		
		<?= $this->Form->input("editions.$edition_index.quantity", [
			'default' => 1, 
			'label' => 'Edition Size'
		]); ?>
<?php 
 else :
	$factory = $this->loadHelper('EditionFactory');
	$helper = $factory->load($edition->type);
?>

		<?= $helper->quantityInput($edition, $edition_index); ?>

<?php endif; ?>

