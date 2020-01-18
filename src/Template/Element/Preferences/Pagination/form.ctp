<?php
use App\Lib\Prefs;
/**
 * @var \App\View\AppView $this
 * @var Prefs $PrefsObject
 */

echo $this->Form->create($PrefsObject->getForm(), [
    'url' => ['controller' => 'preferences', 'action' => 'setPrefs']
]);
echo $this->Html->tag('ul', null, ['class' => 'menu']);
echo $this->Html->tag('li', $this->Form->control($PrefsObject::PAGINATION_LIMIT));

// leaves the form open and a <UL> open
// place additional inputs in <LI>s

echo $this->element('Preferences/Pagination/' . $PrefsObject->getFormVariant());

echo '</ul>';
echo $this->Form->control('id', ['type' => 'hidden']);
echo $this->Form->submit();
echo $this->Form->end();
