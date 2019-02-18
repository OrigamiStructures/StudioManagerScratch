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
		$dispoID_list_match = $stack->accessArgs()->property('disposition_id');
		$joins = new Layer($stack->load('dispositionsPieces', ['', $dispLayer->IDs()], $dispoID_list_match));
		
		// Layer object's __contruct() accept an array of entities 
		// and that's what $stack->load( ) returns. 
		// Layer turns an array of entities into a quasi-db tool.
		// See \App\Lib\Layer
		$distinct_pieces_args = $stack->accessArgs()->property('id');
		$pieces = new Layer(
				// load() is the primary 'search' method in an entity 
				// that extends the StackEntity class.
				// See \App\Model\Entity\StackEntity for the base class 
				// of these entities and details of load()
				$stack->load('pieces', ['', 
					// distinct() is a Layer class method that returns no duplicates.
					// $joins was defined on line 36 as a Layer object.
					$joins->distinct('piece_id')], $distinct_pieces_args)
			);
		$distinct_formats_args = $stack->accessArgs()->property('id');
		$formats = new Layer($stack->load('formats', ['', $pieces->distinct('format_id')], $distinct_formats_args));	
		$distinct_editions_args = $stack->accessArgs()->property('id');
		$editions = new Layer($stack->load('editions', ['', $formats->distinct('edition_id')], $distinct_editions_args));
		
        echo "<h1>{$artwork->title}</h1>";
		$allInLayer = $editions->accessArgs()->limit('all');
        foreach ($editions->load('', [], $allInLayer) as $edition) {
            echo "<h2>{$edition->displayTitle}</h2>";
            foreach ($formats->load('', [], $allInLayer) as $format) {
                echo "<h3>{$format->displayTitle}</h3>";
				$pieces_for_format_arg = $pieces->accessArgs()->property('format_id');
				foreach ($pieces->load('', $format->id, $pieces_for_format_arg) as $piece) {
					echo '<ul><li>' . $piece->displayTitle . '<ul>';
					$dispo_joins_for_piece_arg = $joins->accessArgs()->property('piece_id');
					foreach ($joins->load('', $piece->id, $dispo_joins_for_piece_arg) as $link) {
						$argObj = null; // this is an id search
						echo "<li>{$dispLayer->load($link->disposition_id, [], $argObj)->displayTitle}</li>";
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
	
	$format_for_piece_arg = $dispLayer->accessArgs()->property('id');
	$edition_for_format = $dispLayer->accessArgs()->property('id');
	$artwork_for_edition = $dispLayer->accessArgs()->property('id');
	
	foreach ($dispLayer->load('', [], $allInLayer) as $dispId => $disposition) {
		
		$argObj = null;
		$joins = new Layer($stacks->load('dispositionsPieces', ['disposition_id', $dispLayer->IDs()], $argObj));
		$argObj = null;
		$pieces = new Layer($stacks->load('pieces', ['id', $joins->distinct('piece_id')], $argObj));
				
		echo '<h3>' . $disposition->displayTitle . "($disposition->id)" . '</h3><ul>';
        foreach ($pieces->sort('format_id') as $piece) {
			
			$stack = $stacks->ownerOf('pieces', $piece->id)[0];
			
//			$format_for_piece_arg->value($piece->format_id);
			$format = $stack->load('formats', ['', $piece->format_id], $format_for_piece_arg)[$piece->format_id];	
//			$edition_for_format->value($piece->edition_id);
			$edition = $stack->load('editions', ['', $piece->edition_id], $edition_for_format)[$piece->edition_id];
//			$artwork_for_edition->value($edition->artwork_id);
			$artwork = $stack->load('artwork', ['', $edition->artwork_id], $artwork_for_edition)[$edition->artwork_id];
			
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
