<?php
use App\Lib\Prefs;

/* @var \App\View\AppView $this */
/* @var Prefs $PrefsObject */


$control = $this->Form->control(
    $PrefsObject::PAGINATION_SORT_CATEGORY, [
    'options' => $PrefsObject->selectList($PrefsObject::PAGINATION_SORT_CATEGORY),]);

echo $this->Html->tag('li', $control);
