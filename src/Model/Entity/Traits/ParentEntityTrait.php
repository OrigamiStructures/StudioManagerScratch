<?php
namespace App\Model\Entity\Traits;

use Cake\Utility\Inflector;

/**
 * Description of ParentEnitityTrait
 *
 * @author dondrake
 */
trait ParentEntityTrait {
	
	public function __call($name, $arguments) {
		// return the index of an association entity
		if (preg_match('/indexOf(.*)/', $name, $match)) {
			list($id) = $arguments;
			$association = strtolower(Inflector::pluralize($match[1]));
			return $this->indexOfRelated($association, $id);
			
		// return the associated entity that has the ID
		} elseif (preg_match('/return(.*)/', $name, $match)) {
			list($id) = $arguments;
			$association = strtolower(Inflector::pluralize($match[1]));
			if (is_array($this->$association)) {
				$index = $this->indexOfRelated($association, $id);
				$node = $index !== FALSE ? $this->{$association}[$index] : FALSE;
				
			}
			return $node;
		}
	}
	
	public function indexOfRelated($association, $format_id) {
		if (is_array($this->$association)) {
			foreach ($this->$association as $index => $entity) {
				if ($this->{$association}[$index]->id == $format_id) {
					return $index;
				}
			}
		}
		return FALSE;
	}
	
	protected function _key(array $args) {
		return implode('_', $args);
	}
}
