<?php 
$format_element = $ArtStackElement->choose('formatContent');

/**
 * format_focus controls visibility of the piece table. This might be done 
 *	with the piece table factory method instead.
 * 
 */
$class = ($SystemState->urlArgIsKnown('format')) ? ' format_focus' : '';
?>
						<!-- Element/Format/full.ctp -->
						<section class="format<?= $class; ?>">
							<?= $this->element($format_element); ?>
						</section>
						<!-- END Element/Format/full.ctp -->
