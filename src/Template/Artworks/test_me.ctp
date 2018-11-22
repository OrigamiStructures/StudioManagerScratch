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
		$joins = new Layer($stack->load('dispositionsPieces', ['disposition_id', $dispLayer->IDs()]));
		$pieces = new Layer($stack->load('pieces', ['id', $joins->distinct('piece_id')]));
		$formats = new Layer($stack->load('formats', ['id', $pieces->distinct('format_id')]));		
		$editions = new Layer($stack->load('editions', ['id', $formats->distinct('edition_id')]));
		
        echo "<h1>{$artwork->title}</h1>";
        foreach ($editions->get('all') as $edition) {
            echo "<h2>{$edition->displayTitle}</h2>";
            foreach ($formats->get('all') as $format) {
                echo "<h3>{$format->displayTitle}</h3>";
				foreach ($pieces->get('format_id', $format->id) as $piece) {
					echo '<ul><li>' . $piece->displayTitle . '<ul>';
					foreach ($joins->get('piece_id', $piece->id) as $link) {
						echo "<li>{$dispLayer->get($link->disposition_id)->displayTitle}</li>";
					}
					echo '</ul></li></ul>';
               }
            }
        }
    }
}
die;
echo '<h1>Reverse Formatting</h1>';
if (isset($artworks)) {
    foreach ($dispositions as $disposition) {
        echo '<ul><li>' . $dispositions[$dispositionId]->displayTitle . '<ul>';
        foreach ($pieces->getSet($disposition->id)->idList() as $pieceId) {
            $piece = $pieces->entity($pieceId);
            $artworkId = $editions->entity($piece->edition_id)->artwork_id;
            echo '<li>' . ucfirst($piece->displayTitle) . ' from ' . 
                $artworks->entity($artworkId)->title . ', ' . 
                $editions->entity($piece->edition_id)->displayTitle . ', ' .
                $formats->entity($piece->format_id)->displayTitle . 
                '</li>';
        }
        echo '</ul></li></ul>';
    }
}
?>
