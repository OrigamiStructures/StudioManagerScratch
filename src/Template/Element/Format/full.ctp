
						<!-- Element/Format/full.ctp -->
<?php 
$format_element = $ArtStackElement->choose('formatContent');
$class = ($editing ? ' editing' : '') . ($focus ? " $focus" : '');
?>
						<section class="format<?= $class; ?>">
							<?= $this->element($format_element); ?>
						</section>
						<!-- END Element/Format/full.ctp -->
