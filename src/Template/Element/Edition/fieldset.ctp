<!-- Element/Edition/fieldset.ctp -->
<?php 
/**
 * This element can be called 'cold' with an Artworks base entity or called 
 * in a loop to present multiple Editions for the Artwork.  $edition_count 
 * must exist in the looping use  but is required for a 'cold' call.
 */
$format_index = isset($format_index) ? $format_index : 0 ; 
?>
<fieldset>
	<legend>Edition Details</legend>
    <?= $this->Form->input("editions.$format_index.id"); ?>
    <?= $this->Form->input("editions.$format_index.artwork_id", 
			['type' => 'hidden']); ?>
    <?= $this->Form->input("editions.$format_index.title", 
			['placeholder' => 'Optional Edition Title', 'label' => 'Edition Title']); ?>
    <?= $this->Form->input("editions.$format_index.type", ['options' => $types]); ?>
    <?= $this->Form->input("editions.$format_index.quantity", ['default' => 1]); ?>
</fieldset>
