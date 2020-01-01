<?php
namespace App\Controller\Component;


use App\Controller\AppController;
use App\Form\LocalPreferencesForm;
use App\Model\Entity\Preference;
use App\Model\Table\PreferencesTable;
use Cake\Controller\Component;
use Cake\Core\App;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use Cake\Utility\Text;
use http\Exception\BadMethodCallException;

/**
 * Class PreferencesComponent
 * @package App\Controller\Component
 *
 * @method AppController getController()
 * @property \FlashComponent $Flash
 */
class PreferencesComponent extends Component
{

    /**
     * A reference to the Preferences table object
     *
     * @var bool|PreferencesTable
     */
    private $repository = false;

    /**
     * @var array Components used by this component
     */
    public $components = ['Flash'];

    /**
     * Using this component will automatically make PreferencesHelper available
     *
     * @param array $config
     */
    public function initialize(array $config)
    {
        parent::initialize($config);
        $this->getController()->viewBuilder()->setHelpers(['Preferences']);

    }

    /**
     *
     * @return \Cake\Http\Response|null
     * @throws BadMethodCallException
     */
    public function setPref()
    {
        $controller = $this->getController();
        /* @var AppController $controller */

        if (!$controller->getRequest()->is(['post', 'put'])) {
            //@todo How do we keep this invisible to API calls?
            $msg = __("Preferences can only be changed through POST or PUT");
            throw new BadMethodCallException($msg);
        }

        $prefsForm = new LocalPreferencesForm();
        $post = $controller->getRequest()->getData();
        if ($prefsForm->validate($post)) {

            $supervisor_id = $controller->contextUser()->getId('supervisor');
            $prefs = $this->repository()->getPreferencesFor($supervisor_id);
            $userVariants = $prefs->userVariants();
            $schema = $prefsForm->schema();

            $allowedPrefs = collection($prefsForm->schema()->fields());
            $newVariants = $allowedPrefs
                ->reduce(function($accum, $path) use ($post, $schema, $userVariants){
                    //if the post is default, leave variant out of the list
                    //if post is non-default, non-null
                    // or variant is non-null, variant must be included
                    // and we prefere post if its different than variant
                    $defaultValue = $schema->field($path)['default'];
                    $postValue = Hash::get($post, $path);
                    $variantValue = Hash::get($userVariants, $path);
                    if ( $postValue == $defaultValue ) {
                        //let variant evaporate
                    } elseif (!is_null($variantValue) ||  !is_null($postValue)) {
                        $accum = Hash::insert(
                            $accum,
                            $path,
                            $variantValue != $postValue ? $postValue : $variantValue
                        );
                    }
                    return $accum;
                }, []);
            if ($newVariants != $userVariants) {
                $prefs->setVariants($newVariants);
            }
            osd($prefs, 'prefs entity');
            osd($post);die;

            $settingSummaries = $this->summarizeSettings($post ?? []);

            if (!$this->repository()->save($prefs)) {
                $msg = $settingSummaries->count > 1
                    ? __("Your preferences $settingSummaries->summaryStatement were not saved. Please try again")
                    : __("Your preference for $settingSummaries->summaryStatement was not saved. Please try again");
                $this->Flash->error($msg);
            } else {
                $msg = $settingSummaries->count > 1
                    ? __("Your preferences $settingSummaries->summaryStatement were saved.")
                    : __("Your preference for $settingSummaries->summaryStatement was saved.");
                $this->Flash->error($msg);
            }
        }

        return $controller->redirect($controller->referer());
}

    /**
     * Unset one user preference
     *
     * @param $path
     */
    public function clearPref($path)
    {
        $controller = $this->getController();
        /* @var AppController $controller */
        $supervisor_id = $controller->contextUser()->getId('supervisor');

        //read the persisted prefs
        $prefs = $this->repository()->getPreferncesFor($supervisor_id);
        /* @var Preference $prefs */

        $prefs->prefs = Hash::remove($prefs->prefs, $path);


    }

    /**
     * Make a simple object with versions of the posted user prefs for messaging
     *
     * $post is posted data array
     *  [
     *      ['path.to.set' => 'value']
     *      ['another.pref => '42']
     *  ]
     *
     * From that example $prefsSummary will be:
     * stdClass {
     *  post =              ['path.to.set' => 'value',
     *                       'another.pref' => '42']
     *  summaryArray =      ['path, to, set = value',
     *                       'another, pref = 42']
     *  summaryStatement =  'path, to, set = value and another, path = 42'
     *  count =             2
     * }
     *
     * @param array $post
     * @return \stdClass
     */
    private function summarizeSettings(array $post): \stdClass
    {
        $settings = collection(Hash::flatten($post));
        $settingSummaries = $settings->map(function ($value, $key) {
            $pref = str_replace('.', ', ', $key);
            return "[$pref = $value]";
        })
            ->toArray();
        $prefsSummary = new \stdClass();
        $prefsSummary->post = $post;
        $prefsSummary->summaryArray = $settingSummaries;
        $prefsSummary->summaryStatement = Text::toList($settingSummaries);
        $prefsSummary->count = count($post);

        return $prefsSummary;
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
