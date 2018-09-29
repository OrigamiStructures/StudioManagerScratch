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
	<?= $this->Form->input('first_start_date'); ?>
	<?= $this->Form->input('second_start_date'); ?>
	<?= $this->Form->input('first_end_date'); ?>
	<?= $this->Form->input('second_end_date'); ?>
	<?= $this->Form->button('submit'); ?>
<?php	
echo $this->Form->end();
?>



<?php
echo $this->element('Disposition/testing/dispo_table');
//osd($pieces->toArray());
//$t = new OSDTImer();
//$t->start();
$edsets = new \App\Model\Lib\IdentitySets('Editions', $pieces->toArray());
$editions = $edsets->query();
//osd($editions);
$artsets = new \App\Model\Lib\IdentitySets('Artworks', $editions);
//$artsets = 
//osd($t->result());
//osd($sets->count());
//$idList = array_count_values($sets->merge());
//
//osd($idList);
osd($artsets->query());
//osd($sets);

?>
