<?php
namespace App\Form;

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
 * @package App\Form
 */
class PreferencesForm extends Form
{

    public function setUserPrefs($user_id, $prefs)
    {

    }

    public function clearUserPrefs($user_id, $prefs)
    {

    }

    public function resetUserPrefs($user_id, $prefs)
    {

    }

    /**
     * Pass in a list of names and values
     *
     * [
     *  'field.name' => 'value',
     *  'another.field.here' => 'value'
     * ]
     *
     * These are the users stored choices and they will overwrite
     * the default values in the schema. The schema is the 'context'
     * object for the form, so the form will now show the proper
     * user values when they have been set
     *
     * @param $userPrefs
     */
    public function overrideDefaults($userPrefs)
    {
        $schema = $this->schema();

        $prefs = collection($userPrefs);
        $overrides = $prefs->map(function($value, $fieldName) use ($schema) {
            $attributes = $schema->field($fieldName);
            $attributes['default'] = $value;
            return $attributes;
        })->toArray();

        $this->schema()->addFields($overrides);
    }

    public function getUserPrefs($user_id)
    {
        /* @var  */

        $userPrefs = (TableRegistry::getTableLocator()->get('Preferences'))
            ->getPreferncesFor($user_id); //@todo Thes can be paramterized for a Plugin

        $schema = $this->schema();

        $schemaFields = collection($schema->fields());
        $defaults = $schemaFields->reduce(function($accum, $fieldName, $index) use ($schema) {
            $accum[$fieldName] = $schema->field($fieldName)['default'];
            return $accum;
        }, []);

        return $userPrefs->setDefaults(Hash::expand($defaults));
    }

    protected function _execute(array $data)
    {
        // Send an email.
        return true;
    }
}
