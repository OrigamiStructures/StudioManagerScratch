<?php
use Cake\Utility\Text;
use App\Model\Entity\PersonCard;

/**
 * @var \Cake\View\View $this
 * @var \App\Model\Lib\StackSet $PersonCards
 */

/**
 * prepate the pagination prefs form view block for use by the layout
 */
echo $this->element('Preferences/Pagination/person');

/* @var \App\Model\Lib\StackSet $stackSet */
$ident = $stackSet->getLayer('identity')
    ->find()
    ->specifyFilter('member_type', 'Person')
    ->toKeyValueList('member_type', 'name');
osd($ident);
osd($this->getRequest()->getParam('paging'));

foreach($stackSet->getData() as $id => $card) {

    /* @var \App\Model\Entity\PersonCard $card */

    $isSupervisor = $card->isSupervisor() ? 'Supervisor' : '';
    $isManager = $card->isManager() ? 'Manager' : '';
    $isArtitst = $card->isArtist() ? 'Artist' : '';
    $type = $card->rootElement()->member_type;

    echo "<p><strong>{$card->name(LABELED)}</strong> $isSupervisor $isArtitst $isManager ";
    echo $this->Html->link('View details', ['action' => 'view', $card->rootID()]);
}

echo $this->element('Member/search', ['identitySchema' => $identitySchema]);
