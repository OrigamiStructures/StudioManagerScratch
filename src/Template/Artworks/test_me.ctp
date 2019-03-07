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
use App\Lib\Layer;

echo $this->element('Disposition/testing/dispo_table');
if (isset($stacks)) {
	foreach ($stacks->all() as $stack) {
		
		$artwork = $stack->primaryEntity();
		$dispoID_list_match = $stack->accessArgs()
				->setLayer('dispositionsPieces')
				->valueSource('disposition_id')
				->filterValue($dispLayer->IDs());
		$joins = new Layer($stack->load($dispoID_list_match));
		
		// Layer object's __contruct() accept an array of entities 
		// and that's what $stack->load( ) returns. 
		// Layer turns an array of entities into a quasi-db tool.
		// See \App\Lib\Layer
		$distinct_pieces_args = $stack->accessArgs()
				->setLayer('pieces')
				->valueSource('id')
				->filterValue($joins->distinct('piece_id'));
		$distinct_pieces = $stack->load($distinct_pieces_args);
		$pieces = new Layer($distinct_pieces);
		$distinct_formats_args = $stack->accessArgs()
				->setLayer('formats')
				->valueSource('id')
				->filterValue($pieces->distinct('format_id'));
		$formats = new Layer($stack->load($distinct_formats_args));	
		$distinct_editions_args = $stack->accessArgs()
				->setLayer('editions')
				->valueSource('id')
				->filterValue($formats->distinct('edition_id'));
		$editions = new Layer($stack->load($distinct_editions_args));
		$indexed_dispo = $stack->accessArgs();
		
        echo "<h1>{$artwork->title}</h1>";
		$allInLayer = $editions->accessArgs()->setLimit('all');
        foreach ($editions->load($allInLayer) as $edition) {
            echo "<h2>{$edition->displayTitle}</h2>";
            foreach ($formats->load($allInLayer) as $format) {
                echo "<h3>{$format->displayTitle}</h3>";
				$pieces_for_format_arg = $pieces->accessArgs()
						->valueSource('format_id')
						->filterValue($format->id);
				foreach ($pieces->load($pieces_for_format_arg) as $piece) {
					echo '<ul><li>' . $piece->displayTitle . '<ul>';
					$dispo_joins_for_piece_arg = $joins->accessArgs()
							->valueSource('piece_id')
							->filterValue($piece->id);
					foreach ($joins->load($dispo_joins_for_piece_arg) as $link) {
						$indexed_dispo->lookupIndex($link->disposition_id); // this is an id search
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
			->valueSource('id');
	$edition_for_format = $dispLayer->accessArgs()
			->setLayer('editions')
			->valueSource('id');
	$artwork_for_edition = $dispLayer->accessArgs()
			->setLayer('artwork')
			->valueSource('id');
	$dispo_joins_args = $dispLayer->accessArgs()
			->setLayer('dispositionsPieces')
			->valueSource('disposition_id');
	$linked_pieces_args = $dispLayer->accessArgs()
			->setLayer('pieces')
			->valueSource('id');
	
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
