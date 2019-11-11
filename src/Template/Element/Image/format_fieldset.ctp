<!-- Element/Images/fieldset.ctp -->
<?php 
$render_image_input = TRUE;
if ($this->request->getParam('action') == 'refine') {
	//
	if (\App\Lib\EditionTypeMap::isSingleFormat($edition->type)) {
		$render_image_input = FALSE;
	}
}

if ($render_image_input) :
?>

		<?php 
		$edition_index = isset($edition_index) ? $edition_index : 0 ; 
		$format_index = isset($format_index) ? $format_index : 0 ; 
		if ($this->request->getParam('action') == 'refine') {
			$this->set('format', $artwork['editions'][$edition_index]['formats'][$format_index]);
			echo $this->element('Format/image');
		}
		?>
		<fieldset>
			<?= $this->Form->input("editions.$edition_index.formats.$format_index.image.id"); ?>
			<?= $this->Form->input("editions.$edition_index.formats.$format_index.image.image_file", ['type' => 'file']); ?>
		</fieldset>

<?php
endif;
?>


<!-- END Element/Images/fieldset.ctp -->
