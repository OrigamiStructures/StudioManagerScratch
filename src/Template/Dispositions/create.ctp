<div class="dispositions">
	<?= $this->Form->create($disposition); ?>
	<section class="disposition">
		<div class="left">
			<section class="event">
			<?php	

				echo $this->Form->input('id');
				//	echo $this->Form->input('label');
				echo $this->Form->input('label', ['empty' => 'Required choice']);
//				echo $this->Form->input('name', ['placeholder' => 'Optional: Name to use instead of the label above.']);
				echo $this->Form->input('disposition_type', ['type' => 'hidden']);
				echo $this->Form->input('complete');
				echo $this->Form->input('start_date');

			// osd($disposition); 
			?>
			<?= $this->Form->submit('Submit', ['class' => 'button']); ?>
			</section>
		</div>
		<div class="right">
			<section class="pieces">
				<!--<span class="warning badge">!!!</span>-->
				<?= $this->element('Disposition/piece_list'); ?>
			</section>
			<section class="disposition">
				<!--<span class="warning badge">!!!</span>-->
				<p><?= $standing_disposition->label ?></p>
			</section>
			<section class="member">
				<!--<span class="warning badge">!!!</span>-->
				<p>Recipient</p>
				<?= $this->element('Disposition/member'); ?>
			</section>
			<section class="address">
				<!--<span class="warning badge">!!!</span>-->
				<p>
					<?php if (count($standing_disposition->addresses) > 1) : ?>
					Click the address you want to keep.
					<?php else: ?>
					Address
					<?php endif; ?>
				</p>
				<?= $this->element('Disposition/address_list'); ?>
			</section>
		</div>
	</section>
	<?= $this->Form->end(); ?>
</div>
