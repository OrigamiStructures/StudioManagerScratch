<?php
namespace App\Form;

use App\Model\Entity\Preference;
use Cake\Form\Form;
use Cake\Form\Schema;
use Cake\ORM\TableRegistry;
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
     * @param $user_id
     * @param $prefs array User submitted data
     */
    public function setUserPrefs($user_id, $prefs)
    {
        if (!$this->validate($prefs)) {
            return false;
        }

    }

    /**
     * @param $user_id
     * @param $prefs array User submitted data
     */
    public function clearUserPrefs($user_id, $prefs)
    {
        if (!$this->validate($prefs)) {
            return false;
        }

    }

    /**
     * @param $user_id
     */
    public function resetUserPrefs($user_id)
    {
        $this->UserPrefs = (TableRegistry::getTableLocator()->get('Preferences'))
            ->delete($user_id);
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
        $prefs = collection(Hash::flatten($this->UserPrefs->prefs));
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
     * Load the user prefs and add all defaults
     *
     * Defaults are required so the entity can always
     * answer 'value of x' questions
     *
     * @param $user_id string
     * @return Preference
     */
    public function getUserPrefs($user_id)
    {
        $this->user_id = $user_id;
        if ($this->UserPrefs === false) {
            $this->UserPrefs = (TableRegistry::getTableLocator()->get('Preferences'))
                ->getPreferncesFor($user_id);
            /* @var  Preference $userPrefs */

            $schema = $this->schema();
            $schemaFields = collection($schema->fields());
            $defaults = $schemaFields->reduce(function ($accum, $fieldName, $index) use ($schema) {
                $accum[$fieldName] = $schema->field($fieldName)['default'];
                return $accum;
            }, []);
            $this->UserPrefs->setDefaults(Hash::expand($defaults));
        }
        return $this->UserPrefs;
    }

    protected function _execute(array $data)
    {
        // Send an email.
        return true;
    }
}
