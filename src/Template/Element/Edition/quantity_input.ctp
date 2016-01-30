<?php
$policy_statement = FALSE;
$allow_quantity = TRUE;

if ($SystemState->is(ARTWORK_CREATE)) {
/**
 * Establish values for CREATE version of the input
 */

	$default = 1;
	$label = 'Edition Size';
	
/**
 * Establish values REFINEMENT of multiple-format types
 */
} elseif (in_array($edition->type, $this->SystemState->multipleFormatEditionTypes())) {
	
	$EditionTable = Cake\ORM\TableRegistry::get('Editions');
	$minimum = $EditionTable->minimumSize($edition);
		
	$label = ($edition->hasFluid() || $edition->hasUnassigned() ? 'Reduce or ' : '') .
			"Change the edition size (minimum size $minimum):";
	$default = $edition->quantity;
	
	if ($edition->type === EDITION_LIMITED & $edition->hasCollected()) {
		$pieces = $edition->collected_piece_count === 1 ?
				'one piece' :
				"$edition->collected_piece_count pieces";
		$policy = "You have sold $pieces from this edition.<br />Most artists "
				. "believe an edition's size should not be increased after active "
				. "sales have begun. ClearStudio does not prevent this practice. "
				. "Proceed according to your own policies.";
		$policy_statement = $this->Html->para('policy_statement', $policy);
	}

/**
 * Establish values REFINEMENT of single-format types
 */
} else {
	$allow_quantity = FALSE;
}

if ($allow_quantity) :
?>
		
		<?= $policy_statement ? $policy_statement : ''; ?>
		<?= $this->Form->input("editions.$edition_index.quantity", [
			'default' => $default, 
			'label' => $label
		]); ?>

<?php 
endif;
?>