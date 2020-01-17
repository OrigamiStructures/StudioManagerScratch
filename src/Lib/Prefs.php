<?php


namespace App\Lib;


class Prefs extends PrefsBase
{

    const PAGINATION_LIMIT = 'pagination.limit';
    const PAGINATION_SORT_PEOPLE = 'pagination.sort.people';
    const PAGINATION_SORT_ARTWORK = 'pagination.sort.artwork';

    public  $lists = [
        Prefs::PAGINATION_SORT_PEOPLE => [
            'values' => ['first_name', 'last_name'],
            'select' => ['first_name' => 'First Name', 'last_name' => 'Last Name', 'x' => 'Bad Value']
        ],
        Prefs::PAGINATION_SORT_ARTWORK => [
            'values' => [],
            'select' => []
        ],
    ];

}
