<?php 
$pieces = $standing_disposition->pieces;
foreach ($pieces as $piece) :
?>

		<p>
		<?= $this->DispositionTools->identity($piece); ?>
		</p>
		
<?php
endforeach;
?>