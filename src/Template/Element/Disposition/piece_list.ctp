<?php 
$pieces = $standing_disposition->pieces;
foreach ($pieces as $piece) :
    $review_url = [
        'controller' => 'artworks', 
        '?' => [
            'artwork' => $piece->format->edition->artwork_id,
            'edition' => $piece->edition_id,
            'format' => $piece->format_id,
        ]];

    $remove_url = [
        'controller' => 'dispositions', 
        '?' => [
            'artwork' => $piece->format->edition->artwork_id,
            'edition' => $piece->edition_id,
            'format' => $piece->format_id,
            'piece' => $piece->id,
        ]];
?>

		<p>
            <?= $this->InlineTools->inlineReviewDelete($review_url, $remove_url); ?>
            <?= $this->DispositionTools->identity($piece); ?>
		</p>
		
<?php
endforeach;
?>