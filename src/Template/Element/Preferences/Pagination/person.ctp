<?php
use App\Lib\Prefs;

/* @var \App\View\AppView $this */
/* @var Prefs $PrefsObject */


$control = $this->Form->control(
    $PrefsObject::PAGINATION_SORT_PEOPLE, [
    'options' => $PrefsObject::selectList($PrefsObject::PAGINATION_SORT_PEOPLE),]);

echo $this->Html->tag('li', $control);
