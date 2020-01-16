<?php
/* @var \App\View\AppView $this */
$this->extend('/Layout/default');

//PreferencesComponent::includePrefsViewBundle() supports this element
echo $this->element('pagination');

echo $this->fetch('content');

//add search tools
echo '<h1>Search Tools Here</h1>';
