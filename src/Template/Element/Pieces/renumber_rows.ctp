<!-- Element/Pieces/renumber_rows.ctp -->
<tr><td>
	<?= $this->Form->button('submit'); ?>
	<?= $this->Form->button('cancel', ['type' => 'submit', 'form' => 'cancel']); ?>
</td></tr>

<?php
$owners = new \Cake\Collection\Collection($providers);
$owner_title = $owners->reduce(function($accumulator, $owner) {
	$accumulator[$owner->key()] = $owner->display_title;
	return $accumulator;
}, []);
$count = 1;
$returning = isset($this->request->data['number']);

foreach($pieces as $piece) :
	?>
	<tr>
		<td><?= $this->Form->input("number[$piece->number]",[
			'label' => FALSE, 'id' => $piece->id, 'default' => NULL, 
			'value' => $returning ? $this->request->data['number'][$count++] : ''
				]); ?></td>
		<td><?= $piece->number; ?></td>
		<td>  <?= $owner_title[$piece->key()] ?></td>
		<td><?= (boolean) $piece->disposition_count ? $piece->disposition_count . ' events' : '-'; ?></td>
		<td><?= $piece->collected ? 'Yes' : '-'; ?> 
			<?= $this->element('Disposition/disposition_event_descriptions', ['piece' => $piece]); ?>
		</td>
	</tr>
<?php
endforeach;
?>
<!-- END Element/Pieces/renumber_rows.ctp -->
