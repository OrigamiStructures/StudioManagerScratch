##Classes involved in the User Preference system

- PreferencesComponent
- PreferencesHelper
- PreferencesTable
- Preference (entity)

Loading the Component automatically makes the Helper available on the View.

###Preferences Schema

The only two fields of interest are

- user_id
  search on this field with **ContextUser::getId('supervisor')**
- prefs
  this is where any user prefecences will be stored.

There is no requirement that a supervisor have any preferces set, or even a prefence record. In fact, users will not have a record until they elect to set a preference. Once they have a record, all thier selected preferences will be stored in that one record in the prefs field.

The prefs field is a json type column. So, when you have the Prefs entity, you'll be able to access the stored data through array operations.

Each entity also includes a `defaults` property. It contains all possible preferences and a defalt value for each.

Since the Preferences entity contains all defalut settings and any user settings, our retrieval method simply check the user set and returns it if available and if not, returns the default value.

The Hash utility class is used to retrieve values. So you simply need to have the string-path to the stored value.

Example of using Hash

```php
$data = [
   'key' => 'value',
   'limit' => 5,
   'Shiping' => [
      'address' => 39,
      'method' => 'UPS'
   ]
];

Hash::get('key', $data);
//returns 'value'

Hash::get('Shipping.address', $data);
//returns 39
```

So, to get or set any preference value, we just have to know its path on the array.

To prevent typos, constants will be made on the entity for every path.

```php
class Preference extends Entity
{
    /**
     * Default values for preferences
     *
     * @var array
     */
    private $defaults = [
        'perPage' => 10,
        'Shipping' => [
             'address' => '44',
             'method' => 'USPS'
        ]
    ];

    const PER_PAGE = 'perPage';
    const SHIPPING_ADDRESS = 'Shipping.address';
    const SHIPPING_METHOD = 'Shipping.method';

    public function for($path)
    {
        $setting =
           Hash::get($this->prefs, $path)
           ?? Hash::get($this->defaults, $path);
        return $setting;
    }

}
```

With the constants defined inside the Entity class, they're kept out of the global scope. To retrieve preference values you will use calls like this:

```php
$prefs->for($prefs::PER_PAGE);
$prefs->for($prefs::SHIPPING_ADDRESS);
$prefs->for($prefs::SHIPPING_METHOD);

```
