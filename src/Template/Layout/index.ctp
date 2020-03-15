<?php
/* @var \App\View\AppView $this */
$this->extend('/Layout/default');

echo $this->element('AlertBanners/index_filter');

echo $this->element('Common/pagination_bar', ['pagingScope' => 'index']);

echo $this->fetch('pagination_prefs_form');

echo $this->fetch('content');

//add search tools
echo '<h1>Search Tools Here</h1>';
