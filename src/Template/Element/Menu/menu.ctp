<?php
if (isset($artworks)) {
	$combined = (new Cake\Collection\Collection($artworks))->combine(
		function($artworks) { return $artworks->title; }, 
		function($artworks) { return "create/{$artworks->id}"; }
	);
	$menus['Artwork']['Edit'] = $combined->toArray();
}
echo $this->DropDown->menu($menus);
