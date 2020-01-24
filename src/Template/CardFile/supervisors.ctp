<?php
use Cake\Utility\Text;
use App\Model\Entity\PersonCard;

/**
 * @var \Cake\View\View $this
 * @var \App\Model\Lib\StackSet $personCards
 */

/**
 * prepate the pagination prefs form view block
 */
echo $this->element('Preferences/Pagination/person');

foreach($personCards->getData() as $id => $card) {

    /* @var PersonCard $card */

    $isSupervisor = $card->isSupervisor() ? 'Supervisor' : '';
    $isManager = $card->isManager() ? 'Manager' : '';
    $isArtitst = $card->isArtist() ? 'Artist' : '';

    echo "<p><strong>{$card->name()}</strong> $isSupervisor $isArtitst $isManager ";
    echo $this->Html->link('View details', ['action' => 'view', $card->rootID()]);
    echo ' ' . $this->Html->link('Act As', [
        'controller' => 'supervisors',
        'action' => 'act_as',
        'supervisor', $card->rootElement()->user_id
    ]);
}

echo $this->element('Member/search', ['identitySchema' => $identitySchema]);
