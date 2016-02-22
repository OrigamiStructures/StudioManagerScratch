			<section class="disposition">
				<p><?= $standing_disposition->label ?></p>
			</section>

			<section class="member">
				<p>Recipient</p>
				<?= $this->element('Disposition/member'); ?>
			</section>

			<section class="address">
				<?= $this->DispositionTools->addressLabel($standing_disposition); ?>
				<?= $this->element('Disposition/address_list'); ?>
			</section>

			<section class="pieces">
				<?= $this->element('Disposition/piece_list'); ?>
			</section>

