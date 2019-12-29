<?php
namespace App\Form;

use Cake\Form\Form;
use Cake\Form\Schema;
use Cake\Validation\Validator;


class PreferencesForm extends Form
{

    protected function _buildSchema(Schema $schema)
    {
        return $schema
            ->addField(
            'paginate.limit', [
                'type' => 'integer',
                'default' => 10
            ])
            ->addField('paginate.sort.people', [
                'type' => 'string',
                'default' => 'last_name'
            ])
            ->addField('paginate.sort.artwork', [
                'type' => 'string',
                'default' => 'title'
            ]);
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

    public function validationDefault(Validator $validator)
    {
        $validator->integer(
            'paginate.limit',
            "Pagination limit must be the number of item you want on each page.",
            'update'
        );
        return $validator;
    }

    protected function _execute(array $data)
    {
        // Send an email.
        return true;
    }
}
