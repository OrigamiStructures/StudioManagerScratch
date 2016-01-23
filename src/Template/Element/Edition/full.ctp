
			<!-- Element/Edition/full.ctp -->
			<section class="edtion">
				<div class="columns small-12 medium-9 description">
					<?= $this->element('Edition/text'); ?>
					<section class="formats">
						<?php $this->set('formats', $edition->formats); 
						echo $this->element('Format/many'); ?>

					</section>
				</div>
			</section>
			<!-- END Element/Edition/full.ctp -->
