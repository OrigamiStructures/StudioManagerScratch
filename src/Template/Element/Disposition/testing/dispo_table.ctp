<?php
	$columns = ['id', 'created', 'start_date', 'end_date', 'type', 
		'label', 'name', 'complete', 'disposition_id', 'first_name', 'city', 'state'];
		
?>
<table>
	<tbody>
		<?= $this->Html->tableHeaders($columns) ?>
		<?php foreach ($result as $disposition): 
			$properties = array_intersect_key($disposition->properties(), array_flip($columns));
//		osd($properties);
//			$disposition = new \ArrayObject($disposition);
//			osd($disposition);
		?>
		<?= $this->Html->tableCells($properties) ?>
		<?php endforeach; ?>
	</tbody>
</table>
