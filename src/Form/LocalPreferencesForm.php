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

    public $prefsSchema = [
        PrefCon::PAGINATION_LIMIT => [
            'type' => 'integer',
            'default' => 10
        ],
        PrefCon::PAGINATION_SORT_PEOPLE => [
            'type' => 'string',
            'default' => 'last_name'
        ],
        PrefCon::PAGINATION_SORT_ARTWORK => [
            'type' => 'string',
            'default' => 'title'
        ]
    ];

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

    public function selectList($path)
    {
        return PrefCon::$lists[$path]['select'];
    }

    public function values($path)
    {
        return PrefCon::$lists[$path]['values'];
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
    public function processErrors($Flash)
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
