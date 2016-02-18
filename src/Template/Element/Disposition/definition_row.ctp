<?php 
if(empty($standing_disposition->label)) {
	$DispositionTable = Cake\ORM\TableRegistry::get('Dispositions');
	$disposition_label = $DispositionTable->disposition_label;
//	osd($disposition_label);
}
?>	
	<div class="disposition">
		<section class="pieces">
			<!--<span class="warning badge">!!!</span>-->
			<?= $this->element('Disposition/piece_list'); ?>
		</section>
		<section class="disposition">
			<!--<span class="warning badge">!!!</span>-->
			<?= $this->Form->select('disposition_label', $disposition_label, ['empty' => 'Choose a disposition']); ?>
			<?= $this->Form->input('disposition_type', ['type' => 'hidden']); ?>
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
		<section class="prompt">
			<?= $this->Html->link('Discard disposition', ['controller' => 'dispositions', 'action' => 'discard']); ?>
		</section>
		
	</div>
