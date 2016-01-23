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
    <?= $this->Form->input("editions.$edition_index.type", ['options' => $types]); ?>
	
    <?php
	/**
	 * This needs to respond to new Edition quantity change rules
	 */
	if ($SystemState->is(ARTWORK_CREATE)) {
		echo $this->Form->input("editions.$edition_index.quantity", ['default' => 1]);
	} elseif (!$this->isUnique($artwork->editions[$edition_index])) {
		$factory = $this->loadHelper('EditionFactory');
		$helper = $factory->load($edition->type);
		echo $this->Html->para(NULL, $helper->editionQuantitySummary($edition));
	}
	?>
	
	<?php
 if ($SystemState->controller() !== 'formats' && $SystemState->is(ARTWORK_CREATE)) {
	 // do this for Artworks::create or Editions::create
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
