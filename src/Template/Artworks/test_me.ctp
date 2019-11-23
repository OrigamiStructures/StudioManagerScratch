<style>
    table, div.checkbox {
        font-size: 12px;
    }
    td, th {
        padding: 3px 12px;
    }
    div.checkbox {
        display: inline-block;
        width: 10rem;
    }
</style>
<?php
//$log = new App\SiteMetrics\ProcessLogs();
//osd($log);
echo $this->Form->create();
?>
    <?= $this->Form->select('method', $methods, ['multiple' => 'checkbox']); ?>
    <?= $this->Form->input('first_start_date'); ?>
    <?= $this->Form->input('second_start_date'); ?>
    <?= $this->Form->input('first_end_date'); ?>
    <?= $this->Form->input('second_end_date'); ?>
    <?= $this->Form->button('submit'); ?>
<?php
echo $this->Form->end();
?>

<?php
use App\Model\Lib\Layer;

echo $this->element('Disposition/testing/dispo_table');
if (isset($stacks)) {
	foreach ($stacks->getData() as $stack) {
	    /* @var \App\Model\Entity\ArtStack $stack */

		$artwork = $stack->rootElement();
		$joinArray = $stack
            ->getLayer('dispositions_pieces')
            ->find()
            ->specifyFilter('disposition_id', $activity->IDs())
            ->toArray();
		$joinLayer = new Layer($joinArray);

		$pieces = $stack
            ->getLayer('pieces')
            ->find()
            ->specifyFilter(
                    'id',
                    $joinLayer->toDistinctList('piece_id'),
                    'in_array')
            ->toLayer();

		$formatIDs = $pieces->toDistinctList('format_id');

		$formats = $stack
            ->getLayer('formats')
            ->find()
            ->specifyFilter('id', $formatIDs)
            ->toLayer();

		$editionIDs = $formats->toDistinctList('edition_id');
//		$formats = new Layer($formats, 'editions_format');

		$editions = $stack
            ->getLayer('editions')
            ->find()
            ->specifyFilter('id', $editionIDs)
            ->toArray();
		$editions = new Layer($editions, 'editions');

        echo "<h1>{$artwork->title}</h1>";
//		$allInLayer = $editions->accessArgs()->setLimit('all');
        foreach ($editions->toArray() as $edition) {
            echo "<h2>{$edition->displayTitle}</h2>";
            foreach ($formats->toArray() as $format) {
                echo "<h3>{$format->displayTitle}</h3>";
				$assignedPieces = $pieces
                    ->getLayer()
                    ->find()
                    ->specifyFilter('format_id', $format->id)
                    ->toArray();
				foreach ($assignedPieces as $piece) {
					echo '<ul><li>' . $piece->displayTitle . '<ul>';
					$pieceActivity = $joinLayer
                        ->getLayer()
                        ->find()
                        ->specifyFilter('piece_id', $piece->id)
                        ->toArray();
					foreach ($pieceActivity as $link) {
						echo "<li>"
						. "{$activity->element($link->disposition_id, LAYERACC_ID)->displayTitle}"
						. "</li>";
					}
					echo '</ul></li></ul>';
               }
            }
        }
    }
}
//die;
echo '<h1>Reverse Formatting Piece Lines</h1>';
if (isset($stacks)) {

	foreach ($activity->toArray() as $dispId => $disposition) {
		$joinArray = $stacks->getLayer('dispositions_pieces')
            ->find()
            ->specifyFilter('disposition_id', $dispId)
            ->toArray();
		$joinLayer = new Layer($joinArray, 'dispositions_pieces');

        $pieces = $stacks->getLayer('pieces')
            ->find()
            ->specifyFilter(
                'id',
                $joinLayer->toDistinctList('piece_id'),
                'in_array')
            ->specifySort('format_id',\SORT_DESC, \SORT_NUMERIC)
            ->toArray();

		echo '<h3>' . $disposition->displayTitle . " (id: $disposition->id)" . '</h3><ul>';
        foreach ($pieces as $piece) {

			$stack = $stacks->ownerOf('pieces', $piece->id)[0];
			$format = $stack->formats->element($piece->format_id, LAYERACC_ID);
			if(is_null($format)) {
				osd($stack->formats->IDs(), "Piece format $piece->format_id");
			}
			$edition = $stack->editions->element($piece->edition_id, LAYERACC_ID);
			$artwork = $stack->rootElement();

			echo '<li>' . ucfirst($piece->displayTitle) . ' from ' .
                $artwork->title . ', ' .
                $edition->displayTitle . ', ' .
//                $format->displayTitle .
                '</li>';
        }
        echo '</ul></li></ul>';
    }
}
?>
