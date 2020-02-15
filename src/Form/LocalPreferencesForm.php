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
use App\Constants\PrefCon;

class LocalPreferencesForm extends PreferencesForm
{

    /**
     * @var bool|Prefs
     */
    public $Prefs = false;

    public $prefsSchema = [
        PrefCon::PAGING_CATEGORY => [
            'type' => 'json',
            'default' => [
                'limit' => 10,
                'dir' => 'asc',
                'sort' => 'last_name',
                'scope' => 'identities'
            ]
        ],
        PrefCon::PAGING_ORGANIZATION => [
            'type' => 'json',
            'default' => [
                'limit' => 10,
                'dir' => 'asc',
                'sort' => 'last_name',
                'scope' => 'identities'
            ]
        ],
        PrefCon::PAGING_PEOPLE=> [
            'type' => 'json',
            'default' => [
                'limit' => 10,
                'dir' => 'asc',
                'sort' => 'last_name',
                'scope' => 'identities'
            ]
        ],
        PrefCon::PAGING_ARTWORK => [
            'type' => 'json',
            'default' => [
                'limit' => 10,
                'dir' => 'asc',
                'sort' => 'title',
                'scope' => 'identities'
            ]
        ],
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
            PrefCon::PAGING_PEOPLE_LIMIT,
            0,
            'You must show more than zero items per page.'
        );
        $validator->inList(
            PrefCon::PAGING_PEOPLE_DIR,
            PrefCon::values(PrefCon::PAGING_PEOPLE_DIR),
            'Sort direction can only be ' . Text::toList(
                PrefCon::values(PrefCon::PAGING_PEOPLE_DIR),
                'or'
            )
        );
        $validator->inList(
            PrefCon::PAGING_PEOPLE_SORT,
            PrefCon::values(PrefCon::PAGING_PEOPLE_SORT),
            'Sorting can only be done on ' . Text::toList(
                PrefCon::values(PrefCon::PAGING_PEOPLE_SORT),
                'or'
            )
        );
        $validator->inList(
            PrefCon::PAGING_CATEGORY_SORT,
            PrefCon::values(PrefCon::PAGING_CATEGORY_SORT),
            'Sorting can only be done on ' . Text::toList(
                PrefCon::values(PrefCon::PAGING_CATEGORY_SORT),
                'or'
            )
        );
        $validator->inList(
            PrefCon::PAGING_ORGANIZATION_SORT,
            PrefCon::values(PrefCon::PAGING_ORGANIZATION_SORT),
            'Sorting can only be done on ' . Text::toList(
                PrefCon::values(PrefCon::PAGING_ORGANIZATION_SORT),
                'or'
            )
        );

        return parent::validationDefault($validator);
    }

    protected function _buildSchema(Schema $schema)
    {
        return parent::_buildSchema($schema);
    }
}
