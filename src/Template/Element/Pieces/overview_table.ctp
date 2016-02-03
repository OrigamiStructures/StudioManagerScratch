<!-- Element/Edition/overview_table.ctp -->
<table>
	<caption>
		Pieces in this edition
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
			<th>Owner</th>
			<th>History</th>
			<th>Collected</th>
		</tr>
	</thead>
	<tbody>
		<?= $this->element('Pieces/overview_rows'); ?>
	</tbody>
</table>
<!-- END Element/Edition/overview_table.ctp -->
