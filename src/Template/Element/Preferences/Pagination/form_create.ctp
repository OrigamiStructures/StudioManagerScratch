<?php
use App\Lib\Prefs;
use App\Constants\PrefCon;
/**
 * @var \App\View\AppView $this
 * @var Prefs $PrefsObject
 */

echo $this->Form->create($PrefsObject->getForm(), [
    'url' => ['controller' => 'preferences', 'action' => 'setPrefs']
]);
echo $this->Html->tag('ul', null, ['class' => 'menu']);
echo $this->Html->tag('li', $this->Form->control(PrefCon::PAGING_PEOPLE_LIMIT));
echo $this->Html->tag('li', $this->Form->control(
    PrefCon::PAGING_PEOPLE_DIR, [
    'options' => PrefCon::selectList(PrefCon::PAGINATION_DIR),])
);

// leaves the form open and a <UL> open
// place additional inputs in <LI>s

