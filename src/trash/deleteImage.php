<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Model\Behavior;

use Cake\ORM\Behavior;

/**
 * CakePHP deleteImage
 * @author dondrake
 */
class deleteImage extends Behavior {
	
	public function beforeSave($arg, $arg2, $arg3) {
		die('in behavior beforeSave');
	}
	
}
