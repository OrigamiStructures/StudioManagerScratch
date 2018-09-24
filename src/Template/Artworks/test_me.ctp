<style>
	table, div.checkbox {
		font-size: 12px;
		
	}
	td, th {
		padding: 3px 12px;
	}
	div.checkbox {
		display: inline-block;
		width: 10rem;
	}
</style>
<?php
echo $this->Form->create();
?>
	<?= $this->Form->select('method', $methods, ['multiple' => 'checkbox']); ?>
	<?= $this->Form->input('start_date'); ?>
	<?= $this->Form->input('end_date'); ?>
	<?= $this->Form->button('submit'); ?>
<?php	
echo $this->Form->end();
?>



<?php
echo $this->element('Disposition/testing/dispo_table');
//osd($result->toArray());
//osd($combined);
//osd($stuff[0]());
//osd($stuff[1]('input val'));
//osd($new, 'new');
//osd($old, 'old');
?>
