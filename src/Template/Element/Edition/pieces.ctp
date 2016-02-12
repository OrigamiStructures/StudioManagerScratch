<!-- Element/Edition/pieces.ctp -->
<?php 
$caption = 'Pieces in this edtion that haven\'t been assigned to a format.';
$pieces = $edition->pieces;
$providers = [$edition] + $edition->formats; // CONCATENATION CAN BE REMOVED LATER WHEN 'WHERE' CLAUSE IS WORKING
$this->set(compact('caption', 'pieces', 'providers'));
?>

<?= $this->element('Pieces/overview_table'); ?>
<!-- END Element/Edition/pieces.ctp -->
