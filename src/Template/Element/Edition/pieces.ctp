					<!-- Element/Edition/pieces.ctp -->
<?php 
$piece_element = $ArtStackElement->choosePieceTable($edition);
$EditionHelper->pieceTable($edition);
?>
					<button class="button tiny secondary">Reveal Pieces</button>
					<?= $this->element($piece_element); ?>
					<!-- END Element/Edition/pieces.ctp -->
