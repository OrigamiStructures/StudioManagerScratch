This describes simple Layer use. For more detail about layers and the more complex data aggregates that contain them see:

- [Interacting with Layer Objects](/article/interacting-with-layer-objects "Interacting with Layer Objects")
- [Interacting with StackEntities](/article/interacting-with-stackentities "Interacting with StackEntities")
- [Interacting with StackSet Objects.](/article/interacting-with-stackset-objects "Interacting with StackSet Objects.")

##What are Layers
 Layers are wrapper objects that simplify the use of arrays of entities.

**Content Rules**

- The entities in the array must all be the same type
- The `id` column must be included and must be named `id`

Once an array is wrapped in a Layer you will have tools to filter and retrieve the contained data.

##Making a Layer

Query the db to get an array of entities

```php
$members = $this->Members->find('all')
    ->select(['id', 'first_name', 'last_name', 'user_id', 'member_type')
    ->order(['id' => 'DESC'])
    ->limit(3);
```

if we unpack and output the result we'll see the typical query result array. 
This is the correct data to wrap in a Layer.

```php
debug($members->toArray());

[
	(int) 0 => object(App\Model\Entity\Member) {

		'id' => (int) 75,
		'first_name' => 'Leonardo',
		'last_name' => 'DiVinci',
		'user_id' => '708cfc57-1162-4c5b-9092-42c25da131a9',
		'member_type' => 'Person',
		'[new]' => false,
		'[dirty]' => [],
		'[original]' => [],
		'[repository]' => 'Members'
	
	},
	(int) 1 => object(App\Model\Entity\Member) {

		'id' => (int) 74,
		'first_name' => 'Bay Area Book Artists',
		'last_name' => 'Bay Area Book Artists',
		'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
		'member_type' => 'Category',
		'[new]' => false,
		'[dirty]' => [],
		'[original]' => [],
		'[repository]' => 'Members'
	
	},
	(int) 2 => object(App\Model\Entity\Member) {

		'id' => (int) 73,
		'first_name' => 'Sheila',
		'last_name' => 'Botein',
		'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
		'member_type' => 'Person',
		'[new]' => false,
		'[dirty]' => [],
		'[original]' => [],
		'[repository]' => 'Members'
	
	}
]
```

With this array we can make a Layer object.

```php
$memberLayer = new App/Model/Lib/Layer($members);

//or use the global function

$memberLayer = layer($members);

debug($memberLayer);

object(App\Model\Lib\Layer) {
	[protected] _layer => 'member'
	[protected] _className => 'Member'
	[protected] _data => [
	   (int) 75 => object(App\Model\Entity\Member) {

		'id' => (int) 75,
		'first_name' => 'Leonardo',
		'last_name' => 'DiVinci',
		'member_type' => 'Person',

	     },
	     (int) 74 => object(App\Model\Entity\Member) {

		'id' => (int) 74,
		'first_name' => 'Bay Area Book Artists',
		'last_name' => 'Bay Area Book Artists',
		'member_type' => 'Category',

	     },
	     (int) 73 => object(App\Model\Entity\Member) {

		'id' => (int) 73,
		'first_name' => 'Sheila',
		'last_name' => 'Botein',
		'member_type' => 'Person',

	     }
           ]
	[protected] _entityProperties => [
		(int) 0 => 'id',
		(int) 1 => 'first_name',
		(int) 2 => 'last_name',
		(int) 3 => 'user_id',
		(int) 4 => 'member_type'
	]
	[protected] primary => null
	[protected] _errors => []
}
```
As you can see, your data is now in a protected property of the Layer object.

##Class Introspection

###layerName()

This method mostly exists to support integration of Layers into larger structures, 
but if you need to know the layer's name:

```php
 debug($memberLayer->layerName());

'member'

```

###entityClass($style = 'bare')

Typically the layer name matches the contained entities, but this need not be the case. 
By default `entityClass()` returns the simple class name, but the optional argument lets you get a 
fully namespaced version.

```php
 debug($memberLayer->entityClass());

'Member'

// any value other than 'bare' will get the namespaced version
 debug($memberLayer->entityClass('namespace'));

'App\Model\Entity\Member'

```

###hasId($id)

Returns a boolean indicating if the Layer contains an entity with the id value.

```php
 debug($memberLayer->hasId(33));

false

 debug($memberLayer->hasId(75));

true
```

###isClean()

Returns `true` if all entities in the stack are clean, `false` if any have been modified.

