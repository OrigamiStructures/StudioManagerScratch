<?php


namespace App\Lib;


use App\Exception\BadClassConfigurationException;

class Prefs extends PrefsBase
{

    const PAGINATION_LIMIT = 'pagination.limit';
    const PAGINATION_SORT_PEOPLE = 'pagination.sort.people';
    const PAGINATION_SORT_CATEGORY = 'pagination.sort.category';
    const PAGINATION_SORT_ORGANIZATION = 'pagination.sort.organization';
    const PAGINATION_SORT_ARTWORK = 'pagination.sort.artwork';

    const FORM_VARIANT_PERSON = 'person';
    const FORM_VARIANT_ORGANIZATON = 'organization';
    const FORM_VARIANT_CATEGORY = 'category';

    /**
     * 'values' are used for input validation checks
     * 'select' is used for form input generation
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
    public function selectList($path)
    {
        return $this->lists[$path]['select'];
    }

    public function values($path)
    {
        return $this->lists[$path]['values'];
    }

}
