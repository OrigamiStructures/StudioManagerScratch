<?php
use Cake\Utility\Text;
use App\Model\Entity\PersonCard;

foreach($personCards->getData() as $id => $card) {

    /* @var PersonCard $card */

    $isSupervisor = $isArtitst = $isManager = '';
    if(count($card->manifests) > 0){
        $supervisors = $card->getManifests()->toDistinctList('supervisor_member');
        $managers = $card->getManifests()->toDistinctList('manager_member');
        $artists = $card->getManifests()->toDistinctList('member_id');

        $isSupervisor = in_array($card->rootID(), $supervisors) ? 'Supervisor' : '';
        $isManager = in_array($card->rootID(), $managers) ? 'Manager' : '';
        $isArtitst = in_array($card->rootID(), $artists) ? 'Artist' : '';
    }

    $membershipList = count($card->getMemberships()) == 0
        ? 'None'
        : Text::toList($card->getMemberships()->toValueList('name'));

    echo "<p><strong>{$card->name()}</strong> $isSupervisor $isArtitst $isManager</p>";
	echo '<p>Memberships: ' . $membershipList . '</p>';
}
