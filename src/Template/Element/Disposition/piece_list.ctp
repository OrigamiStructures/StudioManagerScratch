<?php 
$pieces = $standing_disposition->pieces;
foreach ($pieces as $piece) :
?>

		<p>
		<?= $piece->fullyIdentified() 
			? $piece->identityLabel() 
			: $this->Html->link($piece->identityLabel(), [
				'controller' => 'artworks', 
				'action' => 'review', 
				'?' => $piece->identityArguments()
			]);
		?>
		</p>
		
<?php
endforeach;
?>