<?php


namespace App\Lib;


class PrefCon
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
    public static $variants = [
        PrefCon::FORM_VARIANT_CATEGORY,
        PrefCon::FORM_VARIANT_ORGANIZATON,
        PrefCon::FORM_VARIANT_PERSON
    ];

    /**
     * 'values' are used for input validation checks
     *
     * 'select' is used to coordinate validation checks, and form control creation
     *
     * @var array
     */
    public static $lists = [
        PrefCon::PAGINATION_SORT_PEOPLE => [
            'values' => ['first_name', 'last_name'],
            'select' => ['first_name' => 'First Name', 'last_name' => 'Last Name', 'x' => 'Bad Value']
        ],
        PrefCon::PAGINATION_SORT_CATEGORY => [
            'values' => ['last_name'],
            'select' => ['last_name' => 'Name']
        ],
        PrefCon::PAGINATION_SORT_ORGANIZATION => [
            'values' => ['last_name'],
            'select' => ['last_name' => 'Name']
        ],
        PrefCon::PAGINATION_SORT_ARTWORK => [
            'values' => [],
            'select' => []
        ],
    ];

    /**
     * Get the defined value list
     *
     * @param $path string
     * @return array
     */
    public static function values($path)
    {
        return self::$lists[$path]['values'];
    }

    /**
     * Get the defined key => value list
     * @param $path string
     * @return array
     */
    public static function selectList($path)
    {
        return self::$lists[$path]['select'];
    }

}
