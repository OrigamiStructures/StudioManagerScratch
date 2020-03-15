<?php
use Cake\Utility\Text;

/**
 * prepate the pagination prefs form view block for use by the layout
 */
$this->element('Preferences/Pagination/organization');

foreach($stackSet->getData() as $id => $card) {

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
    if ($card->isCategory()) {
        $organization = "<span>Organization: "
        . Text::toList($card->IDs()) . '</span>';
    }

    echo "<p><strong>" . $this->Html->link($card->name(), ['action' => 'view', $card->rootID()]) . "</strong> $organization</p>";
	echo $contacts;
	echo $addresses;
    echo $memberships;
    echo $members;
}

echo $this->element('Member/search', ['identitySchema' => $identitySchema]);
