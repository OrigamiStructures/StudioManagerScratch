<?php


namespace App\Form;

use App\Form\PreferencesForm;
use Cake\Form\Schema;
use Cake\Validation\Validator;

class LocalPreferencesForm extends PreferencesForm
{

    /**
     * @param Schema $schema
     * @return Schema
     */
    protected function _buildSchema(Schema $schema)
    {
        return $schema
            ->addField(
                PrefCon::PAGINATION_LIMIT, [
                'type' => 'integer',
                'default' => 10
            ])
            ->addField(PrefCon::PAGINATION_SORT_PEOPLE, [
                'type' => 'string',
                'default' => 'last_name'
            ])
            ->addField(PrefCon::PAGINATION_SORT_ARTWORK, [
                'type' => 'string',
                'default' => 'title'
            ])
            ->addField('id', [
                'type' => 'string'
            ]);
    }

    /**
     * @param Validator $validator
     * @return Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator->requirePresence('id');
//        $validator->integer(
//            'paginate.limit',
//            "Pagination limit must be the number of item you want on each page.",
//            'update'
//        );
        return $validator;
    }

}

class PrefCon {

    const PAGINATION_LIMIT = 'paginate.limit';
    const PAGINATION_SORT_PEOPLE = 'paginate.sort.people';
    const PAGINATION_SORT_ARTWORK = 'paginate.sort.artwork';

}
