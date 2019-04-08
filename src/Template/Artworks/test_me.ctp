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
	foreach ($stacks->all() as $stack) {
		
		$artwork = $stack->primaryEntity();
		$joinArray = $stack->find()
				->setLayer('dispositionsPieces')
				->specifyFilter('disposition_id', $activity->IDs())
				->load();
		$joinLayer = new Layer($joinArray);
		
		$distinct_pieces = $stack->find()
				->setLayer('pieces')
				->specifyFilter(
						'id', 
						$joinLayer->distinct('piece_id'), 
						'in_array')
				->load();
		
		$formatIDs = $stack->distinct('format_id', $distinct_pieces);
		$pieces = new Layer($distinct_pieces);
		
		$formats = $stack->find()
				->setLayer('formats')
				->specifyFilter('id', $formatIDs)
				->load();
		
		$editionIDs = $stack->distinct('edition_id', $formats);
		$formats = new Layer($formats);	

		$editions = $stack->find()
				->setLayer('editions')
				->specifyFilter('id', $editionIDs)
				->load();
		$editions = new Layer($editions);
				
        echo "<h1>{$artwork->title}</h1>";
//		$allInLayer = $editions->accessArgs()->setLimit('all');
        foreach ($editions->load() as $edition) {
            echo "<h2>{$edition->displayTitle}</h2>";
            foreach ($formats->load() as $format) {
                echo "<h3>{$format->displayTitle}</h3>";
				$assignedPieces = $pieces->find()
						->specifyFilter('format_id', $format->id)
						->load();
				foreach ($assignedPieces as $piece) {
					echo '<ul><li>' . $piece->displayTitle . '<ul>';
					$pieceActivity = $joinLayer->find()
							->specifyFilter('piece_id', $piece->id)
							->load();
					foreach ($pieceActivity as $link) {
						echo "<li>"
						. "{$activity->member($link->disposition_id)->displayTitle}"
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

	foreach ($activity->load() as $dispId => $disposition) {
		$joinArray = $stacks->find()
				->setLayer('dispositionsPieces')
				->specifyFilter('disposition_id', $dispId)
				->load();
		$joinLayer = new Layer($joinArray, 'dispositionsPieces');
		
		$distinct_pieces = $stacks->find()
				->setLayer('pieces')
				->specifyFilter(
						'id', 
						$joinLayer->distinct('piece_id'), 
						'in_array')
				->load();
		$pieces = new Layer($distinct_pieces, 'pieces');
		
		echo '<h3>' . $disposition->displayTitle . " (id: $disposition->id)" . '</h3><ul>';
        foreach ($pieces->sort('format_id') as $piece) {
			
			$stack = $stacks->ownerOf('pieces', $piece->id)[0];
			$format = $stack->formats->member($piece->format_id);
			$edition = $stack->editions->member($piece->edition_id);
			$artwork = $stack->primaryEntity();

			echo '<li>' . ucfirst($piece->displayTitle) . ' from ' . 
                $artwork->title . ', ' . 
                $edition->displayTitle . ', ' . 
                $format->displayTitle . 
                '</li>';
        }
        echo '</ul></li></ul>';
    }
}
?>
