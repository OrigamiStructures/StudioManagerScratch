<!-- Element/Format/image.ctp -->
<?php 
if ($format->image === NULL) {
	$image = $artwork->image == NULL ? "NoImage.png" : $artwork->image->fullPath;
	$attributes = ['class' => 'dup_image'];
} else {
	$image = $format->image->fullPath;
	$attributes = [];
}
?>
						<?= $this->Html->image($image, $attributes); ?>

						<!-- END Element/Format/image.ctp -->
