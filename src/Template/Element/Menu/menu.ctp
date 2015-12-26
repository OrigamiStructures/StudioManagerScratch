<?php
if (isset($artworks)) {
	$combined = (new Cake\Collection\Collection($artworks))->combine(
		function($artworks) { return $artworks->title; }, 
		function($artworks) { return "/artworks/refine/artwork:{$artworks->id}"; }
	);
	$menus['Artwork']['Edit'] = $combined->toArray();
}
if (isset($editions)) {
	$combined = (new Cake\Collection\Collection($editions))->combine(
		function($editions) { return $editions->display_title; }, 
		function($editions) { return "/editions/refine/{$editions->id}"; }
	);
	$menus['Edition'] = [
		'Create' => "/editions/create/artwork:{$editions[0]->artwork_id}",
		'Edit' => $combined->toArray()];
}
echo $this->DropDown->menu($menus);
