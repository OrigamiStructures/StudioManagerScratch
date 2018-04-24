
						<!-- Element/Format/full.ctp -->
<?php 
$focus = isset($focus) ? $focus : '';
$format_element = $ArtStackElement->choose('formatContent');
$class = ($editing ? ' editing' : '') . $focus;
?>
						<section class="format<?= $class; ?>">
							<?= $this->element($format_element); ?>
						</section>
						<!-- END Element/Format/full.ctp -->
