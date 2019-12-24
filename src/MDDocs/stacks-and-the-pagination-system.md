##The situation at present

Pagination works with the stack system through a substitute Paginator `App\Model\Lib\StackPaginator`. This class is put in the path of normal cake flow by **AppController**.

```php
class AppController extends Controller {

    public function initialize()
    {
        parent::initialize();
		$this->loadComponent('Paginator', ['paginator' => new StackPaginator()]);
	}
}

```

###Normal Pagination Process

This is the standard procedure for paginating data:

![A sequence diagram showing the flow of a typical controller pagination request using the built-in pagination component](/img/images/image/2cdb58f8-7fae-4c47-bb65-b974052e044b/native-cake-pagination.png "Standard Cake pagination request")

In reality, the first argument to Controller::paginate() can be data other than a query, but this variation is the simplest to understand. All variants return a ResultSet object.

Cake queries are returned as trees of data. Pagination is operating on the main data source, not contained data.

Calling `PaginatorDatasource::paginate()` with a suitable query when `datasource` is properly composed into the component is required for proper function of the component and coordination with the PaginationHelper.

**The important features**

The native PaginatorComponent passes the query parameters along to the PaginatorDatasource with the addition of the current request. The sequence passes a ResultSet object back to the original caller.

###Normal Stack Request

This is a standard `find('stacksFor')` request:

![A sequence diagram showing the flow of a typical controller 'stacks for' request.](/img/images/image/5b5e3948-03d2-4bc7-b20b-79a15b9b71e2/non-paginated-stack-query.png "A simple Studio Manager stack query")

Step 2 is some process you might go through to find a set of seeds that are unique to the data set you need. With those seeds, you can request the stacks that will fully contextualize around those seeds (step 4).

Step 5 distills the seeds down to the full set of ids for the stack root entity. The resulting ids (Step 6) is the data that should be paginated.

**Important differences from the built in flow**

PaginatorComponent::paginate() expects, as its first argument, a query that can be modified with `limit` and `page`. We don't have access to a query like that until well into the stack creation process. This is the first incompatability with the PaginatorComponent; a query that can respond to `page` and `limit` is not available until the proper component context is lost.

```php
 $this->loadComponent('Paginator', ['paginator' => new StackPaginator()]);
```

The second incompatability is the return type. The stack system returns a StackSet rather than a ResultSet object.

###Paginated Stack Request

To make our system work with the paginator, we need to defer the `PaginatorDatasource::paginate()` until partway through the StackSet creation process. And we need make our call-sequence return a StackSet rather than a ResultSet.

CakePHP has made the PaginatorDatasource object configurable for just such situations. We extend the native datasource with **StackPaginator** to accomplish both our goals.

This is the flow that makes our stack system work with the native Pagination system:

![A sequence diagram showing the flow of a controller pagination request for a stack set using the standard pagination component and a extended paginator datasource](/img/images/image/f2a41a14-88af-4b1b-b8a2-6c11e7748f03/paginated-stack-query.png "A paginated stack query")

Our replacement datasource takes our stack query wrapped in a callable. It then makes another callable that is the properly contextualized `PaginatorDatasource::paginate()` request. It places that pagination-callable on our callable-wrapped stack query. Then it runs the newly enhanced stack creation process. That process watches for the presence of the inserted paginate-callable. If found, it provides the required query, runs the callable, then continues with the now-paginated root-set.

Gads! I hope the sequence diagram make things clear because describing it makes no sense.

The code:

```php
// in a controller

    //$stackCall will be waiting for $paginator (callable)
    $stackCall = function($paginator) use ($PersonCards, $ids) {
        return $PersonCards->find(
                'stacksFor',
                ['seed' => 'identity', 'ids' => $ids, 'paginator' => $paginator]
            );
    };

    //this reaches the Component, but that will pass things on to the datasource
    $results = $this->paginate($stackCall);

// the StackPaginator datasource class
// called by PaginatorComponent
class StackPaginator extends Paginator {

    public function paginate($findStackCallable, array $params = [], array $settings = []) {

        //the pagination call will be waiting for us to provide the proper query object
        $paginatorCallable = function($query) use ($params, $settings) {
            return parent::paginate($query, $params, $settings);
        };

        //now we provide the paginator call and run the stack query process
        return $findStackCallable($paginatorCallable);
    }
}
```

##Plan for improvements

