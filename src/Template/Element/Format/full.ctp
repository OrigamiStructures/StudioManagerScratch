<?php 
//$format_element = $this->ArtElement->choose('formatContent');
//$format_element = $elements[FORMAT_LAYER];

/**
 * focus controls visibility of the piece table. This might be done 
 *	with the piece table factory method instead.
 * 
 */
$class = ($SystemState->urlArgIsKnown('format')) ? ' focus' : '';
?>
						<!-- Element/Format/full.ctp -->
						<section class="format<?= $class; ?>">
							<?= $this->element($elements[FORMAT_LAYER]($format)); ?>
						</section>
						<!-- END Element/Format/full.ctp -->
