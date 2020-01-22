<?php


namespace App\Lib;


use App\Exception\BadClassConfigurationException;
use App\Form\PreferencesForm;
use App\Model\Entity\Preference;
use App\Lib\PrefCon;
use http\Exception\BadQueryStringException;

class Prefs extends PrefsBase
{

    public $formVariant = false;

    public function __construct(Preference $entity, PreferencesForm $form, string $variant = null)
    {
        parent::__construct($entity, $form);
        if (!is_null($variant)) {
            $this->setFormVariant($variant);
        }
    }

    public function setFormVariant($variant)
    {
        if (!in_array($variant, PrefCon::$variants)) {
            $msg = 'Invalid preference variation requested';
            throw new BadQueryStringException($msg);
        }
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

}
