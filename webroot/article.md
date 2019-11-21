This describes use of the LayerAccessSystem which provides a unified set of data 
access tools for Layers, StackEntities, and StackSets. For more detail about Layers 
and the data aggregates that contain them see:

- [Interacting with Layer Objects](/article/interacting-with-layer-objects "Interacting with Layer Objects")
- [Interacting with StackEntities](/article/interacting-with-stackentities "Interacting with StackEntities")
- [Interacting with StackSet Objects.](/article/interacting-with-stackset-objects "Interacting with StackSet Objects.")

![Class diagram showing the three layer access structures—Layer, StackEntity, and StackSet—and the way they each former class is contained by the later class.](/OStructures/img/images/image/df494027-529f-4d40-968e-ae2b249adfb5/layer-struct.png "LayerAccessStructures")

StackSets contain sets of StackEntities. StackEntities contain mutiple Layers. Layers contain arrays of Entities.

##Starting Simple: What are Layers
 Layers are wrapper objects that simplify the use of arrays of entities.

**Content Rules**

- The entities in the array must all be the same type
- The `id` column must be included and must be named `id`

Once an array is wrapped in a Layer you will have tools to filter and retrieve the contained data. 
Some of the methods are available directly on the LayerObject to do basic object introspecition. 
Then there are some simple data access tools available on the object through a Trait (Layer and 
StackSet use the Trait). Finally, there is a set of filtering, sorting, and pagination tools 
available through the collaboration of the **LayerAccessProcessor** and **LayerAccessArgs** objects.

Layer, StackEntity, and StackSet all implement the `LayerAccessStructureInterface` which 
gives them access to these advanced tools.

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

##Layer Class Introspection

Before we get into the data access tools, there are a couple of basic introspection tools 
available on Layers.

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

##Data Access Methods

Layers use the `LayerElementAccessTrait` and so, have several useful data 
access tools available directly.

Classes that use this trait need to implement the abastract method `getData()` 
so that it delivers an array of homogenous entities indexed by their ids; 
a trivial matter for Layer, which contains an array just like this.

Classes that use this trait also need to implement the abstract `IDs($layer = null)` method. 

![A class diagram showing Layer and StackSet using the trait but not StackEntity](/OStructures/img/images/image/9b00978f-854e-4d59-adf7-c22bf2e23f36/layer-struct-and-element-trait.png "The Layer structures and their use of LayerElementAccessTrait")

###count()

`Layer::count()` tells you how many entities are stored.

Layers also implement the \Countable interface so you can do `count($layer)`;

```php
echo $memberLayer->count(); //method on the layer

3

echo count($memberLayer); //syntax for a \Countable class

3
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

###IDs($layer = null)

`IDs()` returns an array containing the entity IDs

```php
debug($memberLayer->IDs());

 [
  0 => 75
  1 => 74
  2 => 73
]
```
The `$layer` argument is ignored in Layers. Its use is described in the documetation of 
the LayerAccessSystem's use with StackEntities and StackSets.

###hasId($id)

Returns a boolean indicating if the Layer contains an entity with the id value.

```php
 debug($memberLayer->hasId(33));

false

 debug($memberLayer->hasId(75));

true
```

###shift()

As was the case with `layerName()`, `shift()`'s use is primarily in more complex structures.

However, there may be times when you can be sure there is only one stored entitiy. 
Or you may specifically want the first (or a sample) entity. 

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
```

`shift()` does not alter the content of the Layer like its namesake, the php method 
`array_shift()`.

##Advanced features through two Interfaces

![Class diagram showing the two classes that collaborate to provide advanced features and detailing their implementation of processing and retrieval interfaces](/OStructures/img/images/image/3940eb86-fe17-4450-b144-7782a472edff/layer-access-adv.png "Layer access system classes that provide advance data retrieval features")

The advanced data retrieval features are defined in two Interfaces:

- LayerAccessInterface - defines the data structures that can be returned   
   Any one of these can be used to deliver a final product after performing the 
   desired processing. Or you can use them on unprocessed data to get the full set.
   - toArray
   - toLayer
   - toValueList
   - toKeyValueList
   - toDistinctVaueList

