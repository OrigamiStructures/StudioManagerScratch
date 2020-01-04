<?php
namespace App\Controller\Component;

use App\Controller\AppController;
use App\Form\LocalPreferencesForm;
use App\Form\PreferencesForm;
use App\Model\Entity\Preference;
use App\Model\Table\PreferencesTable;
use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\Core\App;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use Cake\Utility\Text;
use http\Exception\BadMethodCallException;

/**
 * Class PreferencesComponent
 * @package App\Controller\Component
 *
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
     * @var bool|PreferencesForm|LocalPreferencesForm
     */
    private $PreferenceForm = false;

    /**
     * @var bool|string
     */
    private $formClass = false;

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
     * @return Preference
     * @throws BadMethodCallException
     */
    public function setPrefs()
    {
        $prefsForm = $this->getFormObjet();
        $post = $this->getController()->getRequest()->getData();

        if ($prefsForm->validate($post)) {
            $prefs = $this->repository()->getPreferencesFor($post['id']);
            $userVariants = $prefs->getVariants();
            $prefsDefaults = $this->getPrefsDefaults();

            $allowedPrefs = collection($prefsForm->getValidPaths());
            $newVariants = $allowedPrefs
                ->reduce(function($accum, $path) use ($post, $prefsDefaults, $userVariants){
                    //if the post is default, leave variant out of the list
                    //if post is non-default, non-null
                    // or variant is non-null, variant must be included
                    // and we prefer post if its different than variant
                    $postValue = Hash::get($post, $path);
                    $variantValue = Hash::get($userVariants, $path);
                    if ( $postValue == $prefsDefaults[$path]) {
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
                $this->savePrefs($post, $prefs);
            } else {
                $this->Flash->success('No new preferences were requested');
            }
        }
        $prefs->setDefaults($prefsForm->getDefaults());

        return $prefs;
}

    /**
     * Unset one user preference
     *
     * @param $path
     */
    public function clearPrefs($user_id)
    {
        //read the persisted prefs
        $prefs = $this->repository()->getPreferencesFor($user_id);
        /* @var Preference $prefs */

        $prefs = $this->repository()->patchEntity($prefs, ['prefs' => []]);

        if ($this->repository()->save($prefs)) {
            $this->Flash->success('Your preferences were reset to the default values.');
        } else {
            $this->Flash->error('Your preferences were no reset. Please try again');
        }
        return;
    }

    /**
     * Get the ModellessForm object to use as a Form::create context
     *
     * The object will carry all user settings to the form as values
     *
     * @param $user_id
     * @return LocalPreferencesForm
     */
    public function getFormContextObject($user_id)
    {
        return $this->getFormObjet()->asContext($user_id);
    }

    /**
     * Get the user's preference entity
     *
     * fully stocked with all the default settings and user variants
     *
     * @param $user_id
     * @return Preference
     */
    public function getUserPrefsEntity($user_id)
    {
        return $this->getFormObjet()->getUsersPrefsEntity($user_id);
    }

    /**
     * Get the [path => value] array of all prefs and their default values
     * @return array
     */
    public function getPrefsDefaults()
    {
        return $this->getFormObjet()->getDefaults();
    }

    /**
     * Get the array of non-default settings for the user
     *
     * @param $user_id
     * @return array
     */
    public function getUserVariants($user_id)
    {
        return $this->getFormObjet()->getUsersPrefsEntity($user_id)->getVariants();
    }

    /**
     * Fully namespaced name of an override PreferencesForm class
     *
     * Normally LocalPreferencesForm extends PreferencesForm is used.
     * But any MyPrefForm extends PreferencesForm can be substituted
     *
     * @param $formClass
     */
    public function setFormClass($formClass)
    {
        $this->formClass = $formClass;
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
        $validPaths = $this->getFormObjet()->getValidPaths();
        $settings = collection(Hash::flatten($post));
        $settingSummaries = $settings->reduce(function ($accum, $value, $path) use ($validPaths) {
            if (in_array($path, $validPaths)) {
                $pref = str_replace('.', ', ', $path);
                $accum[] = "[$pref = $value]";
            }
            return $accum;
        }, []);
        $prefsSummary = new \stdClass();
        $prefsSummary->post = $post;
        $prefsSummary->summaryArray = $settingSummaries;
        $prefsSummary->summaryStatement = Text::toList($settingSummaries);
        $prefsSummary->count = count($settingSummaries);

        return $prefsSummary;
    }

    /**
     * @return PreferencesForm|LocalPreferencesForm
     */
    public function getFormObjet()
    {
        if ($this->PreferenceForm == false && !$this->formClass == false) {
            $class = $this->formClass;
            $this->PreferenceForm = new $class();
        } else {
            $this->PreferenceForm = new LocalPreferencesForm();
        }
        return $this->PreferenceForm;
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

    /**
     * @param $post
     * @param Preference $prefs
     */
    protected function savePrefs($post, Preference $prefs): void
    {
        $settingSummaries = $this->summarizeSettings($post ?? []);

        if ($this->repository()->save($prefs)) {
            $msg = $settingSummaries->count > 1
                ? __("Your preferences $settingSummaries->summaryStatement were saved.")
                : __("Your preference for $settingSummaries->summaryStatement was saved.");
            $this->Flash->success($msg);
        } else {
            $msg = $settingSummaries->count > 1
                ? __("Your preferences $settingSummaries->summaryStatement were not saved. Please try again")
                : __("Your preference for $settingSummaries->summaryStatement was not saved. Please try again");
            $this->Flash->error($msg);
        }
    }

}
