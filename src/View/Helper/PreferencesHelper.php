<?php


namespace App\View\Helper;

use Cake\View\Helper;
use App\Form\PrefCon;


class PreferencesHelper extends Helper
{
    public $helpers = ['Form', 'Html'];

    protected $pageBreaks = [5, 10, 25, 50, 100];

    /**
     * @param $formContext
     */
    public function peoplePagination($formContext)
    {
        $this->getView()->append('prefs_form');
        echo $this->Form->create($formContext, ['action' => 'setPrefs']);

        echo $this->Html->tag(
            'ul',
            $this->Form->control(PrefCon::PAGINATION_LIMIT)
            . $this->Form->control(
                PrefCon::PAGINATION_SORT_PEOPLE, [
                'options' => $formContext->selectList(PrefCon::PAGINATION_SORT_PEOPLE),]),
            ['class' => 'menu']
        );
        echo $this->Form->control('id', ['type' => 'hidden']);
        echo $this->getView()->fetch('additional_controls');
        echo $this->Form->submit();
        echo $this->Form->end();
        $this->getView()->end();
    }
}