- LayerTaskInterface - defines the processing that can be done to limit or arrange the results   
  You can perform any or all of these processes, but only once each.
    - filter
    - sort
    - paginate

###Where are these advanced tools

The three structures—Layer, StackEntity, and StackSet—all implement `AccessLayerStructure`'s 
`getLayer($layer)` method. This method delivers a **LayerAccessProcessor** which implements 
both the advanced interfaces.

**LayerAccessProcessor** also has a `find()` method which delivers a **LayerAccessArgs** 
object which implements `LayerAccessInterface`. So, either of these classes can return 
processed, structured data.

####Why two separate classes?

The ***Args*** class oversees setting all the filter, sort, and pagination details. The 
***Processor*** is responsible for actually manipulating the data as requested through ***Args***.

###Getting Structured Data back

*NOTE: `getLayer()` optionally accepts one string parameter, `$layer`. This is needed when 
calling from the structures that contain more than one Layer; StackEntity and StackSet. 
It will be ignored on a Layer::getLayer() call.*

![Class diagram showing that the three classes Layer, StackEntity, and StackSet all implement the LayerStructureInterface which provides tools for filtering, sorting and pagination](/OStructures/img/images/image/5e47aa0a-c5b9-426d-b971-b7771a2e8630/layer-struct-and-access-interface.png "Layer Structures use of the LayerStructureInterface")

####toArray()

```php

$members = $this->Members->find('all')
    ->select(['id', 'first_name', 'last_name', 'user_id', 'member_type')
    ->order(['id' => 'DESC'])
    ->limit(5);

$memberLayer = new Layer($members);

$memberLayer->getLayer()->toArray();

//Will produce 

[
	(int) 0 => object(App\Model\Entity\Member) {
		'id' => (int) 75,
		'first_name' => 'Leonardo',
		'last_name' => 'DiVinci',
		'user_id' => '708cfc57-1162-4c5b-9092-42c25da131a9',
		'member_type' => 'Person',
	},
	(int) 1 => object(App\Model\Entity\Member) {
		'id' => (int) 74,
		'first_name' => 'Bay Area Book Artists',
		'last_name' => 'Bay Area Book Artists',
		'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
		'member_type' => 'Category',
	},
	(int) 2 => object(App\Model\Entity\Member) {
		'id' => (int) 73,
		'first_name' => 'Sheila',
		'last_name' => 'Botein',
		'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
		'member_type' => 'Person',
	},
	(int) 3 => object(App\Model\Entity\Member) {
		'id' => (int) 72,
		'first_name' => 'Carla',
		'last_name' => 'Bohnett',
		'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
		'member_type' => 'Person',
	},
	(int) 4 => object(App\Model\Entity\Member) {
		'id' => (int) 71,
		'first_name' => 'Irene',
		'last_name' => 'Jordahl',
		'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
		'member_type' => 'Person',
	}
]
```

####toLayer()

This method will convert the result array to a new layer.

There is no processing done in this case so the example is a bit circular.

This return type is handy if you have to do additional processing on the data.

```php

$memberLayer->getLayer()->toLayer()

//produces

object(App\Model\Lib\Layer) {
	[protected] _layer => 'member'
	[protected] _className => 'Member'
	[protected] _data => [
		(int) 75 => object(App\Model\Entity\Member) {
			'id' => (int) 75,
			'first_name' => 'Leonardo',
			'last_name' => 'DiVinci',
			'user_id' => '708cfc57-1162-4c5b-9092-42c25da131a9',
			'member_type' => 'Person',
		},
		(int) 74 => object(App\Model\Entity\Member) {
			'id' => (int) 74,
			'first_name' => 'Bay Area Book Artists',
			'last_name' => 'Bay Area Book Artists',
			'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
			'member_type' => 'Category',
		},
		(int) 73 => object(App\Model\Entity\Member) {
			'id' => (int) 73,
			'first_name' => 'Sheila',
			'last_name' => 'Botein',
			'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
			'member_type' => 'Person',
		},
		(int) 72 => object(App\Model\Entity\Member) {
			'id' => (int) 72,
			'first_name' => 'Carla',
			'last_name' => 'Bohnett',
			'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
			'member_type' => 'Person',
		},
		(int) 71 => object(App\Model\Entity\Member) {
			'id' => (int) 71,
			'first_name' => 'Irene',
			'last_name' => 'Jordahl',
			'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
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

####toValueList($valueSource)

`$valueSource` may point to a property or method the accepts no arguments. For example, 
Member entitiy has a method `name()` which concatenates a full name.

```php
$memberLayer->getLayer()->toValueList('name');

