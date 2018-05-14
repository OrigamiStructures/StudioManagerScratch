<!-- Element/Pieces/renumber_rows.ctp -->
<tr><td>
	<?= $this->Form->button(($messagePackage ? 'Re-submit' : 'Submit'), ['class' => 'button']); ?>
	<?= $this->Form->button('Cancel', ['type' => 'submit', 'form' => 'cancel', 'class' => 'button']); ?>
</td></tr>

<?php
$error = $messagePackage ? $messagePackage->errors() : false ;
$owners = new \Cake\Collection\Collection($providers);
$owner_title = $owners->reduce(function($accumulator, $owner) {
	$accumulator[$owner->key()] = $owner->display_title;
	return $accumulator;
}, []);
$count = 1;
$returning = isset($this->request->data['number']);

foreach($pieces as $piece) :
	$field_error = $error ? array_key_exists($piece->number, $error) : $error;
	?>
	<tr>
		<td><?= $this->Form->input("number[$piece->number]",[
			'label' => FALSE, 'id' => $piece->id, 'default' => NULL, 
			'value' => $returning ? $this->request->data['number'][$count++] : '',
			'class' => $field_error ? 'form_error' : '',
				]); ?>
		<?php
		if ($field_error) {
			echo "<div class=\"error_message\">" . implode('<br>' , $error[$piece->number]) . '</div>';
		}
		 
		?>
		</td>
		<td><?= $piece->number; ?></td>
		<td>  <?= $owner_title[$piece->key()] ?></td>
		
		<?php 
		$c = $piece->disposition_count;
		$plural = ($c > 1) ? 's' : '';
		?>
		<td><?= (boolean) $c ? "$c event$plural" : '-'; ?></td>
		
		<td><?= $piece->collected ? 'Yes' : '-'; ?> 
			<?= $this->element('Disposition/disposition_event_descriptions', ['piece' => $piece]); ?>
		</td>
	</tr>
<?php
endforeach;
?>
<!-- END Element/Pieces/renumber_rows.ctp -->
