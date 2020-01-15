<?php


namespace App\Controller;

use App\Controller\AppController;
use App\Exception\BadPrefsImplementationException;

class PreferencesController extends AppController
{

    public $components = ['Preferences'];

    /**
     * This will not be accessible for the API
     */
    public function setPrefs()
    {
        if (!$this->getRequest()->is('post')
            && !$this->getRequest()->is('put')
        ) {
            $msg = __("Preferences can only be changed through POST or PUT");
            throw new BadPrefsImplementationException($msg);
        }

        $prefsForm = $this->Preferences->getFormObjet();
        list($prefsForm, $prefs) = $this->Preferences->setPrefs();

        /*
         * see https://github.com/OrigamiStructures/StudioManagerScratch/issues/172
         */
//        if (empty($prefsForm->getErrors())) {
        return $this->redirect($this->referer());
//        }

        $this->set(compact('prefsForm', 'prefs'));
        $this->render('/UserPrefs/set_prefs');
    }

}
