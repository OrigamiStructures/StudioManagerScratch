<!-- Element/Format/image.ctp -->
<?php 
//osd($format);
if ($format->image === NULL) {
	$image = $artwork->image == NULL ? "NoImage.png" : $artwork->image->fullPath('medium');
	$attributes = ['class' => 'dup'];
} else {
	$image = $format->image->fullPath('medium');
	$attributes = [];
}
?>
						<?= $this->Html->image($image, $attributes); ?>

						<!-- END Element/Format/image.ctp -->
