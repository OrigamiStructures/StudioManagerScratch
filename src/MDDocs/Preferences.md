##Classes involved in the User Preference system

![A class diagram showing the classes that collaborate in the User Preference system showing their public interfaces and some major properties](/img/images/image/708e8e4b-a00e-42b0-a5a0-a74b7921b08b/classes-in-preference-sys.png "Classes in the Preferences system and their basic associations")

##Simple Use

>Extending the set of named prefs and preventing typos when accessing preference will be discussed later. Just roll with these simple examples for now.

Loading the Component automatically makes the Helper available on the View.

```php
class ConcreteController extends AppController {

    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Preferences');
    }

}
```

###Getting User Preferences

Once you've loaded the Preferences component, you gain access to an Entity that will answer all questions reguarding a users settings.

```php
//In your controller with the component installed as Preferences
$UserPref = $this->Preferences->getUserPrefsEntity($supervisor_id);

//to get any preference setting
$value = $UserPref->for('name.of.pref');
```

If the user has set a value it will be returned. If they have not made a choice for this pref, or have not made any prefs choice, you'll get the default value for that pref.

This is all you need to make any portion of the system respond to user preference settings.

```php
//in any class

//conditional
if ($UserPref->for('prefs.boolean.setting')) {
    //act base on setting
} else {
    //act based on alternate setting
}

//direct use
$Model->find('all')
    ->where(['column' => $UserPref->for('favorite.value')]);
```

###Allowing Users to set their prefs

Make preference choices available in forms on your pages. The Component will provide the context object required by the Form helper.

```php
//in a controller with the component at Preferences
$this->set($prefsContext, $this->Preferences->getFormContextObject($supervisor_id));

//in a .ctp
echo $this->Form->create($prefsContext);
    //echo inputs here
echo $this->Form->end();
```

The context object will carry all the users preference choices into the form inputs. Any values they have not specified will show the default values.

The context object will also carry the user_id that was used to identify the prefs and this must be included in your form and returned with the POSTed data.

The data should be returned to the PreferencesComponent::setPrefs(). This method will return the entity with any errors.

>Since the Form helper gets errors through its integration with an entity (which carries and save errors), how do we get the same functionality from a form object? Does the error registration process happen on the Form object before the save?

>PreferencesComponent::setPrefs() only does the save procedure after Form::validate() so possibly setPrefs() should not return the entity, but should return the Form object?

####Making forms for prefs

There isn't a final plan for the PreferencesHelper yet. Possibly, we can use fetch blocks and individual helper widgits to assemble and deliver a complete form to the page.

It's an open question how much we can pre-write and how much would need to be customized and how much written specifically for the application. Possibly we'll need to do a parent/subclass setup like the Form class.

###Avoiding typos when accessing prefs values

Following the shcema definition procedures outlined below will make all your preference names available as constants on a single class.

```php
$UserPrefs->for(PrefCon::PAGINATION_LIMIT);
```

The PrefCon class that carries all the constants should be made in the same file as the LocalPreferencesForm class that defines the schema

##Defining the schema and constants

![A class diagram detaing the classes in the Form heirarchy](/img/images/image/b5a11a6a-6ad8-4d5e-9342-2adb81622b67/preference_form_heirarchy.png "Focusing on the classes in the Form heriarchy")

The Preference system is build on CakePHP Modelless Forms. **PreferencesForm** is a direct descendent of **Form** and implements all the baseline functionality for the Preference forms.

The subclass **LocalPreferencesForm** is where the preference schema is defined for the app. In the file *LocalPreferencesForm.php* is a second class, **PrefCon** where the constants are defined.

The schema and constants should be defined in tandem. Each schema column should be targeted by a constant.

###The Structure and Naming preferences

User preferences are stored as a json object in a single field in the `preferences` table. In the code, the data is available as an array of arbitrary depth and structure. This is the array you'll design in your schema.

####dot Notation, arrays and constants

Define your fields in the schema and the constants using dot notation.

The schema is designed in a array property and the `_buildSchema()` method will pass this array to `Scehma::addFields($array)` to populate the schema object.

This two step process allows the Form object to use the property as a source of keys (`PrefForm::getValidPaths()`) for various tasks while still allowing modifications to the schema for any purpose that might arrise.

```php
class LocalPreferencesForm extends \App\Form\PreferencesForm
{
    //in LocalPreferencesForm
    public $prefsSchema = [
      'paginate.sort.people',
        [
          'type' => 'string',
          'default' => 'last_name'
        ]
    ];

    protected function _buildSchema($schema) {
       return $schema->addFields($this->prefsSchema);
    }
}
```

The schema definition will describe a json object that will be stored in the `preferences` table's `pref` column.

In the entity, you'll see this structure as an array

```php
//will create the array entry in Preference entity
[
    'paginate' =>
        ['sort' =>
            ['people' => 'last_name']
        ]
];
```
To prevent typos, it ease future structural changes, define constants in PrefCon for the paths to individual preference values.

I've also defined `value` and `key/value` arrays that can be used in validiation rules, to make select controls in forms, and for whatever else might come up.
```php
//in PrefCon, the constant to insure accurate
const PAGINATE_SORT_PEOPLE = 'paginate.sort.people';

/*
 * PrefCon also gives you a place to define arrays that support
 * validations, Form helper calls and more
 */
static public  $lists = [
    PrefCon::PAGINATION_SORT_PEOPLE => [
        'values' => ['first_name', 'last_name'],
        'select' => [
            'first_name' => 'First Name',
            'last_name' => 'Last Name'
    ]
];

//in code you can use your constants like this
PrefsEntity->for(PrefCon::PAGINATE_SORT_PEOPLE);

//LocalPreferenceForm has methods to help retirieve the arrays
$values = $prefForm->values(PrefCon::PAGINATION_SORT_PEOPLE);
$options = $prefForm->selectList(PrefCon::PAGINATION_SORT_PEOPLE);
```

You can design any schema you want and name the constants in any way that makes sense. The fact that the constants are defined with `const` inside the class, keeps them out of the global space so there will be no name conflicts with other constants. You will only be able to access the constants with the syntax **PrefCon::CONSTANT_NAME**.

