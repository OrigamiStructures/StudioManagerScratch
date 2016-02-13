					<!-- Element/Edition/pieces.ctp -->
<?php 
$piece_element = $ArtStackElement->choosePieceTable($edition);
$EditionHelper->pieceTable($edition);
if ($piece_element != 'empty') :
?>
					<button class="button tiny secondary">Reveal Pieces</button>
					<?php endif; ?>
					<?= $this->element($piece_element); ?>
					<!-- END Element/Edition/pieces.ctp -->
