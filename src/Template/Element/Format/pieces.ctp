<!-- Element/Format/pieces.ctp -->
<?php 
//if (!isset($PieceHelper)){
//	$PieceHelper = $this->loadHelper('PieceTable');
//	$this->set('PieceHelper', $PieceHelper);
//}
$caption = 'Pieces in this format.';
//$pieces = $format->pieces;
$pieces = $EditionHelper->pieceTool()->filter($format->pieces, 'format');
$providers = [$format];
$this->set(compact('caption', 'pieces', 'providers'));
?>

<?= $this->element('Pieces/overview_table'); ?>
<!-- END Element/Format/pieces.ctp -->
