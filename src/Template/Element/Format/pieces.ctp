					<!-- Element/Format/pieces.ctp -->
<?php 
$piece_element = $ArtStackElement->choosePieceTable($format, $edition);
$EditionHelper->pieceTable($format, $edition);
if ($piece_element != 'empty') :
?>
					<button class="button tiny secondary">Reveal Pieces</button>
					<?php endif; ?>
					<?= $this->element($piece_element); ?>
					<!-- END Element/Format/pieces.ctp -->
