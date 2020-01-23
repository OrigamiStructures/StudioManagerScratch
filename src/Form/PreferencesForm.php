<?php
namespace App\Form;

use App\Exception\BadPrefsImplementationException;
use App\Model\Entity\Preference;
use Cake\Controller\Component\FlashComponent;
use Cake\Form\Form;
use Cake\Form\Schema;
use Cake\ORM\TableRegistry;
use Cake\Utility\Text;
use Cake\Validation\Validator;
use Cake\Utility\Hash;
use Cake\View\Form\ContextInterface;

/**
 * PreferencesForm
 *
 * This class can go to a plugin for a portable preference system.
 * The user would extend these universal functions with thier own
 * schema, validation, and rules in their local directory and use
 * that class for all their work.
 *
 * @todo What components should be parameterized for a plugin. And
 *      given that there is no additional constructor inputs on the
 *      class how would we get more data in here? A decorator?
 *
 * @package App\Form
 */
class PreferencesForm extends Form
{

    /**
     * @var bool|string
     */
    protected $user_id = false;

    /**
     * @var bool|Preference
     */
    protected $UserPrefs = false;

    /**
     * A Hash::flattened array [path-key => default-value]
     * @var array
     */
    protected $defaults;

    /**
     * Paths of all valid preference values
     *
     * @var string[]
     */
    protected $validPaths;

    public $prefsSchema = [];

    public function __construct(EventManager $eventManager = null)
    {
        parent::__construct($eventManager);
        $schema = $this->schema();
        $this->validPaths = array_keys($this->prefsSchema ?? []);
        $prefDefaults = (collection($this->validPaths))
            ->reduce(function ($accum, $path) use ($schema) {
                if ($path != 'id') {
                    $accum = Hash::insert($accum, $path, $schema->field($path)['default']);
                }
                return $accum;
            }, []);

        $this->defaults = Hash::flatten($prefDefaults);
        return $this;
    }

    /**
     * @param Schema $schema
     * @return Schema
     */
    protected function _buildSchema(Schema $schema)
    {
        $schema->addFields($this->prefsSchema);
        $schema
            ->addField('id', [
                'type' => 'string'
            ]);
        return parent::_buildSchema($schema);
    }

    /**
     * Validate our flat schema
     *
     * Because we have structure encoded in our schema using
     * dot notation, we have to flatten the post to match,
     * then expand the errors. Errors go to the FormHelper
     * wich does not understand the flat version.
     *
     * @param array $data
     * @return bool
     */
    public function validate($data)
    {
        $result = parent::validate(Hash::flatten($data));
        $this->setErrors(Hash::expand($this->getErrors()));
        return $result;
    }

    /**
     * @return array
     */
    public function getDefaults(): array
    {
        return $this->defaults;
    }

    /**
     * @return string[]
     */
    public function getValidPaths(): array
    {
        return $this->validPaths;
    }

    /**
     * Process the validiation errors into flash messages
     *
     * RequiredStructure = [
     *      'pagination.limit' => [
     *          'greaterThan' => 'You must show more than zero items per page.'
     *      ],
     *      'pagination.sort.people' => [
     *          'inList' => 'Sorting can only be done on first_name or last_name',
     *          'notBad' => 'That was a bad value'
     *      ]
     * ]
     * @param $Flash FlashComponent
     */
    public function errorsToFlash($Flash)
    {
        $errors = $this->flattenErrors();
        if (Hash::check($errors,'id._required')) {
            $msg = Hash::get($errors, 'id._required');
            throw new BadPrefsImplementationException($msg);
        } else {
            foreach ($errors as $field => $msg) {
                $msg = implode(' ', $msg);
                $Flash->error($msg);
            }
        }
    }

    /**
     * Partially flatten the errors to work for Flash reporting
     *
     * @return array
     */
    private function flattenErrors()
    {
        $result = [];
        $errors = Hash::flatten($this->getErrors());
        $fullKeys = array_keys($errors);
        foreach ($fullKeys as $key) {
            $steps = explode('.', $key);
            $error = array_pop($steps);
            $path = implode('.', $steps);
            $result[$path][$error] = Hash::get($this->getErrors(), $key);
        }
        return $result;
    }

    public function validationDefault(Validator $validator)
    {
        return parent::validationDefault($validator);
    }    /**
     * Return this object with user prefs overwritting default values
     *
     * The users stored choices will overwrite the default values
     * in the schema. The form will now show the proper user values
     * when they have been set
     *
     * @param $userPrefs string
     */
    public function asContext($user_id)
    {
        if ($this->user_id === false || $this->user_id != $user_id) {
            $this->getUsersPrefsEntity($user_id);
        }
        $schema = $this->schema();

        $prefs = collection(Hash::flatten($this->UserPrefs->getVariants()));
        $overrides = $prefs->map(function($value, $fieldName) use ($schema) {
            $attributes = $schema->field($fieldName);
            $attributes['default'] = $value;
            return $attributes;
        })->toArray();

        $idAttributes = $schema->field('id');
        $idAttributes['default'] = $user_id;
        $overrides['id'] = $idAttributes;

        $this->schema()->addFields($overrides);

        return $this;
    }

    /**
     * Load the user prefs and add all defaults to it
     *
     * The current user preference record is retrieved then two
     * modifications are made. Looping on the current schema:
     *  1. Defaults values are written for each possible schema column
     *  2. The user's settings are cleaned so they only contain current schema
     *      columns and only then if there is a non-default value stored
     * If the entity changes in this 2nd stage it will be resaved.
     *
     * In this way we insure that no stale or invalid data is stored
     * in the users preference. Since this is the only way to get a
     * user's preference entity, we guarantee validity on every use.
     *
     * @param $user_id string
     * @return Preference
     */
//    public function getUsersPrefsEntity($user_id)
//    {
//        $this->user_id = $user_id;
//        if ($this->UserPrefs === false) {
//            $this->UserPrefs = (TableRegistry::getTableLocator()->get('Preferences'))
//                ->getPreferencesFor($user_id);
//            /* @var  Preference $userPrefs */
//
//            $schema = $this->schema();
//            $defaults = [];
//            $prefs = [];
//
//            //Make a list of all default values
//            //And filter any invalid prefs out of the json object
//            foreach ($schema->fields() as $path) {
//                $defaultValue = $schema->field($path)['default'];
//                $defaults[$path] = $defaultValue;
//                if (!in_array($this->UserPrefs->getVariant($path), [null, $defaultValue])) {
//                    $prefs = Hash::insert($prefs, $path, $this->UserPrefs->getVariant($path));
//                }
//            }
//            //set the default values into the entity
//            $this->UserPrefs->setDefaults($defaults);
//
//            //if the prefs list changed during filtering, save the corrected version
//            if ($this->UserPrefs->getVariants() != $prefs) {
//                $this->UserPrefs->setVariants($prefs);
//                (TableRegistry::getTableLocator()->get('Preferences'))
//                    ->save($this->UserPrefs);
//            }
//        }
//        return $this->UserPrefs;
//    }

    public function __debugInfo()
    {
        $info = parent::__debugInfo();

        $info['Prefs'] = is_object($info['Prefs'])
            ?'object (' . get_class($info['Prefs']) . ')'
            : $info['Prefs'];

        return $info;
    }
}
