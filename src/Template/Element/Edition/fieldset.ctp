<!-- Element/Edition/fieldset.ctp -->
<?php 
/**
 * This element can be called 'cold' with an Artworks base entity or called 
 * in a loop to present multiple Editions for the Artwork.  $edition_count 
 * must exist in the looping use  but is required for a 'cold' call.
 */
$edition_count = isset($edition_count) ? $edition_count : 0 ; 
?>
<fieldset>
    <?= $this->Form->input("editions.$edition_count.id"); ?>
    <?= $this->Form->input("editions.$edition_count.artwork_id", 
			['type' => 'hidden']); ?>
    <?= $this->Form->input("editions.$edition_count.title", 
			['placeholder' => 'Optional Edition Title', 'label' => 'Edition Title']); ?>
    <?= $this->Form->input("editions.$edition_count.type", ['options' => $types]); ?>
    <?= $this->Form->input("editions.$edition_count.quantity", ['default' => 1]); ?>
</fieldset>
