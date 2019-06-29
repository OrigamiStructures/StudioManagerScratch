<?php
namespace App\Model\Lib;

use Cake\Datasource\Paginator;

/**
 * StackPaginator
 * 
 * Subclass Paginator used by PaginationComponent to make it operate 
 * on Stack results
 *
 * @author dondrake
 */
class StackPaginator extends Paginator {
	
    public function paginate($object, array $params = [], array $settings = [])
    {
		
		$distillation = $object->distill($settings['seed'], $settings['ids']);
		$table = $distillation['Table'];
		$query = $table->find('all')->where(['id IN' => $distillation['IDs']]);
		
		$result = parent::paginate($query, $params, $settings);
		
		$IDs = $result->reduce(function($accum, $entity) {
			$accum[] = $entity->id;
			return $accum;
		}, []);
		return $object->stacksFromRoot($IDs);
    }

}