```php
debug($memberLayer->isClean());

true

//shift() gets an entity from the layer as described later in this document
$MembersTable->patchEntity($memberLayer->shift(), ['person_type' => 'Gallery']);
debug($memberLayer->isClean());

false

debug($memberLayer->load());

[
	(int) 75 => object(App\Model\Entity\Member) {

		'id' => (int) 75,
		'first_name' => 'Leonardo',
		'last_name' => 'DiVinci',
		'user_id' => '708cfc57-1162-4c5b-9092-42c25da131a9',
		'member_type' => 'Person',
		'person_type' => 'Gallery',
		'[dirty]' => [
			'person_type' => true
		],
	
	},
	(int) 74 => object(App\Model\Entity\Member) {

		'id' => (int) 74,
		'first_name' => 'Bay Area Book Artists',
		'last_name' => 'Bay Area Book Artists',
		'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
		'member_type' => 'Category',
		'[dirty]' => [],
	
	},
	(int) 73 => object(App\Model\Entity\Member) {

		'id' => (int) 73,
		'first_name' => 'Sheila',
		'last_name' => 'Botein',
		'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
		'member_type' => 'Person',
		'[dirty]' => [],
	
	}
]
```

###count()

`count()` tells you how many entities are stored.

```php
echo $memberLayer->count();

3
```

##Data Access Methods

###load()

To retrieve the array entities, simply use the `load()` method. 

Note that the data is now indexed by ID rather than 0...n as in the original query.

