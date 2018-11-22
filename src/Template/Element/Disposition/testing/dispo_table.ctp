<?php
	$columns = ['id', 'created', 'start_date', 'end_date', 'type', 
		'label', 'name', 'complete', 'disposition_id', 'first_name', 'city', 'state'];
		
?>
<table>
	<?php if(is_object($result)) : ?>
	<caption><?= 'Records found: ' . $dispLayer->count(); ?></caption>
	<tbody>
		<?= $this->Html->tableHeaders($columns) ?>
		<?php //$pieceLists = [];
		foreach ($dispLayer->load('all') as $disposition): 
//			$pieceLists[$disposition->id] = 
//				new \App\Model\Lib\IdentitySet($disposition, 'pieces');
			$properties = array_intersect_key($disposition->properties(), array_flip($columns));
			$properties['disposition_id'] = $disposition->pieceCount();
		?>
		<?= $this->Html->tableCells($properties) ?>
		<?php endforeach; ?>
	</tbody>
	<?php endif; ?>
</table>
<?php
//osd($pieceLists);
