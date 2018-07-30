<!-- Element/Format/summary.ctp -->
            <?php
$q = [
	'controller' => 'formats', 
	'?' => [
		'artwork' => $artwork->id,
		'edition' => $edition->id,
		'format' => $format->id,
	]];
$l = $this->ArtStackTools->inlineReviewRefine($q);
?>
<?php
                $count = count($formats);
                $word = ($count > 1) ? 'formats' : 'format';
                echo $this->Html->tag('p', "<!-- contains $count $word -->" );
//				echo "<div></div>";
                echo $this->Html->tag('p', $l . $format->displayTitle );
            ?>
