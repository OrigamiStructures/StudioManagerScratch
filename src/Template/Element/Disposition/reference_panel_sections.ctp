			<section class="disposition">
				<h1><?= $this->DispositionTools->dispositionLabel($standing_disposition) ?></h1>
			</section>

<!--			<section class="member">
				<p>Recipient</p>
			</section>-->

			<section class="address">
				<h2>Recipient</h2>
				<?= $this->element('Disposition/member'); ?>
				<?= $this->DispositionTools->addressLabel($standing_disposition); ?>
				<?= $this->element('Disposition/address_list'); ?>
			</section>

			<section class="pieces">
				<h2>Pieces</h2>
				<?= $this->element('Disposition/piece_list'); ?>
			</section>

			<section class="prompt">
				<?= $this->Html->link('Save disposition', 
					['controller' => 'dispositions', 'action' => 'save'], 
					['class' => 'button']); ?>
				<?= $this->Html->link('Discard disposition', 
					['controller' => 'dispositions', 'action' => 'discard'], 
					['class' => 'button']); ?>
			</section>
