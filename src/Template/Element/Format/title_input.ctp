<?php
$render_title_input = TRUE;
if ($this->request->getParam('action') == 'refine') {
	if (\App\Lib\EditionTypeMap::isSingleFormat($edition->type) && empty($format->title)) {
		$render_title_input = FALSE;
	}
}

if ($render_title_input) :
?>

<?= $this->Form->input("editions.$edition_index.formats.$format_index.title",
			['placeholder' => 'Optional Format Title', 'label' => 'Format Title']); ?>

<?php
endif;
