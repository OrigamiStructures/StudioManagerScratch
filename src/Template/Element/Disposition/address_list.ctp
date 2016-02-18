<?php foreach($standing_disposition->addresses as $address) : ?>

		<?php // is_string($output) ? $output : $this->Form->radio('address', $output);?>
		<p>
		<?= $this->DispositionTools->identity($address); ?>
		</p>

		
<?php endforeach; ?>