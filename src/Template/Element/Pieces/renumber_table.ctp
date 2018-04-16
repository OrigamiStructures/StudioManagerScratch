<!-- Element/Pieces/renumber_table.ctp -->
<?php
$edition = $providers['edition'];
if (!in_array($edition->type, \App\Lib\SystemState::limitedEditionTypes())) {
?>
	Only numbered editions may be renumbered.
<?php
} else {
?>
	
	
<table>
	<caption>
		<?= $caption ?>
	</caption>
	<thead>
		<tr>
			<th>New number</th>
			<th>Original number</th>
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
	
	
<?php } ?>
	
<!-- END Element/Edition/renumber_table.ctp -->
