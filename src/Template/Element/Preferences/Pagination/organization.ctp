<?php
use App\Lib\Prefs;
use App\Lib\PrefCon;

/* @var \App\View\AppView $this */
/* @var Prefs $PrefsObject */


$control = $this->Form->control(
    $PrefsObject::PAGINATION_SORT_ORGANIZATION, [
    'options' => PrefCon::selectList(PrefCon::PAGINATION_SORT_ORGANIZATION),]);

echo $this->Html->tag('li', $control);
