<?php
use Cake\Utility\Text;
use App\Model\Entity\PersonCard;

/* @var \Cake\View\View $this */

foreach($personCards->getData() as $id => $card) {

    /* @var PersonCard $card */

    $isSupervisor = $card->isSupervisor() ? 'Supervisor' : '';
    $isManager = $card->isManager() ? 'Manager' : '';
    $isArtitst = $card->isArtist() ? 'Artist' : '';

    echo "<p><strong>{$card->name()}</strong> $isSupervisor $isArtitst $isManager ";
    echo $this->Html->link('View details', ['action' => 'view', $card->rootID()]);
}
