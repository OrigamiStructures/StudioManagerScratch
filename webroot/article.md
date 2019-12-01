This describes use of the **Layer** class specifically and the **LayerAccessSystem** (**LAS**) 
more generally. **LAS** provides a unified set of data access tools for classes that implement 
the *LayerAccessSturctureInterface*; Layers, StackEntity, and StackSet. For more detail about 
Layers and the data aggregates that contain them see:

- [Interacting with Layer Objects](/article/interacting-with-layer-objects "Interacting with Layer Objects")
- [Interacting with StackEntities](/article/interacting-with-stackentities "Interacting with StackEntities")
- [Interacting with StackSet Objects.](/article/interacting-with-stackset-objects "Interacting with StackSet Objects.")

![Class diagram showing the three layer access structures—Layer, StackEntity, and StackSet—and the way they each former class is contained by the later class.](/OStructures/img/images/image/df494027-529f-4d40-968e-ae2b249adfb5/layer-struct.png "LayerAccessStructures")

The basic organizing principles:

- StackSets contain sets of StackEntity objects stored in an ID indexed array of same-type entities
- A StackEntity contains mutiple Layers stored as properties of the entity, one layer per property
- Layers contain an ID indexed array of same-type entities.

##Starting Simple: What are Layers
 Layers are wrapper objects that simplify the use of arrays of entities.

**Content Rules**

- The entities in the array must all be the same type
- The `id` column must be included and must be named `id`

Once an array is wrapped in a Layer you will have tools to filter and retrieve the contained data. 
Some methods are available directly on the Layer to do basic object-structure introspection. 
There are also tools to access and inspect the stored array data. These tools are available through 
the *LayerElementAccessTrait*. **StackSet** also uses the trait since it holds a similar 
array of data. Finally, there is a set of filtering, sorting, and pagination tools 
available through the collaboration of the **LayerAccessProcessor** and **LayerAccessArgs** objects.

**Layer**, **StackEntity**, and **StackSet** all implement the `LayerAccessStructureInterface` which 
gives them access to these advanced tools.

##Making a Layer

**StackEntity** objects are automatically populated with **Layer**s of appropriate data. But 
**Layer**s are useful whenever you have an homogenous array of entities.

Query the db to get an array of entities

```php
$members = $this->Members->find('all')
    ->select(['id', 'first_name', 'last_name', 'user_id', 'member_type')
    ->order(['id' => 'DESC'])
    ->limit(3);
```

If we unpack and output the result we'll see the typical query result array. 
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

##Basic Data Access and Introspection

Layers use the `LayerElementAccessTrait` and so, have several useful data 
access tools available directly.

Classes that use this trait need to implement the abastract method 
`LayerElementAccessTrait::getData()` so that it delivers an array of homogenous entities 
indexed by their ids; a trivial matter for Layer, which contains an array just like this.

Classes that use this trait also need to implement the trait's abstract `IDs($layer = null)`.. 

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

###hasId($id)

Returns a boolean indicating if the Layer contains an entity with the id value.

```php
 debug($memberLayer->hasId(33));

false

 debug($memberLayer->hasId(75));

true
```

###getData()

Retrieve the array of stored data

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
The `$layer` argument is ignored in **Layers**. Its use is described in the documetation of 
the **LAS**'s use with **StackEntities** and **StackSets**.

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

##Returning Restructured Data

**Layer** implements the ***LayerAccessInterface*** which alows it to return its storeed data in 
a variety of useful ways.

![A class diagram showing that Layer, LayerAccessArgs, and LayerAccessProcessor all implement the LayerAccessInterface](/OStructures/img/images/image/34f7201f-b339-42be-9829-82d19ebd54d5/layer-access-interface.png "Classes that implement the LayerAccessInterface")

You can see that two other classes implement this interfaces. More details on that later, but 
you should note, these return-data structures are generally available in the Layer Access system. 
In fact, other than the Trait features discussed earlier, they are *THE* ways to retrieve 
your data from the LA system.

