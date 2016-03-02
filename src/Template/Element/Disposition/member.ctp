<?php
    $review_url = [
        'controller' => 'members', 
        '?' => [
            'member' => $standing_disposition->member->id,
        ]];

    $remove_url = [
        'controller' => 'dispositions', 
        '?' => [
            'member' => $standing_disposition->member->id,
        ]];

?>

<p>
    <?= $this->ArtworkReview->inlineReviewDelete($review_url, $remove_url); ?>
    <?= $this->DispositionTools->identity($standing_disposition->member); ?>
</p>