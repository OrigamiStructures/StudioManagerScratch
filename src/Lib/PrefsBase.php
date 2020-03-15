<?php


namespace App\Lib;


use App\Exception\UnknownPreferenceKeyException;
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

    public function __construct(Preference $entity, PreferencesForm $form)
    {
        $this->entity = $entity;
        $this->form = $form;
        $this->form->Prefs = $this;
        return $this;
    }

    /**
     * @param $path
     * @return mixed
     * @throws \BadMethodCallException
     */
    public function for($path)
    {
        try {
            return $this->getEntity()->for($path);
        } catch (UnknownPreferenceKeyException $e) {
            throw $e;
        }
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
