					<!-- Element/Format/pieces.ctp -->
<?php 
// COUPLING ALERT
// EditionHelper sets $pieces and that variable 
// is used by ArtStackElement in the table choice logic
// HELPER CALL ORDER IS CRITICAL
$EditionHelper->pieceTable($format, $edition);
$piece_element = $ArtStackElement->choosePieceTable($format, $edition);
if ($piece_element != 'empty') :
?>
					<button class="button tiny secondary">Reveal Pieces</button>
<?php endif; ?>
					<?= $this->element($piece_element); ?>
					<!-- END Element/Format/pieces.ctp -->
