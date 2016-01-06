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
	
	protected $artwork = ['Artwork' => [
            'Sample' => '/artworks/sample',
            'View All' => '/artworks/review',
            'Create' => '/artworks/create',
			'Review Artwork' => [],
			'Refine Artwork' => [],
			'Edition' => [],
			'Format' => [],
        ]];
	protected $member = ['Market' => [
			'Collectors' => [],
			'Venues' => [],
			'Representitives' => [],
			'Groups' => [],
		]];
	protected $disposition = ['Dispostion' => [
			'Go to Dispo' => '/disposition/index',
        ]];
	protected $account = ['Account' => [
			'Login' => 'users/users/login',
			'Logout' => 'users/users/logout',
			'Edit My Profile' => '/users/editProfile',
			'Update Payment Type' => '/users/updatePayment'
		]];
	protected $admin = ['Admin' => [
				'Artist' => [],
			]];


	public function initialize(array $config) {
		parent::initialize($config);
	}
	
	/**
	 * Call point to get a main navigation menu
	 * 
	 * @return array
	 */
	public function assemble() {
		$this->template();
		$this->artwork();
		$this->members();
		$this->disposition();
		$this->account();
		$this->admin();
		return $this->menu;
	}
	
	/**
	 * Establish the main menu keys and thier order
	 */
	protected function template() {
		$this->menu = $this->artwork + 
			$this->member + $this->disposition +
			$this->account + $this->admin;
	}

		/**
	 * Makes an Artwork stack menu for the current context
	 */
	protected function artwork() {
		$this->addArtworks();
		$this->addEditions();
		$this->addFormats();
	}
	
	protected function members() {
	}
	
	protected function disposition() {
	}
	
	protected function account() {
	}
	
	protected function admin() {
		if ($this->SystemState->admin('system')){
			$this->menu['Admin']['Logs'] = [];
			$this->menu['Admin']['Remap States'] = '/artworks/map_states';
		}
	}

	/**
	 * Generate navigation choices from a page of Artworks records
	 * 
	 * Will produce both a Refine and Review link for each Artwork.
	 * Will work automatically for the standard $artworks array that is 
	 * used to render views, or the $artwork variable if only one is known 
	 * 
	 * COMBINE EVERYTHING INTO A SINGLE LOOP?
	 * 
	 * @return array
	 */
	protected function addArtworks() {
		if (is_null($this->SystemState->menu_artworks)) {
			if (is_null($this->SystemState->menu_artwork)) {
					return;
			}
			$artworks = [$this->SystemState->menu_artwork];
		} else {
			$artworks = $this->SystemState->menu_artworks;
		}
		$combined = (new Collection($artworks))->combine(
			function($artworks) { return $artworks->title; }, 
			function($artworks) { return "/artworks/refine?artwork={$artworks->id}"; }
		);
		$this->menu['Artwork']['Refine Artwork'] = $combined->toArray();
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
	 * Arwork. SHOULD ALSO PRODUCE Refine and Review for the Formats?
	 * 
	 * @return array
	 */
	protected function addEditions(){
		if (is_null($this->SystemState->menu_artwork)) {
			return;
		}
		$editions = $this->SystemState->menu_artwork->editions;
		
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
	
	protected function addFormats() {
		if (is_null($this->SystemState->menu_artwork)) {
			return;
		}
		$editions = $this->SystemState->menu_artwork->editions;
		$many_editions = count($editions) > 1;
		
		foreach ($editions as $index => $edition) {
			$formats = $edition->formats;
			$query_args = "?artwork={$edition->artwork_id}&edition={$edition->id}";
			
			$refine = $review = [];
			foreach ($edition->formats as $index => $format) {
				$refine[$format->display_title] = "/formats/refine$query_args&format={$format->id}";
				$review[$format->display_title] = "/formats/review$query_args&format={$format->id}";
			}
			
			if ($many_editions) {
				$this->menu['Artwork']['Format'][$edition->display_title] = [
					'Create' => "/formats/create$query_args",
					'Refine' => $refine,
					'Review' => $review
				];
			} else {
				$this->menu['Artwork']['Format'] = [
					'Create' => "/formats/create$query_args",
					'Refine' => $refine,
					'Review' => $review
			];
			}
		}
	}

}
