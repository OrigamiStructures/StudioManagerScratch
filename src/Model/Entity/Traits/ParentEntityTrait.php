<?php
namespace App\Model\Entity\Traits;

/**
 * Description of ParentEnitityTrait
 *
 * @author dondrake
 */
trait ParentEntityTrait {
	
		public function indexOfRelated($association, $format_id) {
		foreach ($this->$association as $index => $entity) {
			if ($this->{$association}[$index]->id == $format_id) {
				return $index;
			}
		}
		return FALSE;
	}

}
