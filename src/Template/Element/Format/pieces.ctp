<!-- Element/Format/pieces.ctp -->
<?php 
$piece_element = $ArtStackElement->choosePieceTable($format, $edition);
$EditionHelper->pieceTable($format, $edition);
?>

<button class="button tiny secondary">Reveal Pieces</button>
<?= $this->element($piece_element); ?>
<!-- END Element/Format/pieces.ctp -->
