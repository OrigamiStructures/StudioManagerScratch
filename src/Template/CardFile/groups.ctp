<?php
use Cake\Utility\Text;

foreach($organizationCards->getData() as $id => $card) {

    /* @var \App\Model\Entity\OrganizationCard $card
     */
    $contacts = '<p>' . Text::toList($card->getContacts()->toValueList('asString')) . '</p>';

    $addresses = '<p>' . Text::toList($card->getAddresses()->toValueList('asString')) . '</p>';

    $memberships = '';
    if ($card->isMember()) {
        $memberships =  "<p><strong>Memberships</strong>: "
            . Text::toList($card->getMemberships()->toValueList('name')) . '</p>';
    }

    $members = '';
    if ($card->hasMembers()) {
        $members =  "<p><strong>Members</strong>: "
            . Text::toList($card->getMembers()->toValueList('name')) . '</p>';
    }

    $organization = '';
    if ($card->isGroup()) {
        $organization = "<span>Organization: "
        . Text::toList($card->IDs()) . '</span>';
    }

    echo "<p><strong>{$card->name()}</strong> $organization</p>";
	echo $contacts;
	echo $addresses;
    echo $memberships;
    echo $members;


}
