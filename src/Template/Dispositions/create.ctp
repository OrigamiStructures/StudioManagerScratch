<div class="dispositions">
	<?= $this->Form->create($disposition); ?>
	<section class="disposition">
		
		<div class="left">
			<section class="event">
			<?php	
				echo $this->Form->input('id');
				//	echo $this->Form->input('label');
				echo $this->Form->input('label', ['empty' => 'Required choice']);
				echo $this->Form->input('name', ['placeholder' => 'Optional name for this placement']);
				echo $this->Form->input('disposition_type', ['type' => 'hidden']);
				echo $this->Form->input('complete');
				echo $this->Form->input('start_date');
			?>
			<?= $this->Form->submit('Submit', ['class' => 'button']); ?>
			</section>
		</div>
		
		<div class="right">
			<?= $this->element('Disposition/reference_panel'); ?>
		</div>
		
	</section>
	<?= $this->Form->end(); ?>
</div>
