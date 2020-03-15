<?php


namespace App\Constants;


class PrefCon
{
    /**
     * Used to cut down on typo-based errors
     *
     * PAGINATION... coordinates schema entries, validation rules, and
     * form control creation
     */

    const PAGING_PEOPLE = 'paging.people';
    const PAGING_PEOPLE_LIMIT = 'paging.people.limit';
    const PAGING_PEOPLE_DIR = 'paging.people.dir';
    const PAGING_PEOPLE_SORT = 'paging.people.sort';

    const PAGING_CATEGORY = 'paging.category';
    const PAGING_CATEGORY_LIMIT = 'paging.category.limit';
    const PAGING_CATEGORY_DIR = 'paging.category.dir';
    const PAGING_CATEGORY_SORT = 'paging.category.sort';

    const PAGING_ORGANIZATION = 'paging.organization';
    const PAGING_ORGANIZATION_LIMIT = 'paging.organization.limit';
    const PAGING_ORGANIZATION_DIR = 'paging.organization.dir';
    const PAGING_ORGANIZATION_SORT = 'paging.organization.sort';

    const PAGING_ARTWORK = 'paging.artwork';
    const PAGING_ARTWORK_LIMIT = 'paging.artwork.limit';
    const PAGING_ARTWORK_DIR = 'paging.artwork.dir';
    const PAGING_ARTWORK_SORT = 'paging.artwork.sort';

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
        PrefCon::PAGING_PEOPLE_SORT => [
            'values' => ['first_name', 'last_name', 'collector'],
            'select' => ['first_name' => 'First Name', 'last_name' => 'Last Name', 'collector' => 'Collected Works']
        ],
        PrefCon::PAGING_CATEGORY_SORT => [
            'values' => ['last_name'],
            'select' => ['last_name' => 'Name']
        ],
        PrefCon::PAGING_ORGANIZATION_SORT => [
            'values' => ['last_name'],
            'select' => ['last_name' => 'Name']
        ],
        PrefCon::PAGING_PEOPLE_DIR => [
            'values' => ['asc', 'desc'],
            'select' => ['asc' => 'Ascending', 'desc' => 'Descending']
        ],
        PrefCon::PAGING_ORGANIZATION_DIR => [
            'values' => ['asc', 'desc'],
            'select' => ['asc' => 'Ascending', 'desc' => 'Descending']
        ],
        PrefCon::PAGING_CATEGORY_DIR => [
            'values' => ['asc', 'desc'],
            'select' => ['asc' => 'Ascending', 'desc' => 'Descending']
        ],
        PrefCon::PAGING_ARTWORK_SORT => [
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
