<?php

/*
 * MenuTable generates arrays that can be translated into nested navigation tools
 *
 * Copyright 2015 Origami Structures
 */

namespace App\Model\Table;

use Cake\ORM\Table;
use App\Model\Table\AppTable;
use Cake\Collection\Collection;
use App\Lib\EditionTypeMap;

/**
 * CakePHP MenusTable
 * @author jasont
 */
class MenusTable extends AppTable{

    public $menu = ['Artwork' => []];

    protected $clearStudio = ['ClearStudio' => '/artworks/review'];

	protected $artwork = ['Artwork' => [
//            'Sample' => '/artworks/sample',
            'View All' => '/artworks/review',
            'New Edition' => '/artworks/create',
            'New Unique Work' => '/artworks/create_unique',
			'Review Artwork' => [],
			'Refine Artwork' => [],
			'Edition' => [],
			'Format' => [],
        ]];
	protected $member = ['Market' => [
			'View Members' => '/members/review',
			'View Categories' => '/groups/categories',
            'Create Organization' => '/members/create/Organization',
            'Create Person' => '/members/create/Person',
		]];
//    protected $disposition = ['Work Status' => [
	protected $disposition = ['[Dev-REPL-Pages]' => [
//        'Review' => '/dispositions/index',
        'test-artworks' => '/artworks/test_me',
        'test-supervision' => '/supervisors',
        'test-addressbook' => '/addressbook',
        'test-rolodex' => '/rolodexcards',
        'test-rolodex-institutions' => '/rolodexcards/groups',
        'test-pieces-renumber' => '/pieces/renumber?artwork=10',
        'documentation-REPL' => '/members/docs',
        'data-integrity' => '/administrator/userDataIntegrity',
        'artists-index' => '/artists',
        'artists-view' => '/artists/view/1'
    ]];
    protected $cardfile = ['Cardfile REPL' => [
        'Index' => '/cardfile/index',
        'Institutions' => '/cardfile/institutions',
        'People' => '/cardfile/people',
        'Categories' => '/cardfile/categories',
        'Groups' => '/cardfile/groups',
        'Supervisors' => '/cardfile/supervisors',
        'Add' => '/cardfile/add'
    ]];
    protected $account = ['Account' => [
        'Login' => '/users/users/login',
        'Logout' => '/users/users/logout',
        'Edit My Profile' => '/users/users/profile',
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
		$this->cardfile();
		$this->account();
		$this->admin();
		return $this->menu;
	}

	/**
	 * Establish the main menu keys and thier order
	 */
	protected function template() {
		$this->menu = $this->clearStudio + $this->artwork +
			$this->member + $this->disposition + $this->cardfile +
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

    protected function cardfile()
    {

	}

	protected function members() {
	}

	protected function disposition() {
	}

	protected function account() {
	}

	/**
	 * Set up the admin menus for the current circumstance
	 */
	protected function admin() {
		if (!$this->currentUser()->admin()) {
			unset($this->menu['Admin']);
		} elseif ($this->contextUser()->has('supervisor')) {
		    $supervisorCard = $this->contextUser()->getCard('supervisor');

		}
			// all admins have 'artist spoofing' capabilities
			$this->menu['Admin'] =
				[
					'Act as me' => [],
					// Acts as should be a list if there are few than ???
					// after that limit it should be a link to a choice page.
					// Probably a User/Account call to discover the list?
					'Act as...' => [], //$User->artists(),
				];
//		}
		if ($this->currentUser()->isSuperuser()) {
		    $this->menu['Admin']['System Supervisors'] = '/rolodexcards/supervisors';
        }
		if ($this->currentUser()->admin(ADMIN_SYSTEM)){
			$this->menu['Admin']['Logs'] = [];
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
	    return;
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

		// NEW RULE - not everything allows create

        return;
	}

	protected function addFormats() {

		// NEW RULE - not everything allows create

        return;
	}

	protected function allowNewFormat($edition) {
		return EditionTypeMap::isMultiFormat($edition->type);
	}

}