- LayerAccessInterface - Any one of these can be used to deliver a final product after 
    performing the desired processing (discussed later). Or you can use them on unprocessed 
    data to get the full set as discussed in this section.
   - `toArray()`
   - `toLayer()`
   - `toValueList()`
   - `toKeyValueList()`
   - `toDistinctVaueList()`

###toArray()

```php

$members = $this->Members->find('all')
    ->select(['id', 'first_name', 'last_name', 'user_id', 'member_type')
    ->order(['id' => 'DESC'])
    ->limit(5);

$memberLayer = new Layer($members);

$memberLayer->toArray();

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

###toLayer()

This method will convert the result array to a new layer.

There is no processing done in this case so the example is a bit circular.

This return type is handy if you have to do additional processing on the data. More on that later.

```php

$memberLayer->toLayer()

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

###toValueList($valueSource)

`$valueSource` may point to a property or method the accepts no arguments. For example, 
Member entitiy has a method `name()` which concatenates a full name.

```php
$memberLayer->toValueList('name');

//produces

[
	(int) 0 => 'Leonardo DiVinci',
	(int) 1 => 'Bay Area Book Artists',
	(int) 2 => 'Sheila Botein',
	(int) 3 => 'Carla Bohnett',
	(int) 4 => 'Irene Jordahl'
]
```

###toKeyValueList($keySource, $valueSource)

Both `keySource` and `valueSource` may point to a property or method that accepts no arguments.

```php
$memberLayer->toKeyValueList('id', 'name');

//produces

[
	(int) 75 => 'Leonardo DiVinci',
	(int) 74 => 'Bay Area Book Artists',
	(int) 73 => 'Sheila Botein',
	(int) 72 => 'Carla Bohnett',
	(int) 71 => 'Irene Jordahl'
]
```

###toDistinctValueList($valueSource)

Retuns an array of unique values.

`$valueSource` may point to a property or method the accepts no arguments.

```php
$memberLayer->toDistinctValueList('member_type');

//produces 

[
	(int) 0 => 'Person',
	(int) 1 => 'Category'
]
```

##Advanced Features: Filter, Sort, Paginate

![Class diagram showing the two classes that collaborate to provide advanced features and detailing their implementation of processing and retrieval interfaces](/OStructures/img/images/image/3940eb86-fe17-4450-b144-7782a472edff/layer-access-adv.png "Layer access system classes that provide advance data retrieval features")

The advanced data retrieval features are defined by *LayerTaskInterface* and provide tools 
to filter, sort and paginate your data. The data will be returned as defined by the 
*LayerAccessInterface* described above.

###Where are these advanced tools

Classes that implement `AccessLayerStructureInterface` (in our case **Layer**, **StackEntity**, and 
**StackSet**) will have `getLayer($layer)` which will deliver a **LayerAccessProcessor** 
instance (**LAP**).

**LayerAccessProcessor** has a `find()` method which delivers a **LayerAccessArgs** 
object (**LAA**).

