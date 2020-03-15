<?php


namespace App\Controller;

use App\Controller\AppController;
use App\Exception\BadPrefsImplementationException;
use App\Interfaces\FilteringInterface;
use Cake\ORM\Query;

class PreferencesController extends AppController implements FilteringInterface
{

    public $components = ['Preferences'];

    /**
     * This will not be accessible for the API
     */
    public function setPrefs()
    {
        if (!$this->getRequest()->is(['post', 'patch', 'put']))
        {
            $msg = __("Preferences can only be changed through POST or PUT");
            throw new BadPrefsImplementationException($msg);
        }

        $this->Preferences->setPrefs();

        /*
         * see https://github.com/OrigamiStructures/StudioManagerScratch/issues/172
         */
//        if (empty($prefsForm->getErrors())) {
        return $this->redirect($this->referer());
//        }

//        $this->set(compact('prefsForm', 'prefs'));
//        $this->render('/UserPrefs/set_prefs');
    }

    /**
     * Null Implementation
     *
     * Contorllers that use PaginationController need to enable filtering
     * in combination with paging. In this case, we're doing background work
     * to set Perference paging values rather than actually paging data.
     * So we can ignore implementation of this Interface method
     *
     * @param Query $query
     * @return Query
     */
    public function userFilter(Query $query): Query
    {
        return $query;
    }
}
