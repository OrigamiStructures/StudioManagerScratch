<?php
namespace App\Model\Lib;

use Cake\Datasource\Paginator;
use Cake\ORM\Query;

/**
 * StackPaginator
 *
 * Subclass Paginator used by PaginationComponent to make it operate
 * on Stack results
 *
 * @author dondrake
 */
class StackPaginator extends Paginator {

	/**
	 * Implement pagination on a stack table query
	 *
	 * The stack query is packaged in a callable. Then the pagination is
	 * also packaged in a callable and passed to the stack process.
	 * The reason: pagination must happen happen to a query that is
	 * created part way through stack assembly. Sending the pagination
	 * processes in as a callable allows it to be on the scene for this
	 * mid-stream use.
     *
     * Adjustments are made to the pagingParams.
     * Repository-keyed sets are converted to scope-keyed sets so that
     * multiple independent sets from a single Table are possible.
	 *
	 * @todo Does this method have to do any additional work to make
	 *		 $params and $settings work properly?
	 *
	 * @param Callable $findStackCallable
     * @param array $params Request params
     * @param array $settings The settings/configuration used for pagination.
	 * @return Query
	 */
    public function paginate($findStackCallable, array $params = [], array $settings = []) {

		$paginatorCallable = function($query) use ($params, $settings) {
		    /*
		     * $query is lost by paginate() so we need to read repository
		     * name first for the params fix later
		     */
            $alias = $query->getRepository()->getAlias();
			$result = parent::paginate($query, $params, $settings);
			/*
			 * Paging params are stored by Repository alias. Since our's are
			 * locked in by the stack structure, we need a new name to keep separate
			 * sets on their own paging scheme when they are also from the same
			 * repository. So we migrate the data block onto a key = to the scope key.
			 * Scope is the query key for the page so this makes sense and works.
			 *
			 * The block is added to the request by some other code.
			 * debug $this->request->getParam('paging') to see the result
			 */
            $scope = $this->_pagingParams[$alias]['scope'];
			$this->_pagingParams = [$scope => $this->_pagingParams[$alias]];
			return $result;
		};

		return $findStackCallable($paginatorCallable);
    }

}
