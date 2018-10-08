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
echo $this->element('Disposition/testing/dispo_table');
if (isset($artworks)) {
	foreach ($artworks->idList() as $artId) {
		echo "<h1>{$artworks->entity($artId)->title}</h1>";
		foreach ($artworks->sourceFor($artId) as $editionId) {
			echo "<h2>{$editions->entity($editionId)->displayTitle}</h2>";
			$count = 0;
			foreach ($editions->sourceFor($editionId) as $pieceId) {
				if ($count === 0) {
					$formatId = $formats->getSet($pieceId)->idList()[0];
					echo "<h3>{$formats->entity($formatId)->displayTitle}</h3>";
					$count++;
				}
				echo '<ul><li>' . $pieces->entity($pieceId)->displayTitle . '<ul>';
				foreach ($pieces->sourceFor($pieceId) as $dispositionId) {
					echo "<li>{$dispositions[$dispositionId]->displayTitle}</li>";
				}
				echo '</ul></li></ul>';
			}
		}
	}
}
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
