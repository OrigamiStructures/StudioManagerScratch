<?php
/* @var \App\View\AppView $this */
$this->extend('/Layout/default');

//add pagination tools
echo '<h1>Pagination Here</h1>';

echo $this->fetch('content');

//add search tools
echo '<h1>Search Tools Here</h1>';
