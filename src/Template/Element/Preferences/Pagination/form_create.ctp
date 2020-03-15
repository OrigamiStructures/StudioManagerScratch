<?php
use App\Lib\Prefs;
use App\Constants\PrefCon;
/**
 * @var \App\View\AppView $this
 */

echo $this->Form->create($Prefs->getForm(), [
    'url' => ['controller' => 'preferences', 'action' => 'setPrefs']
]);
echo $this->Html->tag('ul', null, ['class' => 'menu']);

// leaves the form open and a <UL> open
// place additional inputs in <LI>s

