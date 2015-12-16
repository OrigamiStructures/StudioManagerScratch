<?php
namespace App\Model\Behavior;

use Cake\ORM\Behavior;
use Cake\ORM\Query;

/**
 * CakePHP FamilyBehavior
 * @author dondrake
 */
class FamilyBehavior extends Behavior {
	
	/**
	 * Find the records that have a specific parent, return as a list
	 * 
	 * Given and id and the name portion of a link column which in 
	 * combination describe a parent record, return a key => value list of 
	 * all records in this table that are linked to the identified parent.
	 * 
	 * @param Query $query
	 * @param array $options [$id, $index_name, ''=>'']
	 * @return array
	 */
	public function findMemberList(Query $query, $options) {
		$artist_id = $this->_table->SystemState->artistId();
		$options += [
			'id' => FALSE, 
			'index_name' => FALSE, 
			'keyField' => $this->_table->primaryKey(), 
			'valueField' => $this->_table->displayField(), 
			'group' => FALSE];
		if (!($options['id'] && $options['index_name'] && $artist_id)) {
			throw new \BadFunctionCallException('id and index_id required');
		}
		
		if (!$cache) {
			if (!$options['group']) {
				unset($options['group']);
			}
			$index_field = $options['index_name'].'_id';

			$query->find('list', $options)
					->where([$index_field => $options['id'], 'user_id' => $artist_id]);
			// cache query
		}
		return $query;
	}
	
	/**
	 * Find the records that have a specific parent, return the records
	 * 
	 * Given and id and the name portion of a link column which in 
	 * combination describe a parent record, return the entities for 
	 * all records in this table that are linked to the identified parent.
	 * 
	 * @param Query $query
	 * @param array $options
	 * @return array
	 */
	public function findMembers(Query $query, $options) {
		$artist_id = $this->_table->SystemState->artistId();
		list($id, $index_name) = $options;
		
		return $query->where([$index_field => $id, 'user_id' => $artist_id]);
	}
	
	public function findChildList(Query $query, $options) {
		list($id, $index_name) = $options;
		
	}
	
	public function findChildren(Query $query, $options) {
		list($id, $index_name) = $options;
		
	}
	
	public function findParent(Query $query, $options) {
		list($id, $index_name) = $options;
		
	}
	
}
