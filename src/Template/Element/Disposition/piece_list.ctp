<?php 
$pieces = $standing_disposition->pieces;
foreach ($pieces as $piece) :
    $artwork_id = $piece->fullyIdentified() ? $piece->format->edition->artwork_id : $piece->edition->artwork_id;
    $review_url = [
        'controller' => 'artworks', 
        '?' => [
            'artwork' => $artwork_id,
            'edition' => $piece->edition_id,
            'format' => $piece->format_id,
        ]];

    $remove_url = [
        'controller' => 'dispositions', 
        '?' => [
            'artwork' => $artwork_id,
            'edition' => $piece->edition_id,
            'format' => $piece->format_id,
            'piece' => $piece->id,
        ]];
?>

		<p>
            <?= $this->ArtStackTools->inlineReviewDelete($review_url, $remove_url); ?>
            <?= $this->DispositionTools->identity($piece); ?>
		</p>
		
<?php
endforeach;
?>