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
    const PAGINATION_LIMIT = 'pagination.limit';
    const PAGINATION_DIR = 'pagination.dir';
    const PAGINATION_SORT_PEOPLE = 'pagination.sort.people';
    const PAGINATION_SORT_CATEGORY = 'pagination.sort.category';
    const PAGINATION_SORT_ORGANIZATION = 'pagination.sort.organization';
    const PAGINATION_SORT_ARTWORK = 'pagination.sort.artwork';

    const PAGING_LIMIT = 'paging.common.limit';
    const PAGING_DIR = 'paging.common.dir';
    const PAGING_COMMON = 'paging.common';

    const PAGING_CARD = 'paging.card';
    const PAGING_CARD_LIMIT = 'paging.card.limit';
    const PAGING_CARD_DIR = 'paging.card.dir';
    const PAGING_CARD_SORT = 'paging.card.sort';

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
        PrefCon::PAGINATION_DIR => [
            'values' => ['asc', 'desc'],
            'select' => ['asc' => 'Ascending', 'desc' => 'Descending']
        ],
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
