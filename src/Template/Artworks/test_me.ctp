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
	foreach ($artworks->merge() as $artId) {
		echo "<h1>{$artworks->entity($artId)->title}</h1>";
		foreach ($artworks->sourceFor($artId) as $editionId) {
			echo "<h2>{$editions->entity($editionId)->displayTitle}</h2>";
			$count = 0;
			foreach ($editions->sourceFor($editionId) as $pieceId) {
				if ($count === 0) {
					$formatId = $formats->getSet($pieceId)->idSet()[0];
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
//osd($artworks);
//$linkedPieces = $pieces->getSet(126)->idSet();
//osd($linkedPieces);
//$linkedEditions = [];
//$linkedFormats = [];
//foreach ($linkedPieces as $piece) {
//	$linkedFormats[$piece] = $formats->getSet($piece)->idSet();
//	$linkedEditions[$piece] = $editions->getSet($piece)->idSet();
//}
//osd($linkedEditions);
//osd($linkedFormats);

		?>
