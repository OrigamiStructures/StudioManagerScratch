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
				->specifyFilter('disposition_id', $dispLayer->IDs())
				->load();
		$joinLayer = new Layer($joinArray);

		// Layer object's __contruct() accept an array of entities 
		// and that's what $stack->load( ) returns. 
		// Layer turns an array of entities into a quasi-db tool.
		// See \App\Model\Lib\Layer
		$distinct_pieces = $stack->find()
				->setLayer('pieces')
				->filterValue($stack->trait_distinct('piece_id', $joinArray))
				->load();
		
		$formatIDs = $stack->trait_distinct('format_id', $distinct_pieces);
		$pieces = new Layer($distinct_pieces);
		
		$formats = $stack->find()
				->setLayer('formats')
				->specifyFilter('id', $formatIDs)
				->load();
		
		$editionIDs = $stack->trait_distinct('edition_id', $formats);
		$formats = new Layer($formats);	

		$editions = $stack->find()
				->setLayer('editions')
				->specifyFilter('id', $editionIDs)
				->load();
		$editions = new Layer($editions);
		
		$indexed_dispo = $stack->accessArgs();
		
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
					die;
					$dispo_joins_for_piece_arg = $joins->accessArgs()
							->specifyFilter('piece_id', $piece->id);
					foreach ($joins->load($dispo_joins_for_piece_arg) as $link) {
						$indexed_dispo->setIdIndex($link->disposition_id); // this is an id search
						echo "<li>{$dispLayer->load($indexed_dispo)->displayTitle}</li>";
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
	
	$format_for_piece_arg = $dispLayer->accessArgs()
			->setLayer('formats')
			->setValueSource('id');
	$edition_for_format = $dispLayer->accessArgs()
			->setLayer('editions')
			->setValueSource('id');
	$artwork_for_edition = $dispLayer->accessArgs()
			->setLayer('artwork')
			->setValueSource('id');
	$dispo_joins_args = $dispLayer->accessArgs()
			->setLayer('dispositionsPieces')
			->setValueSource('disposition_id');
	$linked_pieces_args = $dispLayer->accessArgs()
			->setLayer('pieces')
			->setValueSource('id');
	
	foreach ($dispLayer->load($allInLayer) as $dispId => $disposition) {
//		
		$dispo_joins_args->filterValue($dispLayer->IDs());
		$joins = new Layer($stacks->load($dispo_joins_args));
		$linked_pieces_args->filterValue($joins->distinct('piece_id'));
		$pieces = new Layer($stacks->load($linked_pieces_args));
				
		echo '<h3>' . $disposition->displayTitle . "($disposition->id)" . '</h3><ul>';
        foreach ($pieces->sort('format_id') as $piece) {
			
			$stack = $stacks->ownerOf('pieces', $piece->id)[0];
			
			$format_for_piece_arg->filterValue($piece->format_id);
			$format = $stack->load($format_for_piece_arg)[$piece->format_id];	
			$edition_for_format->filterValue($piece->edition_id);
			$edition = $stack->load($edition_for_format)[$piece->edition_id];
			$artwork_for_edition->filterValue($edition->artwork_id);
			$artwork = $stack->load($artwork_for_edition)[$edition->artwork_id];
			
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
