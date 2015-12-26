<?php
if (isset($artworks)) {
	$combined = (new Cake\Collection\Collection($artworks))->combine(
		function($artworks) { return $artworks->title; }, 
		function($artworks) { return "refine/artwork:{$artworks->id}"; }
	);
	$menus['Artwork']['Edit'] = $combined->toArray();
}
if (isset($editions)) {
	$combined = (new Cake\Collection\Collection($editions))->combine(
		function($editions) { return $editions->display_title; }, 
		function($editions) { return "refine/{$editions->id}"; }
	);
	$menus['Edition'] = ['Edit' => $combined->toArray()];
}
echo $this->DropDown->menu($menus);
