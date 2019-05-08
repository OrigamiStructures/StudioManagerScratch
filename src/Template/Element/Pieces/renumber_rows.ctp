<!-- Element/Pieces/renumber_rows.ctp -->
<tr>
	<td colspan="2">
	<?php
	if ($messagePackage) :
		if ($messagePackage->errors()) { 
			$s = $messagePackage->errorCount() === 1 ? '' : 's';
			echo "<p class='error'>Correct the {$messagePackage->errorCount()} error$s below.</p>";
		} 
		if ($messagePackage->summaries()) {
			foreach ($messagePackage->summaries() as $message) {
				echo "<p>$message</p>";
			}
		}
	endif 
	?>
	</td>
	<td colspan="3">
	<?php if ($messagePackage && !$messagePackage->errors()) : ?>
	<?= $this->Form->button('approve', [
		'type' => 'submit', 'form' => 'cancel_renumber', 'class' => 'button success small']); ?>
	<?php endif; ?>
	<?= $this->Form->button(($messagePackage ? 'Re-submit' : 'Submit'), ['class' => 'button standard small']); ?>
	<?= $this->Form->button('Cancel', [
		'type' => 'submit', 'form' => 'cancel_renumber', 'class' => 'button warning small']); ?>

</td></tr>

<?php
$error = $messagePackage ? $messagePackage->errors() : false ;
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
		<td>  <?= $providers->title($piece->key()) ?></td>
		
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
