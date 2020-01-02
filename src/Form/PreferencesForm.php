<?php
namespace App\Form;

use App\Model\Entity\Preference;
use Cake\Form\Form;
use Cake\Form\Schema;
use Cake\ORM\TableRegistry;
use Cake\Utility\Text;
use Cake\Validation\Validator;
use Cake\Utility\Hash;

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
    protected $prefDefaults;

    /**
     * Paths of all valid preference values
     *
     * @var string[]
     */
    protected $availablePrefs;

    public function __construct(EventManager $eventManager = null)
    {
        parent::__construct($eventManager);
        $schema = clone $this->schema();
        $this->availablePrefs = $schema->fields();
        $prefDefaults = (collection($this->availablePrefs))
            ->reduce(function ($accum, $path) use ($schema) {
                $accum = Hash::insert($accum, $path, $schema->field($path)['default']);
                return $accum;
            }, []);

        $this->prefDefaults = Hash::flatten($prefDefaults);
        return $this;
    }

    /**
     * @return array
     */
    public function getPrefDefaults(): array
    {
        return $this->prefDefaults;
    }

    /**
     * @return string[]
     */
    public function getAvailablePrefs(): array
    {
        return $this->availablePrefs;
    }

    /**
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
            $this->getUserPrefs($user_id);
        }
        $schema = $this->schema();
        /*
         * the values the user has set for their self in this form:
         * [
         *  'field.name' => 'value',
         *  'another.field.here' => 'value'
         * ]
         *
         * return $this
         */
        $prefs = collection(Hash::flatten($this->UserPrefs->getUserVariants()));
        $overrides = $prefs->map(function($value, $fieldName) use ($schema) {
            $attributes = $schema->field($fieldName);
            $attributes['default'] = $value;
            return $attributes;
        })->toArray();

        $this->schema()->addFields($overrides);

        return $this;
    }

    /**
     * @todo This may not be needed. But I feel uneasy modifying
     *      the schema to reflect the user settings. I may be
     *      worrying about nothing. This is a short lived object.
     *
     * @return $this
     */
    public function resetFormDefaults()
    {
        $this->_buildSchema();
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
    public function getUserPrefs($user_id)
    {
        $this->user_id = $user_id;
        if ($this->UserPrefs === false) {
            $this->UserPrefs = (TableRegistry::getTableLocator()->get('Preferences'))
                ->getPreferencesFor($user_id);
            /* @var  Preference $userPrefs */
            pj($this->UserPrefs->prefs);

            $schema = $this->schema();
            $defaults = [];
            $prefs = [];
            foreach ($schema->fields() as $path) {
                $defaultValue = $schema->field($path)['default'];
                $defaults[$path] = $defaultValue;
                if (!in_array($this->UserPrefs->getUserVariant($path), [null, $defaultValue])) {
                    $prefs = Hash::insert($prefs, $path, $this->UserPrefs->getUserVariant($path));
                }
            }
            $this->UserPrefs->setDefaults($defaults);
            if ($this->UserPrefs->getUserVariants() != $prefs) {
                $this->UserPrefs->setVariants($prefs);
                (TableRegistry::getTableLocator()->get('Preferences'))
                    ->save($this->UserPrefs);
            }
        }
        return $this->UserPrefs;
    }

    protected function _execute(array $data)
    {
        // Send an email.
        return true;
    }

}
