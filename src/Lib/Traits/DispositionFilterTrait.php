<?php
namespace App\Lib\Traits;

use App\Lib\DispositionTypeMap;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DispositionFilterTrait
 *
 * @author dondrake
 */
Trait DispositionFilterTrait {
	
	public function filterUnavailable($disposition, $key){
			if (in_array($disposition->type, DispositionTypeMap::scrappedDispositionTypes())) {
				return TRUE;
			}
			
	}
	
	public function filterAvailable($disposition, $key){
			return !in_array($disposition->type, DispositionTypeMap::scrappedDispositionTypes());
	}
	
	public function filterNotFutureEvent($disposition, $key) {
		return TRUE;
	}
	
}
