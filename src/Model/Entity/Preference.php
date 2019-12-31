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
        'created' => true,
        'modified' => true,
        'prefs' => true,
        'user_id' => true,
        'user' => true
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

    public function __debugInfo()
    {
        return [
            'id' => $this->id,
            'user_id (current supervisor)' => $this->user_id,
            'user prefs' => $this->prefs,
            'default prefs' => $this->defaults
        ];
    }
}
