<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Image Entity.
 *
 * @property int $id
 * @property \Cake\I18n\Time $modified
 * @property \Cake\I18n\Time $created
 * @property int $user_id
 * @property \App\Model\Entity\User $user
 * @property string $image
 * @property string $image_dir
 * @property string $mimetype
 * @property int $filesize
 * @property int $width
 * @property int $height
 * @property string $title
 * @property int $date
 * @property string $alt
 * @property int $upload
 * @property \App\Model\Entity\Artwork[] $artworks
 * @property \App\Model\Entity\Format[] $formats
 * @property \App\Model\Entity\Member[] $members
 */
class Image extends Entity
{

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
        '*' => true,
        'id' => false,
    ];
    
    /**
     * Return the full path for the image helper based upon the existance of a directory
     * 
     * @return string
     */
    public function _getFullPath() {
        return empty($this->image_dir)
            ? $this->image
            : $this->image_dir . DS . $this->image;
    }
}
