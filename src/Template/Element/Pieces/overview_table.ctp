<!-- Element/Edition/overview_table.ctp -->
<table>
	<caption>
		<?= $caption ?>
	</caption>
	<thead>
		<tr>
			<?php 
			if (in_array($edition->type, \App\Lib\SystemState::limitedEditionTypes())) : ;
			?>
			<th>Num</th>
			<?php 
			endif;
			?>
			<th>Qty</th>
			<th>Dispositions</th>
<!--			<th>History</th>
			<th>Sold</th>-->
		</tr>
	</thead>
	<tbody>
		<?= $this->element('Pieces/overview_rows'); ?>
	</tbody>
</table>
<!-- END Element/Edition/overview_table.ctp -->
