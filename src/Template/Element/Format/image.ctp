<!-- Element/Format/image.ctp -->
<?php 
//osd($format);
if ($format->image === NULL) {
	$image = $artwork->image == NULL ? "NoImage.png" : $artwork->image->fullPath;
	$attributes = ['class' => 'dup'];
} else {
	$image = $format->image->fullPath;
	$attributes = [];
}
?>
						<?= $this->Html->image($image, $attributes); ?>

						<!-- END Element/Format/image.ctp -->
