	<div class="disposition">
		<section class="pieces">
			<span class="warning badge">!!!</span>
			<p>Pieces</p>
		</section>
		<section class="disposition">
			<span class="warning badge">!!!</span>
			<?= $this->Form->input('disposition_type'); ?>
		</section>
		<section class="member">
			<span class="warning badge">!!!</span>
			<p>Member</p>
		</section>
		<section class="address">
			<span class="warning badge">!!!</span>
			<p>Address</p>
		</section>
		<section class="prompt">
			<?= $this->Html->link('Discard disposition', ['controller' => 'dispositions', 'action' => 'discard']); ?>
		</section>
		
	</div>
