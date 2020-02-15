<?php
use App\Lib\Prefs;
use App\Constants\PrefCon;

/* @var \App\View\AppView $this */

$this->start('pagination_prefs_form');
echo $this->element('Preferences/Pagination/form_create');

echo $this->Html->tag('li', $this->Form->control(PrefCon::PAGING_CATEGORY_LIMIT));
echo $this->Html->tag('li', $this->Form->control(
    PrefCon::PAGING_CATEGORY_DIR, [
    'options' => PrefCon::selectList(PrefCon::PAGING_CATEGORY_DIR),])
);
echo $this->Html->tag('li', $this->Form->control(
    PrefCon::PAGING_CATEGORY_SORT, [
    'options' => PrefCon::selectList(PrefCon::PAGING_CATEGORY_SORT),])
);

echo $this->element('Preferences/Pagination/form_end');
$this->end();
