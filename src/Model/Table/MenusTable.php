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
    public $adminMenu = [
        'Account' => [
            'Login' => 'users/login',
            'Logout' => 'users/logout',
            'Edit My Profile' => 'users/editProfile',
            'Update Payment Type' => 'users/updatePayment'
        ],
        'Artwork' => [
            'View All' => 'artworks/index',
            'Create' => 'artworks/new'
        ],
        'Disposition' => [
            'Go to Dispo' => 'disposition/index'
        ],
        'Admin' => [
            'Subscribers' => 'admin/subscribers',
            'CRUD' => 'admin/crud',
            'Database' => 'phpmysql/index',
            'Logs' => 'admin/logs'
        ]
    ];
}
