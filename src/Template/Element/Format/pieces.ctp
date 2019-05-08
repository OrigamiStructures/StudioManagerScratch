					<!-- Element/Format/pieces.ctp -->
<?php 
// COUPLING ALERT
// EditionHelper sets $pieces and that variable 
// is used by ArtStackElement in the piece table choice logic
// HELPER CALL ORDER IS CRITICAL
$this->EditionFactory->concrete($edition->type)->pieceTable($format, $edition);
$piece_element = $this->ArtElement->choosePieceTable($format, $edition);
if ($piece_element != 'empty') :
?>
					<!--<button class="button tiny secondary">Reveal Pieces</button>-->
<?php endif; ?>
					<?= $this->element($piece_element); ?>
					<!-- END Element/Format/pieces.ctp -->
