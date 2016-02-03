<?php 
$render_title_input = TRUE;
//osd($SystemState->is(ARTWORK_REFINE), '$SystemState->is(ARTWORK_REFINE)');
if ($SystemState->is(ARTWORK_REFINE)) {
//	osd(!in_array($edition->type, [EDITION_LIMITED, EDITION_OPEN]), '!in_array($edition->type, [EDITION_LIMITED, EDITION_OPEN])');
//	osd($format->title, '$format->title');
	if (in_array($edition->type, SystemState::singleFormatEditionTypes()) && empty($format->title)) {
		$render_title_input = FALSE;
	}
}

if ($render_title_input) :
?>

<?= $this->Form->input("editions.$edition_index.formats.$format_index.title", 
			['placeholder' => 'Optional Format Title', 'label' => 'Format Title']); ?>

<?php
endif;
