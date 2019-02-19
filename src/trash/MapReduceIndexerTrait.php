<?php
namespace App\Model\Entity\Traits;

/**
 * AssignmentTrait provides a common Piece access interface for Editions and Formats
 * 
 * Piece assignment processes require access to pieces of various categories 
 * which are children of both Edition and Format entities. This trait normalizes 
 * things across the two classes.
 *
 * @author dondrake
 */
trait MapReduceIndexerTrait {
		
	
	/**
	 * Mapper callable for mapReducer method on the query
	 * 
	 * Produces an array of entities indexed by their id
	 * 
	 * @param Entity $entity
	 * @param int $key
	 * @param object $mapReduce
	 */
	public function indexer($entity, $key, $mapReduce) {
		$mapReduce->emitIntermediate($entity, $entity->id);
	}
	
	/**
	 * Reducer callable for mapReducer method on the query
	 * 
	 * Thwarts the reducer->emit. Lets the id-indexed array pass through
	 * 
	 * @param array $entity
	 * @param int $key
	 * @param object $mapReduce
	 */
	public function passThrough($entity, $key, $mapReduce) {
		$mapReduce->emit($entity[0], $key);
	}

}