*`load()` plays an important role in **[Advanced Use](#advanced-use)** features. 
`load()` is to Layers as `toArray()` is to Query*

```php
debug($memberLayer->load());

[
	(int) 75 => object(App\Model\Entity\Member) {

		'id' => (int) 75,
		'first_name' => 'Leonardo',
		'last_name' => 'DiVinci',
		'member_type' => 'Person',
	
	},
	(int) 74 => object(App\Model\Entity\Member) {

		'id' => (int) 74,
		'first_name' => 'Bay Area Book Artists',
		'last_name' => 'Bay Area Book Artists',
		'member_type' => 'Category',
	
	},
	(int) 73 => object(App\Model\Entity\Member) {

		'id' => (int) 73,
		'first_name' => 'Sheila',
		'last_name' => 'Botein',
		'member_type' => 'Person',

	}
]
```

###element($key, $byIndex = LAYERACC_INDEX)

The `element()` method allows you to retrieve a single entity. 
By default, `element()` will treat the array as though it was indexed 0...n 
like a traditional result array.

An optional second argument allows you to retrieve elements by id.

```php
debug($memberLayer->element(1); 
// same as element(1, LAYERACC_INDEX) 
// same as element(1, true) 

[
	(int) 74 => object(App\Model\Entity\Member) {

		'id' => (int) 74,
		'first_name' => 'Bay Area Book Artists',
		'last_name' => 'Bay Area Book Artists',
		'member_type' => 'Category',
	
	}
]

debug($memberLayer->element(74, LAYERACC_ID); 
// same as element(74, false) 

[
	(int) 74 => object(App\Model\Entity\Member) {

		'id' => (int) 74,
		'first_name' => 'Bay Area Book Artists',
		'last_name' => 'Bay Area Book Artists',
		'member_type' => 'Category',
	
	}
]
```

###IDs()

`IDs()` returns an array containing the entity IDs

```php
debug($memberLayer->IDs());

 [
  0 => 75
  1 => 74
  2 => 73
]
```

###distinct($sourcePoint)

`distinct()` will return an array of the distinct values of one property.

*Note: The return arrays are indexed 0...n.*

```php
debug($memberLayer->distinct('member_type'));

[
	(int) 0 => 'Person',
	(int) 1 => 'Category'
]
```

If your entity has methods that don't require arguments, you can also get 
the distinct results of those methods. For example, these Member entities 
have a `name()` method that delivers a full name for the record.

```php
 debug($memberLayer->distinct('name'));

[
	(int) 0 => 'Leonardo DiVinci',
	(int) 1 => 'Bay Area Book Artists',
	(int) 2 => 'Sheila Botein'
]
```

###valueList($sourcePoint)

`valueList()` returns an array of values from a property or method 
that doesn't take arguments.

```php
debug($memberLayer->valueList('last_name'));

[
	(int) 0 => 'DiVinci',
	(int) 1 => 'Bay Area Book Artists',
	(int) 2 => 'Botein'
]

//Member has a method 
//public function name() {}
debug($memberLayer->valueList('name'));

[
	(int) 0 => 'Leonardo DiVinci',
	(int) 1 => 'Bay Area Book Artists',
	(int) 2 => 'Sheila Botein'
]
```

###keyValueList($keySource, $valueSource)

`keyValueList($keySource, $valueSource)` returns a hash with the keys and 
values drawn from properties and methods of the entities. If a method 
is used as a source it can't require arguments.

```php
debug($memberLayer->valueList('id', 'last_name'));

[
	(int) 75 => 'DiVinci',
	(int) 74 => 'Bay Area Book Artists',
	(int) 73 => 'Botein'
]

//Member has a method 
//public function name() {}
debug($memberLayer->valueList('last_name', 'name'));

[
	(string) 'DiVinci' => 'Leonardo DiVinci',
	(string) 'Bay Area Book Artists' => 'Bay Area Book Artists',
	(string) 'Botein' => 'Sheila Botein'
]
```

###linkedTo($foreignKey, $foreignId)

`linkedTo()` provides a quick way to locate certain kinds of associated data. 

This method assume the foreign key property in the entity will follow Cake 
conventions (*name*\_id). The value you provide must be the *name* portion 
of the key.

```php
debug($memberLayer->linkedTo('user', '708cfc57-1162-4c5b-9092-42c25da131a9'));

[
	(int) 75 => object(App\Model\Entity\Member) {

		'id' => (int) 75,
		'first_name' => 'Leonardo',
		'last_name' => 'DiVinci',
		'user_id' => '708cfc57-1162-4c5b-9092-42c25da131a9',
		'member_type' => 'Person',
	}
]
```

###shift()

As was the case with `layerName()`, `shift()`'s use is primarily in more complex structures.

However, there may be times when you can be sure there is only one stored entitiy. 
Or you may specifically want the first (or a sample) entity. 

*NOTE:* If you use `load()` on a layer with one member, you will get an array containing 
one entity. `shift()` returns the entity itself.

```php
$members = $this->Members->find('first')
    ->select(['id', 'first_name', 'last_name', 'user_id', 'member_type');

$memberLayer = layer($members); // uses the global function for construction
                                // see Making a Layer

debug($memberLayer->shift());

object(App\Model\Entity\Member) {

	'id' => (int) 75,
	'first_name' => 'Leonardo',
	'last_name' => 'DiVinci',
	'member_type' => 'Person',

}

// compare to 
debug($memberLayer->load());

[
	(int) 0 => object(App\Model\Entity\Member) {

		'id' => (int) 75,
		'first_name' => 'Leonardo',
		'last_name' => 'DiVinci',
		'member_type' => 'Person',	
	}
[
```

`shift()` does not alter the content of the Layer like its namesake, the php method 
`array_shift()`. From the example above it may be clear that internally it is 
performing `return array_shift($layerObject->load());`. The product of the `load()` 
command is modified, but the Layer properties are uneffected.

##Data Manipulation Methods

###sort($property, $dir = \SORT/\_DESC, $type = \SORT\_NUMERIC)

Don't neglect the `$type` parameter. Sorting with the wrong value here can prevent sorting. 
For example, the default `\SORT_NUMERIC` would produce no change in the example below.

- **SORT_NUMERIC:** For comparing numbers
- **SORT_STRING:** For comparing string values
- **SORT_NATURAL:** For sorting string containing numbers and you’d like those numbers to be    
order in a natural way. For example: showing “10” after “2”.
- **SORT\_LOCALE\_STRING:** For comparing strings based on the current locale.

*NOTE:* `sort()` builds on the 
[Cake Collection::sortBy()](https://book.cakephp.org/3/en/core-libraries/collections.html#Cake\Collection\Collection::sortBy) 
method. For more details review that documentation

```php
debug($memberLayer->sort('last_name', \SORT_ASC, \SORT_STRING));

[
	(int) 0 => object(App\Model\Entity\Member) {

		'id' => (int) 74,
		'first_name' => 'Bay Area Book Artists',
		'last_name' => 'Bay Area Book Artists',
	
	},
	(int) 1 => object(App\Model\Entity\Member) {

		'id' => (int) 73,
		'first_name' => 'Sheila',
		'last_name' => 'Botein',
	
	},
	(int) 2 => object(App\Model\Entity\Member) {

		'id' => (int) 75,
		'first_name' => 'Leonardo',
		'last_name' => 'DiVinci',
	
	}
]
```

`sort()` is a terminal method and can't be chained. It only exists on the **Layer** object. 
Details of its in combination with other **Layer** and **LayerAccessArgs** features are 
discussed in the **Advanced User** section **[Sorting Filtered Results and Getting Sorted Value Lists]
(#sorting-filtered-results-and-getting-sorted-value-lists)**.

##Advanced Use

The simple access methods described above are built on a more comprehensive set of 
filtering and access tools. They provide simple calls for the most common uses. 

But even the advanced tools have limits. If you want to filter or manipulate 
the data beyond these provided features, `load()` the array and have at it.

###Basic Concepts

Gettin data from a Layer is a two step process

- specify what data you want
- request a return structure

These two steps can be done through a fluent interface or through a parameter system.

```php
//fluent interface example
$layerObject
    ->find()                                    //initiates a fluent expression
    ->specifyFilter('member_type', 'Person')    //multiple calls to define the  data
    ->load();                                    //one of the 4 load variations 

//paramter system example
$args = new App\Model\Lib\LayerAccessArgs();    //make the arguement object
$args->specifyFilter('member_type', 'Person');  //multple calls to define the  data
$layerObject->load($args);                      //one of the 4 load variations
```

The parameter system lets you design reusable access patterns. 

The fluent system may be easier to read in most cases.

####Important concepts
The `find()` method on the `Layer` object returns a `LayerAccessArgs` object. 
So these two lines create the same basic object:

```php
$argObj = $layer->find();
$argObj = new App\Model\Lib\LayerAccessArgs();
```
So anything that can be done in one style (including call-chaining) can be done in the other... until 
the final step of retrieving the result.

The difference is, the first syntax places a copy of the **Layer** inside the `$argObj`. The second syntax 
creates an `$argObj` that has no knowledge of any **Layer** object.

#####Why This distiction matters
Both **LayerAccessArgs** and **Layer** have the four `load()` variations to retrieve the results (see below). 
Those `load()` methods in **LayerAccessArgs** do not accept any paramters. The methods in **Layer** optionally 
alow a parameter (a LayerAccessArgs instance). The parameter is required for everything beyond simply 
retrieving the unaltered, stored array.

This means that, when using the first syntax, the **LayerAccessArgs** has `Layer::load()` composed it. It calls 
this method and passes a reference to itself as an argument.

When the second syntax is used, you must pass your constructed argument object to your 
layer object manually.

As a final, unnecessary note:

```php
//These two calls will make $argObj instances with the exact same properties.
$argObj = $layer->find();
$argObj = new App\Model\Lib\LayerAccessArgs($layer);

//You could now call load() on either

//If you make
$argObj = new App\Model\Lib\LayerAccessArgs();

//then try
$argObj->load();

//You would get an error because the true and final Layer::load() would be unavailable

//These two approaches are also equivalent

//Approach 1
$layerObj = layer($entityArray);
$argObj = new LayerAccessArgs();
$result = $layerObj->load($argObj);

//Approach 2
$layerObj = layer($entityArray);
$result = $layerObj
    ->find()
    ->load();
```

####Variations on load()

There are two commands to return an array of entities from a Layer and two commands 
to retrun an array of values extracted from the entities.

**Get an array of entities**

- `load()`
   - an array of the specified entities
- `loadPage()`
   - paginate the results and return a page
   
**Get an array of values extracted from the entities**

- `loadValueList()`
   - the requested values indexed 0...n   
   the values may be a property or method return from the entity.    
   If a method return, the method must not require arguments
- `loadKeyValueList()`
   - the requested values indexed by the specified key   
   both derived from the same entity and both may be any value   
   described for `loadValueList()`
   
Topics: Studio Manager, Layer, 

###Filtering

You can filter the contained entities by testing one value. If you need to test multiple values 
you'll need write code for the purpose. Or you might convert each result to a new layer and 
then filter that result.

####Using specifyFilter($value_source, $filter_value, $filter_operator = FALSE)

Using `specifyFilter()` is the simplest case for filtering data.

- name the property to test
- provide the value to test against
- provide the operation to use in the test
   - if `$filter_value` is a scalar (string, int, real, boolean) and no   
   `$filter_operator` is specified, the comparison **==** will be used.
   - if `$filter_value` is an array and no `$filter_operator` is specified,   
   the test **in_array** will be used.
   
The supported comparisons are: `==`, `!=`, `===`, `!==`, `<`, `>`, `<=`, `>=`, `in_array`, `true`, `false`, `truthy`

*NOTE: `true`, `false`, and `truthy` ignore the `$filter_value`. `true` and `false` perform `property === true|false`. 
`truthy` casts the property as a boolean treats the result as the test's outcome.

#####Filtering with the fluent syntax

In this case, the operation defaults to `==`  ('Member->member_type' == 'Person')

```php
debug(
    $memberLayer
        ->find()
        ->specifyFilter('member_type', 'Person')
        ->load()
);

	(int) 75 => object(App\Model\Entity\Member) {

		'id' => (int) 75,
		'first_name' => 'Leonardo',
		'last_name' => 'DiVinci',
		'member_type' => 'Person',
	
	},
	(int) 73 => object(App\Model\Entity\Member) {

		'id' => (int) 73,
		'first_name' => 'Sheila',
		'last_name' => 'Botein',
		'member_type' => 'Person',
	
	}
]
```

In this case the operation is specified ('Member->member_type' != 'Person')

```php
debug(
    $memberLayer
        ->find()
        ->specifyFilter('member_type', 'Person', '!=')
        ->load()
);

[
	(int) 74 => object(App\Model\Entity\Member) {

		'id' => (int) 74,
		'first_name' => 'Bay Area Book Artists',
		'last_name' => 'Bay Area Book Artists',
		'member_type' => 'Category',
	
	}
]
```

In this case the operation defaults to in\_array (in\_array('Member->member_type', $arrayOfValues)

```php
$arrayOfValues = ['Leonardo', 'Bay Area Book Artists'];
debug(
    $memberLayer
        ->find()
        ->specifyFilter('first_name', $arrayOfValues)
        ->load()
);

[
	(int) 75 => object(App\Model\Entity\Member) {

		'id' => (int) 75,
		'first_name' => 'Leonardo',
		'last_name' => 'DiVinci',
	
	},
	(int) 74 => object(App\Model\Entity\Member) {

		'id' => (int) 74,
		'first_name' => 'Bay Area Book Artists',
		'last_name' => 'Bay Area Book Artists',
	
	}
]
```
And don't forget. You can test the value of a method too (as long as it doesn't require arguments)

```php
debug(
    $memberLayer
        ->find()
        ->specifyFilter('name', 'Leonardo Divinci')
        ->load()
);

[
	(int) 75 => object(App\Model\Entity\Member) {

		'id' => (int) 75,
		'first_name' => 'Leonardo',
		'last_name' => 'DiVinci',
	
	}
]

```
#####Filtering by manually creating a LayerArgObj

```php
$findByType = new \App\Model\Lib\LayerAccessArgs();

$findByType
    ->setFilterOperator('===')      //if not specified, the default '==' would be used
    ->setFilterTestSubject('member_type');
//  ->filterValue('aString')        this would set the last required parameter

//use the LayerAccessArgs object as a parameter for Layer::load()
//remember, the 'set' calls to LayerAccessArgs return the instance
foreach (['Person', 'Category', 'Unknown'] as $type) {
    debug($memberLayer->load($findByType->filterValue($type)));
}

//first pass, $type = Person

[
	(int) 75 => object(App\Model\Entity\Member) {

		'id' => (int) 75,
		'first_name' => 'Leonardo',
		'last_name' => 'DiVinci',
		'member_type' => 'Person',
	
	},
	(int) 73 => object(App\Model\Entity\Member) {

		'id' => (int) 73,
		'first_name' => 'Sheila',
		'last_name' => 'Botein',
		'member_type' => 'Person',
	
	}
]

//second pass, $type = Category

[
	(int) 74 => object(App\Model\Entity\Member) {

		'id' => (int) 74,
		'first_name' => 'Bay Area Book Artists',
		'last_name' => 'Bay Area Book Artists',
		'member_type' => 'Category',
	
	}
]

//third pass, $type = Unknown

[]
```

###Sorting Filtered Results and Getting Sorted Value Lists

##Developer notes

Sort cannot chain. It directly returns an array. Though that can be re-layerized. 
It is only a method on layer. Adding it to the trait may be needed? or to LAAs?

Sort could probably be implemented like the LLA::specifyFileter() method. I don't 
think there would be a problem either order we ran filter and sorting. But it seems 
like running sorts second would always ensure the smallest work set.

For real efficiency, filtering the pieces directly into a heap might be possible.

For Sort to work with any of the value list return tools, it will have to 
work in the fluent interface.

Layer::keyedList is not used. It is forced to exist by the Interface. Do the other 
object use it or is Layer::keyValueList the future norm?

