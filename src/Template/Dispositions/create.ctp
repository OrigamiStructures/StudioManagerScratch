<?= $this->Form->create($disposition); ?>
	
<?php	
	echo $this->Form->input('id');
//	echo $this->Form->input('label');
	echo $this->Form->input('label', ['empty' => 'Choose a disposition']);
	echo $this->Form->input('disposition_type', ['type' => 'hidden']);
osd($labels);
 osd($disposition); 
?>

<?= $this->Form->submit(); ?>
<?= $this->Form->end(); ?>
