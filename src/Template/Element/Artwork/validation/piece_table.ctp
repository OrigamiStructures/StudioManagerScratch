<table>
	<thead>
		<tr>
			<th>IDs</th>
			<th>Number</th>
			<th>Quantity</th>
			<th>disposition_count</th>
			<th>collected</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($pieces as $piece) : ?>
		<tr>
			<td><?= "$piece->id/$piece->edition_id/$piece->format_id"; ?></td>
			<td><?= $piece->number; ?></td>
			<td><?= $piece->quantity; ?></td>
			<td><?= $piece->disposition_count; ?></td>
			<td><?= $piece->collected; ?></td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>
