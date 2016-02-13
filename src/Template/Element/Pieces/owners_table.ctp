<!-- Element/Edition/owners_table.ctp -->
<?php 
//osd($pieces);die;
/**
 * For use when the pieces table is show in an Editon or Format context and 
 * the only pieces displayed are the ones for that context
 */
?>
<table>
	<caption>
		<?= $caption ?>
	</caption>
	<thead>
		<tr>
			<?php 
			if (in_array($edition->type, \App\Lib\SystemState::limitedEditionTypes())) : ;
			?>
			<th>Number</th>
			<?php 
			endif;
			?>
			<th>Quantity</th>
			<th>History</th>
			<th>Sold</th>
		</tr>
	</thead>
	<tbody>
		<?= $this->element('Pieces/owners_rows'); ?>
	</tbody>
</table>
<!-- END Element/Edition/owners_table.ctp -->
