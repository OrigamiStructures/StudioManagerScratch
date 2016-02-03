<table>
	<caption>
		Pieces in this edition
	</caption>
	<thead>
		<tr>
			<?= $helper->pieceNumberColumn($edtion->type, 'header') ?><!--<th>Number</th>-->
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
