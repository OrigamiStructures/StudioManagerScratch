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
$edition_index = isset($edition_index) ? $edition_index : 0 ; 
$format_index = isset($format_index) ? $format_index : 0 ; 
?>
						<fieldset>
							<legend>Format Details</legend>
							<div class="text">
								<?= $this->Form->input("editions.$edition_index.formats.$format_index.id"); ?>
								<?= $this->Form->input("editions.$edition_index.formats.$format_index.edition_id", 
										['type' => 'hidden']); ?>

								<?= $this->Form->input("editions.$edition_index.formats.$format_index.description", 
										['placeholder' => 'Media, size and other format details']); ?>
							</div>
							<div class="pieces">
								<!-- no pieces during creation -->
							</div>
						</fieldset>
						<?php 
						/**
						 * PENDING FURTHER STUDY
						 * While this last form fieldset may not exist for some layer refinement 
						 * they always appear during creation. And since the 'unique' tailored creation 
						 * is actualy a refinement of a empty stubbed record, we'll need a special 
						 * cancel button to delete that stub. Other special buttons may turn up.
						 */
//						echo $ArtStackElement->choose('artFinalFormButtons'); 
						?>
						<?= $this->Form->submit('Submit', ['class' => 'button']); ?>
	<?php // osd($artwork); ?>
	
    <?php //  echo $this->Form->input("editions.$edition_index.formats.$format_index.range_flag"); ?>
    <?php //  echo $this->Form->input("editions.$edition_index.formats.$format_index.range_start"); ?>
    <?php //  echo $this->Form->input("editions.$edition_index.formats.$format_index.range_end"); ?>
    <?php //  echo $this->Form->input("editions.$edition_index.formats.$format_index.image_id", 
//			['type' => 'hidden']); ?>

