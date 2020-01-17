<?php


namespace App\Lib;


use App\Form\PreferencesForm;
use App\Model\Entity\Preference;
use Cake\Form\Form;
use Cake\ORM\Entity;

class PrefsBase
{
    /**
     * @var Preference
     */
    protected $entity;

    /**
     * @var PreferencesForm
     */
    protected $form;

    public  $lists = [];

    /**
     * Get the defined key => value list
     * @param $path string
     * @return array
     */
    public function selectList($path)
    {
        return $this->lists[$path]['select'];
    }

    /**
     * Get the defined value list
     *
     * @param $path string
     * @return array
     */
    public function values($path)
    {
        return $this->lists[$path]['values'];
    }


    public function __construct(Preference $entity, PreferencesForm $form)
    {
        $this->entity = $entity;
        $this->form = $form;
        return $this;
    }

    /**
     * @return Preference
     */
    public function getEntity(): Preference
    {
        return $this->entity;
    }

    /**
     * @return PreferencesForm
     */
    public function getForm(): PreferencesForm
    {
        return $this->form;
    }

}