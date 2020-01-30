<?php
/* @var \App\Model\Entity\PersonCard $personCard */
$delegatedManagement = $personCard->delegatedManagement($contextUser->getId('supervisor'));


if($personCard->delegatedManagement($contextUser->getId('supervisor'))) : ?>
    <p><em><strong>Delegated Management</strong></em></p>
    <?php
    $delegatedMessage = '<p>%s assigned management of the artist %s to %s. %s. [Contact %s].</p>';
    foreach ($delegatedManagement as $manifest) {
        /* @var \App\Model\Entity\Manifest $manifest */
        $permissonLink = $this->Html->link('Set Permissions', [
            'controller' => 'supervisors', 'action' => 'permissions', $manifest->id
        ]);
        $supervisor = $manifest->getName('supervisor');
        $manager = $manifest->getName('manager');
        $artist = $manifest->getName('artist');
        printf($delegatedMessage, $supervisor, $artist, $manager, $permissonLink, $manager);
    }
endif;

echo "<p><strong>{$personCard->rootElement()->name()} is sharing these card file categories</strong></p>";
if (!$personCard->isSharingCategories()) {
    echo '<p>None</p>';
} else {
    echo '<ul class="member">';
    $duplicates = [];
    foreach ($personCard->getShareCategories() as $share) {
        /* @var \App\Model\Entity\Share $share */
        $category_id = $share->getMemberId('category');
        echo in_array($category_id, $duplicates)
            ? ''
            : '<li>' . $this->Html->link($share->getName('category'), ['action' => 'view', $category_id]) . '</li>';
        $duplicates[$category_id] = $category_id;
    }
    echo '</ul>';
}
