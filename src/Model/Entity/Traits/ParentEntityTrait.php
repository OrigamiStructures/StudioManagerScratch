<?php
namespace App\Model\Entity\Traits;

use Cake\Utility\Inflector;

/**
 * Description of ParentEnitityTrait
 *
 * @author dondrake
 */
trait ParentEntityTrait {
	
	/**
	 * Provide access to child entities or child index discovery
	 * 
	 * Return the index of the child entity named and ID'd
	 * eg:
	 * $artwork->indexOfEdition(412);
	 * $pieces->indexOfDispostion(1945);
	 * 
	 * Return the child entity of the type named that has the indicated ID
	 * eg:
	 * $artwork->returnEdition(412);
	 * $piece->returnDispostion(1945);
	 * 
	 * @param string $name
	 * @param array $arguments
	 * @return string|entity|false
	 */
	public function __call($name, $arguments) {

		if (preg_match('/indexOf(.*)/', $name, $match)) {
			list($id) = $arguments;
			$association = strtolower(Inflector::pluralize($match[1]));
			return $this->indexOfRelated($association, $id);
			
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
	
	/**
	 * Return the index of a child entity
	 * 
	 * Given an association name and record id, return the 
	 * index position of the entity in the set of association records
	 * 
	 * @param string $association
	 * @param string $id
	 * @return string|boolean
	 */
	public function indexOfRelated($association, $id) {
		if (is_array($this->$association)) {
			foreach ($this->$association as $index => $entity) {
				if ($this->{$association}[$index]->id == $id) {
					return $index;
				}
			}
		}
		return FALSE;
	}
	
	/**
	 * To support Entity methods that generate ancestor lists
	 * 
	 * Entities often to provide information about their ancestory. 
	 * This information is encoded in their stored foreign keys. 
	 * They contain a ->key() method to output the ancestor string 
	 * and this method standardizes the string assembly methodology. 
	 * 
	 * This seems to be focused on letting Pieces identify 
	 * their lineage. Though it might appear to be of general 
	 * use, there is currently no other implementation.
	 * 
	 * @param array $args
	 * @return type
	 */
	protected function _key(array $args) {
		return implode('_', $args);
	}
}
