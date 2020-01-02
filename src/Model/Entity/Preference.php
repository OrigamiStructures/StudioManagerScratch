<?php
namespace App\Model\Entity;

use App\Exception\BadClassConfigurationException;
use Cake\ORM\Entity;
use Cake\Utility\Hash;

/**
 * Preference Entity
 *
 * @property int $id
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime $modified
 * @property string $prefs
 * @property string $user_id
 *
 */
class Preference extends Entity
{

    /**
     * Default values for preferences
     *
     * @var array
     */
    private $defaults = [];

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        'prefs' => true,
        'user_id' => true,
    ];

    public function for($path)
    {
//        $path = str_replace('-', '.', $path);
        $setting = Hash::get($this->prefs ?? [], $path) ?? Hash::get($this->defaults, $path);
        if (is_null($setting)) {
            $msg = "The preference '$path' has not been defined in PreferencesTable::defaults yet.";
            throw new BadClassConfigurationException($msg);
        }
        return $setting;
    }

    public function setDefaults($defaults)
    {
        $this->defaults = $defaults;
        $this->clean();
        return $this;
    }

    /**
     * Get the array of user prefs that aren't defaults
     *
     * @return array
     */
    public function getUserVariants()
    {
        return $this->prefs ?? [];
    }

    public function setVariants($array) {
        $this->prefs = $array;
    }

    public function setUserVariant($path, $value)
    {
        $this->prefs = Hash::insert($this->prefs ?? [], $path, $value);
    }

    public function getUserVariant($path)
    {
        return Hash::get($this->prefs ?? [], $path);
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
