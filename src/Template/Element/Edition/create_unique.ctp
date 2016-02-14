<!-- Element/Edition/fieldset.ctp -->
<?php 
/**
 * This element can be called 'cold' with an Artworks base entity or called 
 * in a loop to present multiple Editions for the Artwork.  $edition_count 
 * must exist in the looping use  but is required for a 'cold' call.
 */
$edition_index = isset($edition_index) ? $edition_index : 0 ; 
?>
<!--<fieldset>-->
    <?= $this->Form->input("editions.$edition_index.id"); ?>
    <?= $this->Form->input("editions.$edition_index.artwork_id", 
			['type' => 'hidden']); ?>
    <?= $this->Form->input("editions.$edition_index.title", 
			['type' => 'hidden']); ?>
	
    <?= $this->Form->input("editions.$edition_index.type", ['type' => 'hidden', 'default' => EDITION_UNIQUE]); ?>
	
    <?= $this->form->input("editions.$edition_index.quantity", ['type' => 'hidden', 'default' => 1]); // complex quantity input logic ?>
	
<!--</fieldset>-->
<?php 
				if (($SystemState->controller() === 'editions' ||
						$SystemState->controller() === 'artworks') && 
						$edition->format_count > 1) {
					echo $this->Form->submit('Submit', ['class' => 'button']);
				}
?>