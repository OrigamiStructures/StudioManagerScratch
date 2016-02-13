<!-- Element/Edition/pieces.ctp -->
<?php 
//$caption = 'Pieces in this edtion that haven\'t been assigned to a format.';
//$pieces = $EditionHelper->pieceTool()->filter($edition->pieces, 'edition');
//$providers = ['edition' => $edition] + $edition->formats; // CONCATENATION CAN BE REMOVED LATER WHEN 'WHERE' CLAUSE IS WORKING
//$this->set(compact('caption', 'pieces', 'providers', 'PieceHelper'));
$EditionHelper->pieceTable($edition);
?>

<?= $this->element('Pieces/overview_table'); ?>
<!-- END Element/Edition/pieces.ctp -->
