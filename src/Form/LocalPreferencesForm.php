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
use App\Lib\PrefCon;

class LocalPreferencesForm extends PreferencesForm
{

    /**
     * @var bool|Prefs
     */
    public $Prefs = false;

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

    public function __construct(EventManager $eventManager = null)
    {
        parent::__construct($eventManager);
        return $this;
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
            PrefCon::values(PrefCon::PAGINATION_SORT_PEOPLE),
            'Sorting can only be done on ' . Text::toList(
                PrefCon::values(PrefCon::PAGINATION_SORT_PEOPLE),
                'or'
            )
        );
        $validator->inList(
            PrefCon::PAGINATION_SORT_CATEGORY,
            PrefCon::values(PrefCon::PAGINATION_SORT_CATEGORY),
            'Sorting can only be done on ' . Text::toList(
                PrefCon::values(PrefCon::PAGINATION_SORT_CATEGORY),
                'or'
            )
        );
        $validator->inList(
            PrefCon::PAGINATION_SORT_ORGANIZATION,
            PrefCon::values(PrefCon::PAGINATION_SORT_ORGANIZATION),
            'Sorting can only be done on ' . Text::toList(
                PrefCon::values(PrefCon::PAGINATION_SORT_ORGANIZATION),
                'or'
            )
        );

        return parent::validationDefault($validator);
    }

}
