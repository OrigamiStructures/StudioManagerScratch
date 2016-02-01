<?php
foreach($pieces as $piece) :
	if (!is_null($piece->format_id)) {
		$format = $edition[0]->returnFormat($piece->format_id);
		$owner_title = trim($format->display_title . ' Format');
	} else {
		$owner_title = $edition[0]->display_title;
	}
?>
	<tr>
		<td><?= $piece->number; ?></td>
		<td><?= $piece->quantity; ?></td>
		<td><?= $owner_title ?></td>
		<td><?= (boolean) $piece->disposition_count ? $piece->disposition_count . ' events' : '-'; ?></td>
		<td><?= $piece->collected ? 'Yes' : '-'; ?></td>
	</tr>
<?php
endforeach;
?>