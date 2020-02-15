<?php
use App\Lib\Prefs;
use App\Constants\PrefCon;

/* @var \App\View\AppView $this */

$this->start('pagination_prefs_form');
echo $this->element('Preferences/Pagination/form_create');

echo $this->Html->tag('li', $this->Form->control(PrefCon::PAGING_PEOPLE_LIMIT));
echo $this->Html->tag('li', $this->Form->control(
    PrefCon::PAGING_PEOPLE_DIR, [
    'options' => PrefCon::selectList(PrefCon::PAGING_PEOPLE_DIR),])
);
echo $this->Html->tag('li', $this->Form->control(
    PrefCon::PAGING_PEOPLE_SORT, [
    'options' => PrefCon::selectList(PrefCon::PAGING_PEOPLE_SORT),])
);

echo $this->element('Preferences/Pagination/form_end');
$this->end();
