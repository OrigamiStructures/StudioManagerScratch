<?php
use App\Lib\Prefs;
use App\Lib\PrefCon;

/* @var \App\View\AppView $this */
/* @var Prefs $PrefsObject */


$control = $this->Form->control(
    PrefCon::PAGINATION_SORT_PEOPLE, [
    'options' => PrefCon::selectList(PrefCon::PAGINATION_SORT_PEOPLE),]);

echo $this->Html->tag('li', $control);
