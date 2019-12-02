<?php
use Cake\Utility\Text;

foreach($institutionCards->getData() as $id => $card) {

    /* @var \App\Model\Entity\OrganizationCard $card
     */
    $contacts = '<p>' . Text::toList($card->contacts()) . '</p>';

    $addresses = '<p>' . Text::toList($card->addresses()) . '</p>';

    $memberships = '';
    if ($card->isMember()) {
        $memberships =  "<p><strong>Memberships</strong>: "
            . Text::toList($card->memberships()) . '</p>';
    }

    $members = '';
    if ($card->hasMembers()) {
        $members =  "<p><strong>Members</strong>: "
            . Text::toList($card->members()) . '</p>';
    }

    $institutions = '';
    if ($card->isGroup()) {
        $institutions = "<span>Institution: "
        . Text::toList($card->IDs()) . '</span>';
    }

    echo "<p><strong>{$card->name()}</strong> $institutions</p>";
	echo $contacts;
	echo $addresses;
    echo $memberships;
    echo $members;


}
