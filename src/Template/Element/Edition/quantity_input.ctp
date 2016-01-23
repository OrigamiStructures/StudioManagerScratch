<?php
$policy_statement = FALSE;

if ($SystemState->is(ARTWORK_CREATE)) {
	$default = 1;
	$lable = 'Edition Size';
//		echo $this->Form->input("editions.$edition_index.quantity", ['default' => 1]);
		
} elseif (!$this->isUnique($artwork->editions[$edition_index])) {
	
	$size_statement = $increase = $policy = '';
	$minimum = $edition->disposed_piece_count > 0 ? $edition->disposed_piece_count : 1 ;
	$label = ($edition->hasFluid() ? 'Reduce or ' : '') . "Increase the edition size ($minimum or greater):";
//	$salable_statement = !$edition->hasSalable() ? $this->Html->para(NULL, "This edition is sold out.") : '';
	$default = $edition->quantity;
	
	if ($edition->type === EDITION_LIMITED & $edition->hasCollected()) {
		$pieces = $edition->collected_piece_count === 1 ? 'one piece' : $edition->collected_piece_count . ' pieces';
		
		$policy = "You have sold $pieces from this edition.<br />Most artists believe an edition's size should not be increased after active sales have begun. ClearStudio does not prevent this practice. Proceed according to your own policies.";
		$policy_statement = $this->Html->para(NULL, "This edition is sold out.");
	}
}
?>
		
		<?= $policy_statement ? $policy_statement : ''; ?>
		<?= $this->Form->input("editions.$edition_index.quantity", [
			'default' => $default, 
			'label' => $label
		]); ?>