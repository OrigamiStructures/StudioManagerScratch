<?php 
$pieces = $standing_disposition->pieces;
foreach ($pieces as $piece) :
?>

		<?= $piece->fullyIdentified() 
			? $this->Html->tag('para', $piece->identityLabel()) 
			: $this->Html->link($piece->identityLabel(), [
				'controller' => 'artworks', 
				'action' => 'review', 
				'?' => $piece->identityArguments()
			]);
		?>
		
<?php
endforeach;
?>