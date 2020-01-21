<?php


namespace App\Lib;


use App\Exception\BadClassConfigurationException;
use http\Exception\BadQueryStringException;

class Prefs extends PrefsBase
{

    /**
     * Used to cut down on typo-based errors
     *
     * PAGINATION... coordinates schema entries, validation rules, and
     * form control creation
     */
    const PAGINATION_LIMIT = 'pagination.limit';
    const PAGINATION_SORT_PEOPLE = 'pagination.sort.people';
    const PAGINATION_SORT_CATEGORY = 'pagination.sort.category';
    const PAGINATION_SORT_ORGANIZATION = 'pagination.sort.organization';
    const PAGINATION_SORT_ARTWORK = 'pagination.sort.artwork';

    /**
     * Used to cut down on typo-based errors
     *
     * FORM_VARIANT... used when coordination form elements with the kind
     * of data prepared by the controller action
     */
    const FORM_VARIANT_PERSON = 'person';
    const FORM_VARIANT_ORGANIZATON = 'organization';
    const FORM_VARIANT_CATEGORY = 'category';

    /**
     * used to validate params
     *
     * @var array
     */
    public $variants = [
        self::FORM_VARIANT_CATEGORY,
        self::FORM_VARIANT_ORGANIZATON,
        self::FORM_VARIANT_PERSON
    ];

    /**
     * 'values' are used for input validation checks
     *
     * 'select' is used to coordinate validation checks, and form control creation
     *
     * @var array
     */
    public  $lists = [
        Prefs::PAGINATION_SORT_PEOPLE => [
            'values' => ['first_name', 'last_name'],
            'select' => ['first_name' => 'First Name', 'last_name' => 'Last Name', 'x' => 'Bad Value']
        ],
        Prefs::PAGINATION_SORT_CATEGORY => [
            'values' => ['name'],
            'select' => ['name' => 'Name']
        ],
        Prefs::PAGINATION_SORT_ORGANIZATION => [
            'values' => ['name'],
            'select' => ['name' => 'Name']
        ],
        Prefs::PAGINATION_SORT_ARTWORK => [
            'values' => [],
            'select' => []
        ],
    ];

    public $formVariant = false;

    public function setFormVariant($variant)
    {
        if (!in_array($variant, $this->variants)) {
            $msg = 'Invalid preference variation requested';
            throw new BadQueryStringException($msg);
        }
        $this->formVariant = $variant;
    }
    public function getFormVariant()
    {
        if ($this->formVariant == false) {
            $msg = 'The specific variation of the preferences form was never specified';
            throw new BadClassConfigurationException($msg);
        }
        return $this->formVariant;
    }
}
