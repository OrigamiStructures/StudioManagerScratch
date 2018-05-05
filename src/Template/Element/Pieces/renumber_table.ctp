<!-- Element/Pieces/renumber_table.ctp -->
	
<table>
	<caption>
		<?= $caption ?>
	</caption>
	<thead>
		<tr>
			<th>New number</th>
			<th>Original number</th>
			<th>Format</th>
			<th>Assignment</th>
			<th>Dispositions</th>
<!--			<th>History</th>
			<th>Sold</th>-->
		</tr>
	</thead>
	<tbody>
		<?= $this->element('Pieces/renumber_rows'); ?>
	</tbody>
</table>
	
<!-- END Element/Edition/renumber_table.ctp -->
