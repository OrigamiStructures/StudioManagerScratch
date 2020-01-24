<?php
use App\Lib\Prefs;
use App\Lib\PrefCon;

/* @var \App\View\AppView $this */
/* @var Prefs $PrefsObject */

$this->start('pagination_prefs_form');
echo $this->element('Preferences/Pagination/form_create');

echo $this->Html->tag('li', $this->Form->control(
    PrefCon::PAGINATION_SORT_PEOPLE, [
    'options' => PrefCon::selectList(PrefCon::PAGINATION_SORT_PEOPLE),])
);

echo $this->element('Preferences/Pagination/form_end');
$this->end();
