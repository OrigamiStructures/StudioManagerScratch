<?php
use Cake\Utility\Text;
use App\Model\Entity\PersonCard;

foreach($rolodexCards->getData() as $id => $card) {

    /* @var PersonCard $card */

    $isSupervisor = $isArtitst = $isManager = '';
    if(count($card->manifests) > 0){
        $supervisors = $card->manifests()->toDistinctList('supervisor_member');
        $managers = $card->manifests()->toDistinctList('manager_member');
        $artists = $card->manifests()->toDistinctList('member_id');

        $isSupervisor = in_array($card->rootID(), $supervisors) ? 'Supervisor' : '';
        $isManager = in_array($card->rootID(), $managers) ? 'Manager' : '';
        $isArtitst = in_array($card->rootID(), $artists) ? 'Artist' : '';
    }

    $memberships = collection($card->memberships())
        ->reduce(function($membershipList, $name) {
            $membershipList[] = $name;
            return $membershipList;
        },[]) ;
    $membershipList = count($memberships) == 0 ? 'None' : Text::toList($memberships);

    echo "<p><strong>{$card->name()}</strong> $isSupervisor $isArtitst $isManager</p>";
	echo '<p>Memberships: ' . $membershipList . '</p>';
}
