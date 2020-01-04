<?php


namespace App\View\Helper;

use Cake\View\Helper;


class PreferencesHelper extends Helper
{
    public $helpers = ['Form', 'Html'];

    protected $pageBreaks = [5, 10, 25, 50, 100];

    public function pref()
    {
        echo $this->Html->para('something', 'Some text');
    }
}