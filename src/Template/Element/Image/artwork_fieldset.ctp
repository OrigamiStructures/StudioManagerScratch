<!-- Element/Images/fieldset.ctp -->
<?php
$this->append('script');
echo $this->Html->script('dropzone');
$this->end();
$this->append('css');
echo $this->Html->css('dropzone');
$this->end();

$edition_count = isset($edition_count) ? $edition_count : 0 ;
$format_count = isset($format_count) ? $format_count : 0 ;
if ($this->request->getParam('action') == 'refine') {
	echo $this->element('Artwork/image');
}
?>
<fieldset class="artwork-image dz-message dropzone">
	<div class="dropzone-previews"></div> <!-- this is were the previews should be shown. -->
    <?= $this->Form->input("image.id"); ?>
    <?= $this->Form->input("image.image_file", ['type' => 'file', 'class' => 'fallback']); ?>
</fieldset>
