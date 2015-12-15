<?php
namespace App\Lib;

use Cake\Network\Request;

/**
 * Description of ControlBlock
 *
 * @author dondrake
 */
class ControlBlock {
	
	public function __construct(Request $request) {
		$this->request = $request;
	}
//	
}
