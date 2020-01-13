<?php
use Cake\Utility\Text;
use App\Model\Entity\PersonCard;

/**
 * @var \Cake\View\View $this
 * @var \App\Model\Lib\StackSet $PersonCards
 */

foreach($cards->getData() as $id => $card) {

    /* @var \App\Model\Entity\PersonCard $card */

    $isSupervisor = $card->isSupervisor() ? 'Supervisor' : '';
    $isManager = $card->isManager() ? 'Manager' : '';
    $isArtitst = $card->isArtist() ? 'Artist' : '';
    $type = $card->rootElement()->member_type;

    echo "<p><span>{$type}</span> <strong>{$card->name()}</strong> $isSupervisor $isArtitst $isManager ";
    echo $this->Html->link('View details', ['action' => 'view', $card->rootID()]);
}

echo $this->element('Member/search', ['identitySchema' => $identitySchema]);
