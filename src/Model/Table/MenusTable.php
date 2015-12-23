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
	
    public $adminMenu;
	
	public function initialize(array $config) {
		parent::initialize($config);
		$this->adminMenu();
	}
	
	/**
	 * Define basic menus for administators
	 * 
	 * Doing this here allows methods to run in the array definition. 
	 * Setting this in a property declaration would prevent menthods. 
	 */
	protected function adminMenu() {
		$this->adminMenu = [
        'Account' => [
            'Login' => '/users/login',
            'Logout' => '/users/logout',
            'Edit My Profile' => '/users/editProfile',
            'Update Payment Type' => '/users/updatePayment'
        ],
        'Artwork' => [
            'View All' => '/artworks/index',
            'Create' => '/artworks/new',
            'Sample' => '/artworks/sample',
            'Element Test' => '/artworks/elementTest'
        ],
        'Disposition' => [
            'Go to Dispo' => '/disposition/index',
			'Phony' => [
				'here' => '/artificially deep'
			],
        ],
        'Admin' => [
            'Subscribers' => '/admin/subscribers',
            'CRUD' => '/admin/crud',
            'Database' => '/phpmysql/index',
            'Logs' => '/admin/logs',
            'Element Test' => '/artworks/elementTest'
        ]
    ];
	}
}