//produces

[
	(int) 0 => 'Leonardo DiVinci',
	(int) 1 => 'Bay Area Book Artists',
	(int) 2 => 'Sheila Botein',
	(int) 3 => 'Carla Bohnett',
	(int) 4 => 'Irene Jordahl'
]
```

####toKeyValueList($keySource, $valueSource)

Both `keySource` and `valueSource` may point to a property or method that accepts no arguments.

```php
$memberLayer->getLayer()->toKeyValueList('id', 'name');

//produces

[
	(int) 75 => 'Leonardo DiVinci',
	(int) 74 => 'Bay Area Book Artists',
	(int) 73 => 'Sheila Botein',
	(int) 72 => 'Carla Bohnett',
	(int) 71 => 'Irene Jordahl'
]
```

####toDistinctValueList($valueSource)

Retuns an array of unique values.

`$valueSource` may point to a property or method the accepts no arguments.

```php
$memberLayer->getLayer->toDistinctValueList('member_type');

//produces 

[
	(int) 0 => 'Person',
	(int) 1 => 'Category'
]
```

###Data Manipulation Options: filter, sort, paginate

With those return-data structuring tools in hand, we can look at the tools that effect 
*which* data gets returned and how it is sorted.

These processes can be written using a *fluent* interface style or assembled manually. 
The examples will all show the *fluent* style. A general discussion of 
[manual style processing](#manual-style-processing) is a the end of this article.

Your fluent operations will look like this:

```php
$someValidStructure                 //any implementor of LayerStructureInterface
    ->getLayer($ofInterest)         //returns a LayerAccessProcessor object
    ->find()                        //returns a LayerAccessArgs object
    ->specifyFilter($a, $b, $c)     //modifies and returns the LayerAccessArgs instance
    ->specifySort($d, $e, $f)       //modifies and returns the LayerAccessArgs instance
    ->specifyPagination($g, $h)     //modifies and returns the LayerAccessArgs instance
    ->toArray();                    //returns the result data in the requested form
