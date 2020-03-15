##Overview of the Preferences System

Preferences is a component that will construct, manage and deliver a Prefs object. The Prefs object may be transported to any layer of your application to provide values for application processes.

Prefs will answer 'what is the value of' questions with the actor's chosen preference value, the default value for the preference, or a UnknownPreferenceKeyException (change to null?).

```php
try {
    if ($Prefs->for('stored_value') > $someLimit) {
        $this->performSomeAction();
    } else {
        $this->doNormalProcedure();
    }
} catch (UnknownPreferenceKeyException $e) {
    $this->handleUnknownPrefsKey();
}
```

The allowed preference keys are defined by the developer as the fields of a Schema object in a [Form](https://book.cakephp.org/4/en/core-libraries/form.html).

In addition to reporting preference settings or defaults, Prefs can also deliver an Entity and a Form. These objects will support CRUD processes or other developer needs.

```php
//in a controller
$this->loadComponent('Preferences');
$Prefs = $this->Preferences->getPrefs($user_id);
$userPreferences = $Prefs->getEntity();
$formHelperContext = $Prefs->getForm();
```

The preference values set by an actor are stored in the `preferences` table as json data on the `prefs` columnn (text type). All the fields defined in the schema will be included in this one json object.

A PreferencesTable and Preference entity are provided as part of the plungin to participate in the default storage processes of the plugin.

>**NOTE:** This hard-wired table/column requirement should be replaced with a dependency-injection hook. The devoloper may want a different table or different field. They may also require more than one Prefs schema; for example, a user schema and an application schema.
>
>In a multi-schema scenario I expect multiple PreferencesComponents would be created, each with a different alias and each would deliver its Prefs object.

##Developing the Preferences for an Application

There is no pre-defined set of preference keys. You must decided what data points you want to offer in your application. Once you've decided on a preference inflection point you need to:

- Define the field in the Form's schema (including its default value)
- Create some system the actor can use to set their prefered value for the field
- Make your application respond to the discovered value of the field

###An Example Implementation

**A sample layout ctp without preferences**

```html
<!-- Templates/Layouts/default.ctp -->
<!DOCTYPE html>
<html>
<head>
    <title>
        <?= $pageTitle ?>:
    </title>
    <?= $this->Html->css('default-color-theme'); ?>
</head>
<body>
<section class="container clearfix">
    <?= $this->fetch('content') ?>
</section>
</body>
</html>
```

If we want to allow the user to choose different css to control colors on the pages we can define a 'color-theme' preference.

First we define the schema to support this inflection point. The plugin includes the PreferencesForm object which you will extend to define your schema. You'll also want to define validation rules to control the values that get set in your table.

It will be necessary to define an `id` field in your schema so that the posted preferences can be linked to the persisted record.

>The `id` may get added to the plugin classes so it wouldn't have to be part of the implementation in each application.

```php
<?php
namespace App\Form;

use App\Form\PreferencesForm;
use Cake\Form\Schema;
use Cake\Validation\Validator;

class LocalPreferencesForm extends PreferencesForm
{
    /**
     * A standard Form hook method
     *
     * @param Schema $schema
     * @return Schema
     */
    protected function _buildSchema(Schema $schema)
    {
        $schema->addField('color-theme',  [
            'type' => 'string',
            'default' => 'default-color-theme'
        ]);
        $schema->addField('id', [
            'type' => 'string'
        ]);
       return parent::_buildSchema($schema);
    }

    /**
     * Define any validators your schema needs
     *
     * These run during ->patchEntity()
     *
     * @param Validator $validator
     * @return Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator->inList(
            'color-theme',
            ['default-color-theme', 'pastel-theme', 'neon-theme', 'druid-theme'],
            'Only certain color themes are available'
        );
        return parent::validationDefault($validator);
    }
}
```

Next you need to give your actor a way to regester their choice. In this case the actor is an interacting user, so we can present a form. Here is the controller/method that would back-stop the page that presents the form.

```php
namespace App\Controller;

use App\Controller\AppController;
use App\Controller\Component\PreferencesComponent;
use App\Model\Table\PreferencesTable;

class MyController extends AppController
{
    /**
     * Load the component when required
     */
    public function initialize(){
        $this->loadComponent('Preferences');
        parent::initialize();
    }

    public function someMethod() {
        //This is some method that also presents a form
        //that allows the user to change the color-theme setting
        $this->set(
            'theme_options',
            ['default-color-theme', 'pastel-theme', 'neon-theme', 'druid-theme']
        );
        //You'll need the Form to support the FormHelper.
        //It's on the Prefs object along with the Entity.
        //The component can deliver the object
        $this->set('Prefs', $this->Preferences->getPrefs($this->Auth->user('id')));
    }
}
```

The plugin provides a PreferencesController that has a `setPrefs()` method to handle post, patch, or put data from your forms.

>Other methods may be added later for different scenarios.

With the schema in place and with the provided controller ready to accept form data, we can make a form for the user.

```php
echo $this->Form->create($Prefs->getForm(), [
    'url' => ['controller' => 'preferences', 'action' => 'setPrefs']
]);
echo $this->Form->control('theme_options', ['options' => $theme_options]);
echo $this->Form->control('id', ['type' => 'hidden']);
echo $this->Form->submit();
echo $this->Form->end();
```

Posting this form to the provided controller/action will save the new settings and return to the refering page.

So, all that remains is to adjust your application to make use of the new preference inflection point. In this example, this will be in our default layout.

```html
<!-- Templates/Layouts/default.ctp -->
<!DOCTYPE html>
<html>
<head>
    <title>
        <?= $pageTitle ?>:
    </title>
    <?= $this->Html->css($Prefs->for('color_theme'); ?>
</head>
<body>
<section class="container clearfix">
    <?= $this->fetch('content') ?>
</section>
</body>
</html>
```

**Old Docs Below. Not Edited**

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
        $this->loadComponent('Preferences', $config);
        //support for config settings is pending
        //overrides for Form and Table are being considered
    }

}
```

###The Prefs Object

The component will deliver a **Prefs** object which contains a Preferences entity and a Preferences form. These two objects will support any Prefs tasks you have.

```php
//from a controller
$Prefs = $this->Preferences->getPrefs($owner_id);
//to get the Form object with the schema
$form = $Prefs->getForm();
//to get the Entity object with the owner's settings
$entity = $Prefs->getEntity();

