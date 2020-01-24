<?php
/* @var \App\View\AppView $this */
$this->extend('/Layout/default');

echo $this->Html->tag('ul',
    $this->Paginator->prev() . '<li>||</li>' . $this->Paginator->next(),
    ['class' => 'menu']);

echo $this->fetch('pagination_prefs_form');

echo $this->fetch('content');

//add search tools
echo '<h1>Search Tools Here</h1>';
