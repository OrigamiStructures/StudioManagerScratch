<?php
use App\Form\PrefCon;

/* @var \App\View\AppView $this */

$this->start('pagination_sort_preference_control');
echo $this->Form->control(
    PrefCon::PAGINATION_SORT_PEOPLE, [
    'options' => $prefsForm->asContext($prefs->user_id)->selectList(PrefCon::PAGINATION_SORT_PEOPLE),]);
$this->end();

echo $this->element('pagination');
