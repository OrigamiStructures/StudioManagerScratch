<!-- Element/Format/pieces.ctp -->
<?php 
$caption = 'Pieces in this format.';
$pieces = $format->pieces;
$providers = [$format];
$this->set(compact('caption', 'pieces', 'providers'));
?>

<?= $this->element('Pieces/overview_table'); ?>
<!-- END Element/Format/pieces.ctp -->
