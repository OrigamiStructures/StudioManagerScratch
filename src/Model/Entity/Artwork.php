<?php
namespace App\Model\Entity;
use App\Model\Entity\Image;
use Cake\ORM\Entity;
use App\Model\Entity\Traits\ParentEntityTrait;
use App\Model\Entity\Traits\EntityDebugTrait;


/**
 * Artwork Entity.
 *
 * @property int $id
 * @property \Cake\I18n\Time $created
 * @property \Cake\I18n\Time $modified
 * @property int $user_id
 * @property \App\Model\Entity\User $user
 * @property int $image_id
 * @property string $title
 * @property string $description
 * @property \App\Model\Entity\Edition[] $editions
 */
class Artwork extends Entity
{
	
	use ParentEntityTrait;
	use EntityDebugTrait;

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
	
	protected $_links = ['editions', 'user', 'image', 'images'];

    
	/**
	 * From an inverted artwork stack, identify the tip-of-the-iceberg
	 * 
	 * A normal artwork stack begins with the Artwork and sees all the children. 
	 * An inverted stack, such as that linked to a disposition, starts art the 
	 * child and contains the specific entity chain up to the Artwork. This method 
	 * adds to the label for the child piece
	 * 
	 * @return string
	 */
	public function identityLabel() {
		return $this->title;
	}
	
	/**
	 * Is there one and only one edition with one and only one format for the Artwork
	 * 
	 * @return boolean
	 */
	public function isFlat() {
		return ($this->edition_count === 1 && $this->editions[0]->isFlat());
	}
	
}
