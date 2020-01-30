<p><em><strong>Received Management</strong></em></p>
<?php
/* @var \App\Model\Entity\PersonCard $personCard */

$receivedManagement = $personCard->receivedManagement($contextUser->getId('supervisor'));
$receivedMessage = '<p>%s assigned %s management of the artist %s. [Work on this artist now]. [Contact %s].</p>';
foreach ($receivedManagement as $manifest) {
    /* @var \App\Model\Entity\Manifest $manifest */
    $supervisor = $manifest->getName('supervisor');
    $manager = $manifest->getName('manager');
    $artist = $manifest->getName('artist');
    printf($receivedMessage, $supervisor, $manager, $artist, $supervisor);
}

echo "<p><strong>{$personCard->rootElement()->name()} permitted to see these card file categories</strong></p>";
if (!$personCard->hasPermittedCategories()) {
    echo '<p>None</p>';
} else {
    echo '<ul class="member">';
    $duplicates = [];
    foreach ($personCard->getPermittedCategories() as $share) {
        /* @var \App\Model\Entity\Share $share */
        $category_id = $share->getMemberId('category');
        echo in_array($category_id, $duplicates)
            ? ''
            : '<li>' . $this->Html->link($share->getName('category'), ['action' => 'view', $category_id]) . '</li>';
        $duplicates[$category_id] = $category_id;
    }
    echo '</ul>';
}

?>
