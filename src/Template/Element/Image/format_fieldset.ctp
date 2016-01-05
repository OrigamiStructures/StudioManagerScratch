<!-- Element/Images/fieldset.ctp -->
<?php 
$edition_count = isset($edition_count) ? $edition_count : 0 ; 
$format_count = isset($format_count) ? $format_count : 0 ; 
if ($SystemState->is(ARTWORK_REFINE)) {
	$this->set('format', $artwork['editions'][$edition_count]['formats'][$format_count]);
	echo $this->element('Format/image');
}
?>
<fieldset>
    <?= $this->Form->input("editions.$edition_count.formats.$format_count.image.id"); ?>
    <?= $this->Form->input("editions.$edition_count.formats.$format_count.image.image", ['type' => 'file']); ?>
</fieldset>
