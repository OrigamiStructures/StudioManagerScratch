
						<!-- Element/Format/full.ctp -->
<?php 
$format_element = $ArtStackElement->choose('formatContent');
?>
						<section class="format<?= $editing ? ' editing' : ''; ?>">
							<?= $this->element($format_element); ?>
						</section>
						<!-- END Element/Format/full.ctp -->
