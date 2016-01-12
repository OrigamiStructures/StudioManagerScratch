<!-- Element/Format/fieldset.ctp -->
<?php 
/**
 * This element can be called 'cold' with an Artworks base entity 
 * or called in a loop to present multiple Formats for each Edition. 
 * Both $edition_count and $format_count must exist in the looping use 
 * but neither is required for a 'cold' call.
 * If it's called cold, the Artwork might contain multiple Editions 
 * and multiple formats, in that case, we need to choose the proper one 
 * to render. That should be accomplished by scanning for the proper 
 * Format and setting $format_count to its index. BUT HOW IS THIS WORKING 
 * FOR 'CREATE'? I'M A BIT CONFUSED.
 */
osd($SystemState->now());die;
if ($SystemState->is('ARTWORK_REFINE')) {
	$edition_id = $SystemState->queryArg('edition');
	$format_id = $SystemState->queryArg('format');
	
	if (!isset($edition_count) && !is_null($edition_id)) {
		$edition_max = count($artwork['editions']);
		$edition_count = 0;
		while ($artwork['editions'][$edition_count]['id'] != $SystemState->queryArg('edition') && $edition_count < $edition_max) {
			$edition_count++;
		}
	}
	
	if (!isset($format_count) && !is_null($format_id)) {
		$format_max = count($artwork['editions'][$edition_count]['formats']);
		$format_count = 0;
		while ($artwork['editions'][$edition_count]['formats']['id'] != $SystemState->queryArg('format') && $format_count < $format_max) {
			$edition_count++;
		}
	}
}

?>
<fieldset>
	<legend>Format Details</legend>
	<?php osd($artwork); ?>
    <?= $this->Form->input("editions.$edition_count.formats.$format_count.id"); ?>
    <?= $this->Form->input("editions.$edition_count.formats.$format_count.edition_id", 
			['type' => 'hidden']); ?>
    <?= $this->Form->input("editions.$edition_count.formats.$format_count.title", 
			['placeholder' => 'Optional Format Title', 'label' => 'Format Title']); ?>
    <?= $this->Form->input("editions.$edition_count.formats.$format_count.description", 
			['placeholder' => 'Media, size and other format details']); ?>
    <?= $this->Form->input("editions.$edition_count.formats.$format_count.range_flag"); ?>
    <?= $this->Form->input("editions.$edition_count.formats.$format_count.range_start"); ?>
    <?= $this->Form->input("editions.$edition_count.formats.$format_count.range_end"); ?>
    <?= $this->Form->input("editions.$edition_count.formats.$format_count.image_id", 
			['type' => 'hidden']); ?>
</fieldset>
<?= $this->element('Image/format_fieldset'); ?>
