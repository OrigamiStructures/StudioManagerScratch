<?php

/*
 * Copyright 2015 Origami Structures
 */

namespace App\Model\Table;

use Cake\ORM\Table;
use App\Model\Table\AppTable;

/**
 * CakePHP MenusTable
 * @author jasont
 */
class MenusTable extends AppTable{
	
    public $menu;
	
	public function initialize(array $config) {
		parent::initialize($config);
	}
	
	public function assemble() {
		$this->artwork();
		$this->members();
		$this->disposition();
		$this->account();
		return $this->menu;
	}
	
	protected function artwork() {
		$this->menu['Artwork'] = [
            'Sample' => '/artworks/sample',
            'View All' => '/artworks/elementTest',
//          'View All' => '/artworks/index',
            'Create' => '/artworks/create',
			'View Artwork' => [],
			'Edit Artwork' => [],
			'Edition' => [],
			'Format' => [],
        ];
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
		if ($this->SystemState->admin('artist')) {
			$this->menu['Admin'] = [
				'Artist' => [],
			];
		}
		if ($this->SystemState->admin('system')){
			$this->menu['Admin']['Logs'] = [];
		}
		$this->menu['Account'] = [
			'Login' => '/users/login',
			'Logout' => '/users/logout',
			'Edit My Profile' => '/users/editProfile',
			'Update Payment Type' => '/users/updatePayment'
		];
	}
	
//	if (isset($artworks)) {
//	$combined = (new Cake\Collection\Collection($artworks))->combine(
//		function($artworks) { return $artworks->title; }, 
//		function($artworks) { return "/artworks/refine/artwork:{$artworks->id}"; }
//	);
//	$menus['Artwork']['Edit'] = $combined->toArray();
//}
//if (isset($editions)) {
//	$combined = (new Cake\Collection\Collection($editions))->combine(
//		function($editions) { return $editions->display_title; }, 
//		function($editions) { return "/editions/refine/{$editions->id}"; }
//	);
//	$menus['Edition'] = [
//		'Create' => "/editions/create/artwork:{$editions[0]->artwork_id}",
//		'Edit' => $combined->toArray()];
//}

}
