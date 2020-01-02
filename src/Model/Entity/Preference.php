<?php
namespace App\Model\Entity;

use App\Exception\BadClassConfigurationException;
use Cake\ORM\Entity;
use Cake\Utility\Hash;

/**
 * Preference Entity
 *
 * To get a propery constructed entity you must use the getter
 * PreferencesComponent::getUsersPrefsEntity(user_id) or
 * (Concrete)PreferencesForm::getUserPrefsEntity(user_id).
 * These methods will set the $this::defaults property
 *
 * @property int $id
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime $modified
 * @property json $prefs
 * @property string $user_id
 *
 */
class Preference extends Entity
{

    /**
     * Default values for preferences
     *
     * Set by the PreferenceForm class using the current schema
     * [path.to.value => value]
     *
     * @var array
     */
    private $defaults = false;

    /**
     * Fields fields used for newEntity() or patchEntity().
     *
     * @var array
     */
    protected $_accessible = [
        'prefs' => true,
        'user_id' => true,
    ];

    /**
     * Get the current value for a preference
     *
     * Will use the user's value if present, otherwise, the default value
     *
     * @param $path
     * @return mixed
     */
    public function for($path)
    {
        if ($this->defaults === false) {
            $msg = "Preferenes entity must have the default preference values set.";
            throw new BadClassConfigurationException($msg);
        }
        $setting = Hash::get($this->prefs ?? [], $path) ?? $this->defaults[$path];
        if (is_null($setting)) {
            $msg = "The preference '$path' has not been defined in PreferencesTable::defaults yet.";
            throw new BadClassConfigurationException($msg);
        }
        return $setting;
    }

    /**
     * Provide the array of defaults
     *
     * [path.to.value => value]
     *
     * @param $defaults
     * @return $this
     */
    public function setDefaults($defaults)
    {
        $this->defaults = $defaults;
        $this->clean();
        return $this;
    }

    /**
     * Get the array of user prefs that aren't defaults
     *
     * [path =>
     *      [to =>
     *          [pref => value]
     *      ]
     * ]
     *
     * @return array
     */
    public function getVariants()
    {
        return $this->prefs ?? [];
    }

    /**
     * Swap in a new prefs array
     *
     * [path =>
     *      [to =>
     *          [pref => value]
     *      ]
     * ]
     *
     * @param $array
     */
    public function setVariants($array) {
        $this->prefs = $array;
    }

    /**
     * Insert (or overwrite) a value in the user's preferences
     *
     * @param $path
     * @param $value
     */
    public function setVariant($path, $value)
    {
        $this->prefs = Hash::insert($this->prefs ?? [], $path, $value);
    }

    /**
     * Get a single user value or null if they haven't moved from default
     *
     * @param $path
     * @return mixed
     */
    public function getVariant($path)
    {
        return Hash::get($this->prefs ?? [], $path);
    }

    /**
     * get the user id
     *
     * @return string
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    public function __debugInfo()
    {
        $data = [
            'defaults' => $this->defaults
        ];
        $original = parent::__debugInfo();
        return array_merge($data, $original);
    }
}
