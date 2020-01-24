<?php
use Cake\Utility\Text;
use App\Model\Entity\PersonCard;

/**
 * @var \Cake\View\View $this
 * @var \App\Model\Lib\StackSet $PersonCards
 */

/**
 * prepate the pagination prefs form view block
 */
echo $this->element('Preferences/Pagination/person');

foreach($cards->getData() as $id => $card) {

    /* @var \App\Model\Entity\PersonCard $card */

    $isSupervisor = $card->isSupervisor() ? 'Supervisor' : '';
    $isManager = $card->isManager() ? 'Manager' : '';
    $isArtitst = $card->isArtist() ? 'Artist' : '';
    $type = $card->rootElement()->member_type;

    echo "<p><strong>{$card->name(LABELED)}</strong> $isSupervisor $isArtitst $isManager ";
    echo $this->Html->link('View details', ['action' => 'view', $card->rootID()]);
}

echo $this->element('Member/search', ['identitySchema' => $identitySchema]);
