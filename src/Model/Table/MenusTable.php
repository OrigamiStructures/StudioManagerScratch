<?php

/*
 * MenuTable generates arrays that can be translated into nested navigation tools
 * 
 * Navigation lists are built from standing arrays and synthesized from values 
 * stored in teh SystemState property. No data tables exist.
 * 
 * Copyright 2015 Origami Structures
 */

namespace App\Model\Table;

use Cake\ORM\Table;
use App\Model\Table\AppTable;
use Cake\Collection\Collection;

/**
 * CakePHP MenusTable
 * @author jasont
 */
class MenusTable extends AppTable{
	
    public $menu = ['Artwork' => []];
	
	public function initialize(array $config) {
		parent::initialize($config);
	}
	
	/**
	 * Call point to get a main navigation menu
	 * 
	 * @return array
	 */
	public function assemble() {
		$this->artwork();
		$this->members();
		$this->disposition();
		$this->account();
		$this->admin();
		return $this->menu;
	}
	
	/**
	 * Makes an Artwork stack menu for the current context
	 * 
	 * THIS METHOD DOES NOT produce menus with a consistent order. FIX IT. 
	 */
	protected function artwork() {
		$this->addArtworks();
		$this->addArtwork();
		$this->menu['Artwork'] = array_merge_recursive($this->menu['Artwork'], [
            'Sample' => '/artworks/sample',
            'View All' => '/artworks/review',
            'Create' => '/artworks/create',
			'Review Artwork' => [],
			'Refine Artwork' => [],
			'Edition' => [],
			'Format' => [],
        ]);
		// check state for master query to build more layers
	}
	
	protected function members() {
		$this->menu['Market'] = [
			'Collectors' => [],
			'Venues' => [],
			'Representitives' => [],
			'Groups' => [],
		];
	}
	
	protected function disposition() {
		$this->menu['Dispostion'] = [
			'Go to Dispo' => '/disposition/index',
        ];
	}
	
	protected function account() {
		$this->menu['Account'] = [
			'Login' => '/users/login',
			'Logout' => '/users/logout',
			'Edit My Profile' => '/users/editProfile',
			'Update Payment Type' => '/users/updatePayment'
		];
	}
	
	protected function admin() {
		if ($this->SystemState->admin('artist')) {
			$this->menu['Admin'] = [
				'Artist' => [],
			];
		}
		if ($this->SystemState->admin('system')){
			$this->menu['Admin']['Logs'] = [];
			$this->menu['Admin']['Remap States'] = '/artworks/map_states';
		}
	}

		/**
	 * Generate navigation choices from a page of Artworks records
	 * 
	 * Will produce both a Refine and Review link for each Artwork 
	 * 
	 * COMBINE EVERYTHING INTO A SINGLE LOOP?
	 * 
	 * @return array
	 */
	protected function addArtworks() {
		if (is_null($this->SystemState->menu_artworks)) {
			return;
		} else {
			$artworks = $this->SystemState->menu_artworks;
		}
		$combined = (new Collection($artworks))->combine(
			function($artworks) { return $artworks->title; }, 
			function($artworks) { return "/artworks/refine?artwork={$artworks->id}"; }
		);
		$this->menu['Artwork'] = ['Refine Artwork' => $combined->toArray()];
		$combined = (new Collection($artworks))->combine(
			function($artworks) { return $artworks->title; }, 
			function($artworks) { return "/artworks/review?artwork={$artworks->id}"; }
		);
		$this->menu['Artwork']['Review Artwork'] = $combined->toArray();
	}
	
	/**
	 * Generate navigation choices from a single Artwork record
	 * 
	 * Will produce both Refine and Review links for each Edition in the 
	 * Arwork. SHOULD ALSO PRODUCE Refine and Review for the Artwork and 
	 * Refine and Review for the Formats (? for the Formats)
	 * 
	 * @return array
	 */
	protected function addArtwork(){
		if (is_null($this->SystemState->menu_artwork)) {
			return;
		} else {
			$editions = $this->SystemState->menu_artwork->editions;
		}
		$refine = (new Collection($editions))->combine(
			function($editions) { return $editions->display_title; }, 
			function($editions) { return "/editions/refine?artwork={$editions->artwork_id}&edition={$editions->id}"; }
		);
		$review = (new Collection($editions))->combine(
			function($editions) { return $editions->display_title; },
			function($editions) { return "/editions/review?artwork={$editions->artwork_id}&edition={$editions->id}"; }
		);
		$this->menu['Artwork']['Edition'] = [
			'Create' => "/editions/create?artwork={$this->SystemState->menu_artwork->id}",
			'Refine' => $refine->toArray(),
			'Review' => $review->toArray(),
		];
	}

}
