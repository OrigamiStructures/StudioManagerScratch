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
use App\Lib\Prefs;

class LocalPreferencesForm extends PreferencesForm
{

    /**
     * @var bool|Prefs
     */
    public $Prefs = false;

    public $prefsSchema = [
        Prefs::PAGINATION_LIMIT => [
            'type' => 'integer',
            'default' => 10
        ],
        Prefs::PAGINATION_SORT_PEOPLE => [
            'type' => 'string',
            'default' => 'last_name'
        ],
        Prefs::PAGINATION_SORT_ARTWORK => [
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
            Prefs::PAGINATION_LIMIT,
            0,
            'You must show more than zero items per page.'
        );
        $validator->inList(
            Prefs::PAGINATION_SORT_PEOPLE,
            $this->Prefs->values(Prefs::PAGINATION_SORT_PEOPLE),
            'Sorting can only be done on ' . Text::toList(
                $this->Prefs->values(Prefs::PAGINATION_SORT_PEOPLE),
                'or'
            )
        );
        $validator->inList(
            Prefs::PAGINATION_SORT_CATEGORY,
            $this->Prefs->values(Prefs::PAGINATION_SORT_CATEGORY),
            'Sorting can only be done on ' . Text::toList(
                $this->Prefs->values(Prefs::PAGINATION_SORT_CATEGORY),
                'or'
            )
        );
        $validator->inList(
            Prefs::PAGINATION_SORT_ORGANIZATION,
            $this->Prefs->values(Prefs::PAGINATION_SORT_ORGANIZATION),
            'Sorting can only be done on ' . Text::toList(
                $this->Prefs->values(Prefs::PAGINATION_SORT_ORGANIZATION),
                'or'
            )
        );

        return parent::validationDefault($validator);
    }

}

class PrefCon {

//    const PAGINATION_LIMIT = 'pagination.limit';
//    const PAGINATION_SORT_PEOPLE = 'pagination.sort.people';
//    const PAGINATION_SORT_ARTWORK = 'pagination.sort.artwork';
//
//    static public  $listss = [
//        PrefCon::PAGINATION_SORT_PEOPLE => [
//            'values' => ['first_name', 'last_name'],
//            'select' => ['first_name' => 'First Name', 'last_name' => 'Last Name', 'x' => 'Bad Value']
//        ],
//        PrefCon::PAGINATION_SORT_ARTWORK => [
//            'values' => [],
//            'select' => []
//        ],
//    ];

}