```
You can call the **LayerAccessArgs** methods in any order. They will not be executed until 
one of the `toXxxxx()` methods is called. When executed they will always run in the same 
order; filter, sort, paginate.

####Optional hyper-detail
Any `toXxxxx()` call on **LayerAccessArgs** causes it to pass itself to the 
**LayerAccessProcessor** delegating both processing and return-data structuring to that class.

###specifyFilter($value_source, $filter_value, $filter_operator = FALSE)

You can filter the contained entities by testing one value. If you need to test multiple values 
you'll need write code for the purpose. Or you might convert each result to a new layer and 
then filter that result.

####Available comparison operations
The supported comparisons are: `==`, `!=`, `===`, `!==`, `<`, `>`, `<=`, `>=`, `in_array`, 
`!in_array`, `true`, `false`, `truthy`

- if `$filter_value` is a scalar (string, int, real, boolean) and no   
   `$filter_operator` is specified, the comparison **==** will be used.
- if `$filter_value` is an array and no `$filter_operator` is specified,   
   the test **in_array** will be used.
- `true`, `false`, and `truthy` ignore the `$filter_value`. `true` and `false` perform 
`property === true|false`. `truthy` casts the property as a boolean treats the result as 
the test's outcome.

In this case, the operation defaults to `==`  ('Member->member_type' == 'Person')

```php
debug(
    $memberLayer
        ->getLayer()
        ->find()
        ->specifyFilter('member_type', 'Person')
        ->toArray()
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
	},
	(int) 3 => object(App\Model\Entity\Member) {
		'id' => (int) 72,
		'first_name' => 'Carla',
		'last_name' => 'Bohnett',
		'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
		'member_type' => 'Person',
	},
	(int) 4 => object(App\Model\Entity\Member) {
		'id' => (int) 71,
		'first_name' => 'Irene',
		'last_name' => 'Jordahl',
		'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
		'member_type' => 'Person',
	}

]
```

In this case the operation is specified ('Member->member_type' != 'Person')

```php
debug(
    $memberLayer
        ->getLayer()
        ->find()
        ->specifyFilter('member_type', 'Person', '!=')
        ->toArray()
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
        ->getLayer()
        ->find()
        ->specifyFilter('first_name', $arrayOfValues)
        ->toArray()
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
and use any of the return-structuring methods

```php
debug(
    $memberLayer
        ->getLayer
        ->find()
        ->specifyFilter('name', 'Leonardo Divinci')
        ->toKeyValueList('id', 'last_name')
);

[
	(int) 75 => 'DiVinci',
]

```

There may be times that you need to set or modify just one parameter of a filter. 
There are individual calls available for each:

- `setFilterTestSubject($value_source)`
    - sets the property or method return to test
- `setFilterValue($param)`
    - sets the values to search for in *TestSubject*
- `setFilterOperator($param)`
    - one of the allowed [test types](#available-comparison-operations)

###sort($property, $dir = \SORT/\_DESC, $type = \SORT\_NUMERIC)

Don't neglect the `$type` parameter. Sorting with the wrong value here can prevent sorting. 
For example, the default `\SORT_NUMERIC` would produce no change in the example below.

- **`SORT_NUMERIC`:** For comparing numbers
- **`SORT_STRING`:** For comparing string values
- **`SORT_NATURAL`:** For sorting string containing numbers and you’d like those numbers to be    
order in a natural way. For example: showing “10” after “2”.
- **`SORT_LOCALE_STRING`:** For comparing strings based on the current locale.

*NOTE:* `sort()` builds on the 
[Cake Collection::sortBy()](https://book.cakephp.org/3/en/core-libraries/collections.html#Cake\Collection\Collection::sortBy) 
method. For more details review that documentation

```php
$memberLayer
    ->getLayer()
    ->find()
    ->specifySort('last_name', \SORT_ASC, \SORT_STRING)
    ->toValueList('last_name');

[
	(int) 0 => 'Bay Area Book Artists',
	(int) 1 => 'Bohnett',
	(int) 2 => 'Botein',
	(int) 3 => 'DiVinci',
	(int) 4 => 'Jordahl'
]
```
###specifyPagination($page, $limit);

```php
$members = $MembersTable->find('all')
    ->select(['id', 'first_name', 'last_name', 'user_id', 'member_type'])
    ->toArray();

$memberLayer = new Layer($members);

echo count($memberLayer);

(int) 75

$memberLayer
    ->getLayer()
    ->find()
    ->specifyPagination(2, 10)
    ->toValueList('name');

[
	(int) 0 => 'Don Drake',
	(int) 1 => 'Gail Drake',
	(int) 2 => 'Drake Family',
	(int) 3 => 'Wonderland Group',
	(int) 4 => 'Alice Goask',
	(int) 5 => 'SFMOMA',
	(int) 6 => 'Art Collecteur',
	(int) 7 => 'Kate Jordahl',
	(int) 8 => 'Rae Trujillo',
	(int) 9 => 'Vamp and Tramp'
]

$memberLayer
    ->getLayer()
    ->find()
    ->specifyFilter('member_type', 'Category', '!=')
    ->specifySort('last_name', SORT_ASC, SORT_STRING)
    ->specifyPagination(2, 10)
    ->toKeyValueList('name', 'member_type');

[
	'John Thacker' => 'Person',
	'Paula Tognarelli' => 'Person',
	'Rae Trujillo' => 'Person',
	'Sonia Underdown' => 'Person',
	'Vamp and Tramp' => 'Institution',
	'Wonderland Group' => 'Institution',
	'Rachel Wooster' => 'Person',
	'Nanette Wylde' => 'Person',
	'joson photo llc' => 'Institution',
	'photo-eye' => 'Institution'
]

```

###Manual Style Processing

#NOT EDITED PAST HERE

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

