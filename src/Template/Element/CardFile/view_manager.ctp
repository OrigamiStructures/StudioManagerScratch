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
    foreach ($personCard->getPermittedCategories() as $member) {
        echo '<li>' . $member . ' owned by *calculate name* </li>';
//        echo '<li>' . $this->Html->link($member->name(), ['action' => 'view', $member->id]) . '</li>';
    }
    echo '</ul>';
}

?>
