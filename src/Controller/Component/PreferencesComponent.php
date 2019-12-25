<?php
namespace App\Controller\Component;


use App\Controller\AppController;
use App\Model\Entity\Preference;
use App\Model\Table\PreferencesTable;
use Cake\Controller\Component;
use Cake\ORM\TableRegistry;

/**
 * Class PreferencesComponent
 * @package App\Controller\Component
 *
 * @method Component getController() : AppController
 */
class PreferencesComponent extends Component
{

    /**
     * @var bool|PreferencesTable
     */
    private $repository = false;

    public function setPref()
    {
        $controller = $this->getController();
        /* @var AppController $controller */
        $supervisor_id = $controller->contextUser()->getId('supervisor');

        //New prefs arrive in some standard, decodable POST (or GET?)

        //read the persisted prefs
        $prefs = $this->repository()->getPreferncesFor($supervisor_id);
        osd($prefs);
        osd($prefs->for($prefs::PER_PAGE), '$prefs->for($prefs::PER_PAGE)');
        osd($prefs->for($prefs::KEY_TO_ARRAY.'.1'), '$prefs->for($prefs::KEY_TO_ARRAY.\'.1\')');
        osd($prefs->for($prefs::KEY_TO_ARRAY.'.0'), '$prefs->for($prefs::KEY_TO_ARRAY.\'.0\'');
        die;
        //Modify prefs to contain new setting
        //write the prefs to storage
        return $controller->redirect($controller->referer());
    }

    private function loadPrefs($supervisor_id)
    {
    }
    /**
     * Get the Preferences table instance
     *
     * @return PreferencesTable
     */
    private function repository()
    {
        if ($this->repository === false) {
            $this->repository = TableRegistry::getTableLocator()->get('Preferences');
        }
        return $this->repository;
    }

}
