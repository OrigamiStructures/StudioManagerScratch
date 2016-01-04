<!-- Element/Images/fieldset.ctp -->
<?php 
$edition_count = isset($edition_count) ? $edition_count : 0 ; 
$format_count = isset($format_count) ? $format_count : 0 ; 
if ($SystemState->is(ARTWORK_REFINE)) {
	echo $this->element('Artwork/image');
}
?>
<fieldset>
    <?= $this->Form->input("image.id"); ?>
    <?= $this->Form->input("image.image", ['type' => 'file']); ?>
</fieldset>
