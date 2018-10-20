<!-- Element/Edition/dispose_pieces_table.ctp -->
<table>
	<caption>
		<?= $caption ?>
	</caption>
	<thead>
		<tr>
			<?php 
			if (\App\Lib\EditionTypeMap::isNumbered($edition->type)) : ;
			?>
			<th>Num</th>
			<?php 
			endif;
			?>
			<th>Qty</th>
			<th>Owner</th>
			<th>History</th>
			<th>Sold</th>
			<th>Dispose</th>
		</tr>
	</thead>
	<tbody>
		<?= $this->element('Pieces/dispose_pieces_rows'); ?>
	</tbody>
</table>
<!-- END Element/Edition/dispose_pieces_table.ctp -->