Finally, `AccessLayerStructureInterface::getArgObj()` will return a **LayerAccessArgs** 
instance so you can break the whole process down. This technique is described 
[here](#advanced-features-manual-style-processing).

Use **LAA** to define all the details of the processing you want done, **LAP** will do 
the actual data manipulation according to these instructions. Finally, use one of the 
`LayerAccessInterface` methods to structure and retrieve our data.

These processes can be written using a *fluent* interface style or assembled manually. 
The examples will all show the *fluent* style. A general discussion of 
[manual style processing](#advanced-features-manual-style-processing) is a the end of this article.

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

##Associated Data

The Stack system is designed simplify access to linked data. So a common need is to 
locate the layer data that belongs to some other entity. Given the owning entities name 
($foreignKey) and id ($foreignId) we can easily find the linked records.

This is a good example of what use higher level structures can make of the **LA** system 
as they create their own interfaces. Internally, this method creates a *LAA:filter()* 
based on provided data and CakePHP conventions.

###linkedTo($foreignKey, $foreignId)

`linkedTo()` provides a quick way to locate certain kinds of associated data. 

This method assume the foreign key property in the entity will follow Cake 
conventions (*name*\_id). The value you provide should be the *name* portion 
of the key.

This method returns a **LayerAccessArgs**. You can chain any of the 
`LayerAccessInterface::toXxxxx()` return methods to it. You can chain other **LAA** 
methods too, but the `filter` is already in use for this method. Overwriting that 
will produce unexpected results.

```php
$memberLayer
    ->linkedTo('user', '708cfc57-1162-4c5b-9092-42c25da131a9')
    ->toArray();

[
	(int) 75 => object(App\Model\Entity\Member) {

		'id' => (int) 75,
		'first_name' => 'Leonardo',
		'last_name' => 'DiVinci',
		'user_id' => '708cfc57-1162-4c5b-9092-42c25da131a9',
		'member_type' => 'Person',
	}
]

//another valid example
$memberLayer
    ->linkedTo('user', '708cfc57-1162-4c5b-9092-42c25da131a9')
    ->specifySort('created', SORT_DESC)
    ->speicifyPagination($page, $limit)
    ->toKeyValueList('id', 'name');
```

##Advance Features: Manual Style Processing

Take another look at the template *fluent* statement. 
 
```php
 $result = $someValidStructure       //any implementor of LayerStructureInterface
     ->getLayer($ofInterest)         //returns a LayerAccessProcessor object
     ->find()                        //returns a LayerAccessArgs object
     ->specifyFilter($a, $b, $c)     //modifies and returns the LayerAccessArgs instance
     ->specifySort($d, $e, $f)       //modifies and returns the LayerAccessArgs instance
     ->specifyPagination($g, $h)     //modifies and returns the LayerAccessArgs instance
     ->toArray();                    //returns the result data in the requested form
```

Notice that both the **LAP** and **LAA** instances are transient. You begin with an 
object that implements *LSI* and end up with that object and some result.

But there may be times that you want to reuse an **LAA** setup. Or you may want to 
get many result sets from a single **LAP** without performing the full fluent call 
each time.

###Understanding LAP/LAA Collaboration

To make best use of manual processes, you'll need to understand how the two object 
interact. 

Internally, the **LAP** has three important properties:

- `AppendIterator`
   - holds all the data to operate on
- `AccessArgs`
   - holds the **LAA**. May be empty
- `ResultIterator`
   - holds the processed prior to formatting for output (`LayerAccessInterface::toXxxxxx()`)
  
Besides the access speicifications, **LAA** holds one important property:

- `data`
   - an **LAP** instance. May be empty

We'll use the exaple fluent code to explain how these objects interact.

```php
 $result = $someValidStructure       //any implementor of LayerStructureInterface
     ->getLayer($ofInterest)         //returns a LayerAccessProcessor object
```

This produces an **LAP** with `AppendIterator` populated. An **LAP** without this property 
set will throw an exception during processing.

```php
     ->find()                        //returns a LayerAccessArgs object
```

This will return a new **LAA** with its `data` property set to the **LAP** which 
made the `find()` call. Calling any of the `LAI::toXxxxxx()` methods on an **LAA** 
that doesn't have this property set will throw an exception.

```php
     ->specifyFilter($a, $b, $c)     //modifies and returns the LayerAccessArgs instance
     ->specifySort($d, $e, $f)       //modifies and returns the LayerAccessArgs instance
     ->specifyPagination($g, $h)     //modifies and returns the LayerAccessArgs instance
```

With an **LAA** in hand, you can make any of its `set` or `specify` calls in any order.

```php
     ->toArray();                    //returns the result data in the requested form
```

There is a lot that happens in this call. First, it's important to note, in this case, 
the call is made on an **LAA** object that contains an **LAP** instance.
 
The **LAA** delegates the call to its contained **LAP** and passes itself as a parameter.

In the `fluent` pattern, this is the moment when **LAP** finally gets the instructions to 
direct processing. **LAP** passes this recieved **LAA** instance to its `perform($argObj` 
method. This method runs any requred processing and stores the result in `ResultIterator`.

**LAP** then structures `ResultIterator` according to the `toXxxxx()` method called and 
returns that data.

This suggests many places where the process can be broken down.

###The Tools For Manual Processes

There are a methods to help you to decompose the process.

- `LayerStructureInterface::getLayer($layer = null)`
   - Yeilds an **LAP** that contains the specified $layer data. This is the data that   
   will be processed to produce the result. The **LAP** will not contain an **LAA** 
   at this point.
- `LayerTaskProcessor::insert($data)`
   - add a layer, array of entities, or loose entity to the data set
- `LayerStructureInterface::getArgObj()`
   - get a reference to the **LAA**
- `LayerTaskProcessor::cloneArgObj()`
   - Returns a clone of the current **LAA** stripped of its **LAP**. If no **LAA** 
   exists, a new empty instance will be returned.
- `LayerTaskProcessor::setArgObj($argObj)`
   - This clones the provided $argObj and stores the clone. 
- `LayerTaskProcessor::perform($argObj)`
   - Set an new **LAA** and process the **LAP** and sets the result to `ResultIterator`.

###Getting and Populating a LayerAccessProcessor

Making and capturing a populated **LAP**

```php

$layerAccessProcessor = $validStructure->getLayer($someLayer);

debug($layerAccessProcessor);

object(App\Model\Lib\LayerAccessProcessor) {
'[AppendIterator]' => 'Contains 75 items.',
	'[AccessArgs]' => 'null',
	'[layerName]' => 'member',
	'[ResultArray]' => 'null'
}
```

Making an **LAP** and populating it by hand

```php
//Data to place in the LayerAccessProcessor
$layer = layer([
    new \App\Model\Entity\Member(['id' => 1, 'first_name' => 'one']),
    new \App\Model\Entity\Member(['id' => 2, 'first_name' => 'two']),
]);
$array =[
    new \App\Model\Entity\Member(['id' => 3, 'first_name' => 'three']),
    new \App\Model\Entity\Member(['id' => 4, 'first_name' => 'four']),
];
$entity = new \App\Model\Entity\Member(['id' => 5, 'first_name' => 'five']);

/*
 * fully manual populating
 *
 * All the entities inserted must be of the same type
 * and they must all match the type passed to the constructor
 *
 * @param $entityType string lower case singular version of the Entity class
 */
$lap = new \App\Model\Lib\LayerAccessProcessor('member');
$lap->insert($layer)        //you can chain the insert calls
    ->insert($array)
    ->insert($entity);

debug($lap);

object(App\Model\Lib\LayerAccessProcessor) {
	'[AppendIterator]' => 'Contains 5 items.',
	'[AccessArgs]' => 'null',
	'[layerName]' => 'member',
	'[ResultArray]' => 'null'
}
```

###Getting a LayerAccessArgs

```php
/*
 * Get a new, empty LAA
 */
$argObj = $memberLayer->getArgObj();

debug($argObj);

object(App\Model\Lib\LayerAccessArgs) {

	'[data]' => 'not set',
	'[_registry]' => object(App\Model\Lib\ValueSourceRegistry) {

		'_loaded' => []
	
	},
	'_page' => false,
	'_limit' => false,
	'_layer' => false,
	'source_node' => [
		'value' => false,
		'key' => false,
		'filter' => false,
		'resultValue' => false,
		'resultKey' => false,
		'distinctValue' => false
	],
	'_filter_value' => false,
	'_filter_value_isset' => false,
	'_filter_operator' => false,
	'_sortDir' => false,
	'_sortType' => false,
	'_sortColumn' => false

}

/*
 * Get one from an LAP
 */
$lap = $memberLayer->getLayer();    //make a LayerTaksProcessor
$selectList = $lap                  
    ->find()                        //from this point we have a LayerAccessArgs
    ->toKeyValueList('id', 'name'); 

debug($lap);                        //we didn't save the LAA but it is now in the LAP

object(App\Model\Lib\LayerAccessProcessor) {

	'[AppendIterator]' => 'Contains 75 items.',
	'[AccessArgs]' => object(App\Model\Lib\LayerAccessArgs) {

		'[data]' => 'App\Model\Lib\LayerAccessProcessor containing 75items.',
		'[_registry]' => object(App\Model\Lib\ValueSourceRegistry) {

			'_loaded' => [
				(int) 0 => 'resultKey',
				(int) 1 => 'resultValue'
			]
		
		},
		'_page' => false,
		'_limit' => false,
		'_layer' => 'member',
		'source_node' => [
			'value' => false,
			'key' => false,
			'filter' => false,
			'resultValue' => 'name',
			'resultKey' => 'id',
			'distinctValue' => false
		],
		'_filter_value' => false,
		'_filter_value_isset' => false,
		'_filter_operator' => false,
		'_sortDir' => false,
		'_sortType' => false,
		'_sortColumn' => false
	
	},
	'[layerName]' => 'member',
	'[ResultArray]' => 'Contains 75 items.'

}
```
We can get a clone of the **LAA**. But notice, the **LAA::data** is unset in the clone

```php
$argObj = $lap->cloneArgObj();

debug($argObj);

/src/Template/Members/docs.ctp (line 24)

object(App\Model\Lib\LayerAccessArgs) {

	'[data]' => 'not set',
	'[_registry]' => object(App\Model\Lib\ValueSourceRegistry) {

		'_loaded' => [
			(int) 0 => 'resultKey',
			(int) 1 => 'resultValue'
		]
	
	},
	'_page' => false,
	'_limit' => false,
	'_layer' => 'member',
	'source_node' => [
		'value' => false,
		'key' => false,
		'filter' => false,
		'resultValue' => 'name',
		'resultKey' => 'id',
		'distinctValue' => false
	],
	'_filter_value' => false,
	'_filter_value_isset' => false,
	'_filter_operator' => false,
	'_sortDir' => false,
	'_sortType' => false,
	'_sortColumn' => false

}


```

###Using the Manual Objects

```php
$memberProcessor = $memberLayer->getLayer();
$groupFilters = [
    'People' => $memberLayer->getArgObj()     //you can chain off the accessor if you want
        ->specifyFilter('member_type', 'Person'),
    'Institutions' => $memberLayer->getArgObj()
        ->specifyFilter('member_type', 'Institution')
];

$result = [];
foreach($groupFilters as $type => $filter) {
    $filter->setPagination(1, 3);                   //you can keep modifying the LAA
    $memberProcessor->setArgObj($filter);           //and use it when you're ready
    $result[$type] = $memberProcessor->toArray();
}

debug($result);

[
	'People' => [
		(int) 0 => object(App\Model\Entity\Member) {
			'id' => (int) 1,
			'first_name' => 'Don',
			'last_name' => 'Drake',
			'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
			'member_type' => 'Person',
		},
		(int) 1 => object(App\Model\Entity\Member) {
			'id' => (int) 2,
			'first_name' => 'Gail',
			'last_name' => 'Drake',
			'member_type' => 'Person',
		},
		(int) 2 => object(App\Model\Entity\Member) {
			'id' => (int) 7,
			'first_name' => 'Art',
			'last_name' => 'Collecteur',
			'member_type' => 'Person',
		}
	],
	'Institutions' => [
		(int) 0 => object(App\Model\Entity\Member) {
			'id' => (int) 61,
			'first_name' => 'Center for Photo Arts',
			'last_name' => 'Center for Photo Arts',
			'member_type' => 'Institution',
		},
		(int) 1 => object(App\Model\Entity\Member) {

			'id' => (int) 66,
			'first_name' => 'joson photo llc',
			'last_name' => 'joson photo llc',
			'member_type' => 'Institution',
		}
	]
]
```
###Advanced Use Summary

This is not a comprehensive set of example. But it should give you a solid foundation 
if you need to venture beyond the *fluent* interface.

