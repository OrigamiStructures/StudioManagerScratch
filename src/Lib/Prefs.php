<?php


namespace App\Lib;


use App\Exception\BadClassConfigurationException;
use App\Form\PreferencesForm;
use App\Model\Entity\Preference;
use App\Lib\PrefCon;
use http\Exception\BadQueryStringException;

class Prefs extends PrefsBase
{


    public function __construct(Preference $entity, PreferencesForm $form)
    {
        parent::__construct($entity, $form);
    }

    public function for($path)
    {
        return $this->getEntity()->for($path);
    }


}
