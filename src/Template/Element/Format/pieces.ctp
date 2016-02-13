<!-- Element/Format/pieces.ctp -->
<?php 
$piece_element = $ArtStackElement->choosePieceTable($format, $edition);
$EditionHelper->pieceTable($format, $edition);
?>

<?= $this->element($piece_element); ?>
<!-- END Element/Format/pieces.ctp -->
