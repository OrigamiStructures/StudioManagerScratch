			<section class="pieces">
				<?= $this->element('Disposition/piece_list'); ?>
			</section>

			<section class="disposition">
				<p><?= $standing_disposition->label ?></p>
			</section>

			<section class="member">
				<p>Recipient</p>
				<?= $this->element('Disposition/member'); ?>
			</section>

			<section class="address">
				<p>
					<?php if (count($disposition->addresses) > 1) : ?>
					Pending Addresses
					<?php else: ?>
					Address
					<?php endif; ?>
				</p>
				<?= $this->element('Disposition/address_list'); ?>
			</section>
