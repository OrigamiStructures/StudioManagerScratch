					<!-- Element/Edition/pieces.ctp -->
<?php 
$piece_element = $ArtStackElement->choosePieceTable($edition);
$EditionHelper->pieceTable($edition);
?>

					<?= $this->element($piece_element); ?>
					<!-- END Element/Edition/pieces.ctp -->