//to get unowned, default-value objects
$Prefs = $this->Preferences->getPrefs();
```

The component stores Prefs instances in a registry indexed by the id that was used to create them. Subsiquent calls to `$this->Preferences->getPrefs($owner_id)` will return the object from the registry.

###The Preferences Entity

Once you've loaded the Preferences component, you gain access to an Entity that will answer all questions reguarding a users settings.

```php
//In your controller with the component installed as Preferences
$entity = $this->Preferences->getPrefs($supervisor_id)->getEntity();

//to get any preference setting
$value = $entity->for('name.of.pref');
```

If the user has set a value it will be returned. If they have not made a choice for this pref, or have not made any prefs choice, you'll get the default value for that pref.

This is all you need to make any portion of the system respond to user preference settings.

```php
//in any class

//conditional
if ($entity->for('prefs.boolean.setting')) {
    //act base on setting
} else {
    //act based on alternate setting
}

//direct use
$Model->find('all')
    ->where(['column' => $entity->for('favorite.value')]);
```

###Allowing Users to set their prefs

Make preference choices available in forms on your pages. The Component will provide the context object required by the Form helper.

```php
//in a controller with the component at Preferences
$this->set(
   $formContext,
   $this->Preferences->getPrefs($supervisor_id)->getForm()
);

//in a .ctp
echo $this->Form->create($formContext);
    //echo controls here
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


Topics: Studio Manager, Preference,
