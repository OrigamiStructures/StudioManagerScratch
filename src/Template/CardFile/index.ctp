<?php
use Cake\Utility\Text;
use App\Model\Entity\PersonCard;

/**
 * @var \Cake\View\View $this
 * @var \App\Model\Lib\StackSet $fatGenericCards
 */

foreach($fatGenericCards->getData() as $id => $card) {

    /* @var \App\Model\Entity\FatGenericCard $card */

    $isSupervisor = $card->isSupervisor() ? 'Supervisor' : '';
    $isManager = $card->isManager() ? 'Manager' : '';
    $isArtitst = $card->isArtist() ? 'Artist' : '';
    $type = $card->rootElement()->member_type;

    echo "<p><span>{$type}</span> <strong>{$card->name()}</strong> $isSupervisor $isArtitst $isManager ";
    echo $this->Html->link('View details', ['action' => 'view', $card->rootID()]);
}
