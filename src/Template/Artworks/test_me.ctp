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
		$argObj = null;
		$joins = new Layer($stack->load('dispositionsPieces', ['disposition_id', $dispLayer->IDs()], $argObj));
		
		// Layer object's __contruct() accept an array of entities 
		// and that's what $stack->load( ) returns. 
		// Layer turns an array of entities into a quasi-db tool.
		// See \App\Lib\Layer
		$argObj = null;
		$pieces = new Layer(
				// load() is the primary 'search' method in an entity 
				// that extends the StackEntity class.
				// See \App\Model\Entity\StackEntity for the base class 
				// of these entities and details of load()
				$stack->load('pieces', ['id', 
					// distinct() is a Layer class method that returns no duplicates.
					// $joins was defined on line 36 as a Layer object.
					$joins->distinct('piece_id')], $argObj)
			);
		$argObj = null;
		$formats = new Layer($stack->load('formats', ['id', $pieces->distinct('format_id')], $argObj));	
		$argObj = null;
		$editions = new Layer($stack->load('editions', ['id', $formats->distinct('edition_id')], $argObj));
		
        echo "<h1>{$artwork->title}</h1>";
		$allEditionsArg = $editions->accessArgs()->limit('all');
        foreach ($editions->load('', [], $allEditionsArg) as $edition) {
            echo "<h2>{$edition->displayTitle}</h2>";
			$argObj = null;
            foreach ($formats->load('all', [], $argObj) as $format) {
                echo "<h3>{$format->displayTitle}</h3>";
				$argObj = null;
				foreach ($pieces->load('format_id', $format->id, $argObj) as $piece) {
					echo '<ul><li>' . $piece->displayTitle . '<ul>';
					$argObj = null;
					foreach ($joins->load('piece_id', $piece->id, $argObj) as $link) {
						$argObj = null;
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
	$argObj = null;
    foreach ($dispLayer->load('all', [], $argObj) as $dispId => $disposition) {
		
		$argObj = null;
		$joins = new Layer($stacks->load('dispositionsPieces', ['disposition_id', $dispLayer->IDs()], $argObj));
		$argObj = null;
		$pieces = new Layer($stacks->load('pieces', ['id', $joins->distinct('piece_id')], $argObj));
				
        echo '<h3>' . $disposition->displayTitle . '</h3><ul>';
        foreach ($pieces->sort('format_id') as $piece) {
			
			$stack = $stacks->ownerOf('pieces', $piece->id)[0];
			
			$argObj = null;
			$format = $stack->load('formats', ['id', $piece->format_id], $argObj)[$piece->format_id];	
			$argObj = null;
			$edition = $stack->load('editions', ['id', $piece->edition_id], $argObj)[$piece->edition_id];
			$argObj = null;
			$artwork = $stack->load('artwork', ['id', $edition->artwork_id], $argObj)[$edition->artwork_id];
			
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
