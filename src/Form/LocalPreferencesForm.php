<?php


namespace App\Form;

use App\Exception\BadPrefsImplementationException;
use App\Form\PreferencesForm;
use App\Model\Entity\Preference;
use Cake\Controller\Component\FlashComponent;
use Cake\Form\Schema;
use Cake\Utility\Hash;
use Cake\Utility\Text;
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
        $validator->requirePresence('id', true, 'The user id must be included in all preference forms.');
        $validator->greaterThan(
            PrefCon::PAGINATION_LIMIT,
            0,
            'You must show more than zero items per page.'
        );
        $validator->inList(
            PrefCon::PAGINATION_SORT_PEOPLE,
            $this->values(PrefCon::PAGINATION_SORT_PEOPLE),
            'Sorting can only be done on ' . Text::toList(
                $this->values(PrefCon::PAGINATION_SORT_PEOPLE),
                'or'
            )
        );
        return $validator;
    }

    public function validate($data)
    {
        return parent::validate(Hash::flatten($data));
    }

    public function selectList($path)
    {
        return PrefCon::$lists[$path]['select'];
    }

    public function values($path)
    {
        return PrefCon::$lists[$path]['values'];
    }

    /**
     * @param $Flash FlashComponent
     */
    public function processErrors($Flash)
    {
        $errors = $this->getErrors();
        if (Hash::check($errors,'id._required')) {
            $msg = Hash::get($errors, 'id._required');
            throw new BadPrefsImplementationException($msg);
        } else {
            foreach ($errors as $field => $error) {
                $msg = implode(' ', $error);
                $Flash->error($msg);
            }
        }
    }

}

class PrefCon {

    const PAGINATION_LIMIT = 'pagination.limit';
    const PAGINATION_SORT_PEOPLE = 'pagination.sort.people';
    const PAGINATION_SORT_ARTWORK = 'pagination.sort.artwork';

    static public  $lists = [
        PrefCon::PAGINATION_SORT_PEOPLE => [
            'values' => ['first_name', 'last_name'],
            'select' => ['first_name' => 'First Name', 'last_name' => 'Last Name', 'x' => 'Bad Value']
        ],
        PrefCon::PAGINATION_SORT_ARTWORK => [
            'values' => [],
            'select' => []
        ],
    ];

}
