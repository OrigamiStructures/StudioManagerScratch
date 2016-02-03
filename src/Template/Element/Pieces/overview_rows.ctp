<!-- Element/Edition/overview_rows.ctp -->
<?php
$owners = new \Cake\Collection\Collection($providers);
$owner_title = $owners->reduce(function($accumulator, $owner) {
	$accumulator[$owner->key()] = $owner->display_title;
	return $accumulator;
}, []);

foreach($pieces as $piece) :
?>
	<tr>
		<td><?= $piece->number; ?></td>
		<td><?= $piece->quantity; ?></td>
		<td><?= $owner_title[$piece->key()] ?></td>
		<td><?= (boolean) $piece->disposition_count ? $piece->disposition_count . ' events' : '-'; ?></td>
		<td><?= $piece->collected ? 'Yes' : '-'; ?></td>
	</tr>
<?php
endforeach;
?>
<!-- END Element/Edition/overview_rows.ctp -->
