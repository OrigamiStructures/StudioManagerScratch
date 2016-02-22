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
				echo $this->Form->input('type', ['type' => 'hidden']);
				echo $this->Form->input('complete');
				echo $this->Form->input('start_date');
				
				if (empty($disposition->type)) {
					$options = ['type' => 'hidden'];
					
				} else {
					$options = [];
					$disposition->end_date = is_null($disposition->end_date) ? new DateTime('now') : $disposition->end_date ;
				}
				
				echo $this->Form->input('end_date', $options + ['empty' => TRUE]);
			?>
			<?= $this->DispositionTools->validationError('end_date', $errors); ?>
				
			<?= $this->Form->submit('Submit', ['class' => 'button']); ?>
			</section>
		</div>
		
		<div class="right">
			<div class="disposition">

				<?= $this->element('Disposition/reference_panel_sections'); ?>

			</div>
		</div>
		
	</section>
	<?= $this->Form->end(); ?>
</div>
