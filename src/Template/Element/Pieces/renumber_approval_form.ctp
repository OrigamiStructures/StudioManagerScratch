		<?php if($messagePackage && !$messagePackage->errors()) : ?>
	<?= $this->Form->create('Pieces', ['id' => 'approve_renumber']); ?>
		<?= $this->Form->input('do_move', ['value' => TRUE, 'type' => 'hidden']); ?>
	<?= $this->Form->end(); ?>
		<?php endif; ?>
