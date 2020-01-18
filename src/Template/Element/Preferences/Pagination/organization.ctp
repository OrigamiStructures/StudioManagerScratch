<?php
use App\Lib\Prefs;

/* @var \App\View\AppView $this */
/* @var Prefs $PrefsObject */


$control = $this->Form->control(
    $PrefsObject::PAGINATION_SORT_ORGANIZATION, [
    'options' => $PrefsObject->selectList($PrefsObject::PAGINATION_SORT_ORGANIZATION),]);

echo $this->Html->tag('li', $control);
