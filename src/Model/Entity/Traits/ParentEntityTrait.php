<?php
namespace App\Model\Entity\Traits;

/**
 * Description of ParentEnitityTrait
 *
 * @author dondrake
 */
trait ParentEntityTrait {
	
		public function indexOfRelated($association, $format_id) {
		$index = FALSE;
		$count = 0;
		$max = count($this->$association);
		if ( $max > 0) {
			while ($count < $max && $this->{$association}[$count]->id != $format_id) {
				$count++;
			}
			$index = $count !== $max ? $count : FALSE;
		}
		return $index;
	}

}
