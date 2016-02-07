			<!-- Element/Edition/full.ctp -->
			<section class="edition">
				<div class="text">
					<?= $this->element('Edition/text'); ?>
				</div>
				<div class="pieces">
					<?= $this->element('Edition/pieces'); ?>
				</div>
				<div class="formats">
					<?php $this->set('formats', $edition->formats); ?>
					<?= $this->element('Format/many'); ?>
				</div>
			</section>
			<!-- END Element/Edition/full.ctp -->
