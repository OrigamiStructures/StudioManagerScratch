<?php


namespace App\Form;

use App\Form\PreferencesForm;
use Cake\Form\Schema;
use Cake\Validation\Validator;

class LocalPreferencesForm extends PreferencesForm
{

    const PAGINATION_LIMIT = 'paginate.limit';
    const PAGINATION_SORT_PEOPLE = 'paginate.sort.people';
    const PAGINATION_SORT_ARTWORK = 'paginate.sort.artwork';

    /**
     * @param Schema $schema
     * @return Schema
     */
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
     * @param Validator $validator
     * @return Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator->integer(
            'paginate.limit',
            "Pagination limit must be the number of item you want on each page.",
            'update'
        );
        return $validator;
    }

}
