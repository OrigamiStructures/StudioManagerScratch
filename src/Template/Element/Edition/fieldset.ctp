<!-- Element/Edition/fieldset.ctp -->
<?php 
/**
 * This element can be called 'cold' with an Artworks base entity or called 
 * in a loop to present multiple Editions for the Artwork.  $edition_count 
 * must exist in the looping use  but is required for a 'cold' call.
 */
$edition_index = isset($edition_index) ? $edition_index : 0 ; 
?>
<fieldset>
	<legend>Edition Details</legend>
    <?= $this->Form->input("editions.$edition_index.id"); ?>
    <?= $this->Form->input("editions.$edition_index.artwork_id", 
			['type' => 'hidden']); ?>
    <?= $this->Form->input("editions.$edition_index.title", 
			['placeholder' => 'Optional Edition Title', 'label' => 'Edition Title']); ?>
	
    <?php
 if ($SystemState->is(ARTWORK_CREATE)) {
	 // I have a blanket 'disallowed' on type change after creation
	 // but it could be otherwise. fare
	 // Once dispositions start, edition types cannot be change because it 
	 // involves destroying pieces and possibly formats (?!)
	 // THIS NEEDS DISCUSSION
	 echo $this->Form->input("editions.$edition_index.type", ['options' => $types]);
 }	
	?>
	
    <?= $this->element('Edition/quantity_input'); // complex quantity input logic ?>
	
	<?php
 if ($SystemState->controller() !== 'formats' && $SystemState->is(ARTWORK_CREATE)) {
	 // do this for Artworks::create or Editions::create
	 // It allows the system to streamline piece creation in some cases
	 echo '<div><p>If this Edition has more than one Piece:</p>';
	 echo $this->Form->input('multiple', [
			'label' => 'Will the pieces will be made in multiple formats?',
			'type' => 'radio',
			'options' => [' No', ' Yes'],
			'default' => '0'
		]);
	 echo '</div>';

}	
	?>
</fieldset>
<?php 
				if (($SystemState->controller() === 'editions' ||
						$SystemState->controller() === 'artworks') && 
						$edition->format_count > 1) {
					echo $this->Form->submit();
				}
?>