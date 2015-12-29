<!-- Element/Images/fieldset.ctp -->
<?php 
$edition_count = isset($edition_count) ? $edition_count : 0 ; 
$format_count = isset($format_count) ? $format_count : 0 ; 
?>
<fieldset>
    <?= $this->Form->input("editions.$edition_count.formats.$format_count.images.id"); ?>
    <?= $this->Form->input("editions.$edition_count.formats.$format_count.images.image", ['type' => 'file']); ?>
</fieldset>
