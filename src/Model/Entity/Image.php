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
     * Provide No Image default for a missing image. This no-image dodge only 
	 * works if the record exists but the image does not. If there is no record, 
	 * there will be no entity for the View to call to for the image.
     * 
     * @return string
     */
    public function fullPath($size = "small") {
        $path = "";
        $image = "NoImage.png";
        if(!empty($this->image_dir)){
            $path = '../files/images/image_file/' . $this->image_dir . DS;
        }
        if(!empty($this->image_file)){
            $image = $size . '_' . $this->image_file;
        }
        return $path . $image;
    }
}