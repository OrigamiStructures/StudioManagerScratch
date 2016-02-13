					<!-- Element/Edition/pieces.ctp -->
<?php 
$piece_element = $ArtStackElement->choosePieceTable($edition);
$EditionHelper->pieceTable($edition);
?>

					<section class="assignment">
						<?= $EditionHelper->pieceSummary($edition); ?>
					</section>
					<?= $this->element($piece_element); ?>
					<!-- END Element/Edition/pieces.ctp -->
