<?php 
//if (count($standing_disposition->addresses) > 1) {
//	$collection = new \Cake\Collection\Collection($standing_disposition->addresses);
//	$output = $collection->reduce(function($accumulator, $value){
//		$accumulator[] = ['value' => $value->id, 'text' => $value->address_line];
//		return $accumulator;
//	}, []);
//} else {
//	$value = array_shift($standing_disposition->addresses);
//	$output = $this->Html->tag('p', $value->address_line);
//}
foreach($standing_disposition->addresses as $address) :
?>

		<?php // is_string($output) ? $output : $this->Form->radio('address', $output);?>
		<p>
		<?= count($standing_disposition->addresses) == 1 
			? $this->Html->tag('p', $address->address_line) 
			: $this->Html->link($address->address_line, [
				'controller' => 'dispositions', 
				'action' => 'choose_address', 
				'?' => ['address' => $address->id]
			]);
		?>
		</p>

		
<?php
endforeach;
?>