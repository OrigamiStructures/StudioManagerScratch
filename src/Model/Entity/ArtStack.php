<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;
use App\Lib\Traits\StackTrait;
use App\Lib\Layer;

/**
 * ArtStack Entity
 *
 * @property \App\Model\Artwork\Artwork $artwork
 * @property \App\Model\Entity\Edition[] $editions
 * @property \App\Model\Entity\Format[] $formats
 * @property \App\Model\Entity\Piece[] $pieces
 */
class ArtStack extends Entity {
    
    use StackTrait;
    
    public $artwork;
    public $editions;
    public $formats;
    public $pieces;

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array
     */
    protected $_accessible = [
        'artwork' => true,
        'editions' => true,
        'formats' => true,
        'pieces' => true,
        'dispositionsPieces' => true,
//        '*' => true,
    ];
    
//    public function __get($property) {
//        if(in_array($property, array_keys($this->_accessible))) {
//            osd($property);
//            return $this->$property;
//        }
//        parent::__get($property);
//    }
    
    public function artwork() {
        return $this->artwork;
    }
    public function editions() {
        return $this->editions;
    }
    public function formats() {
        return $this->formats;
    }
    public function pieces() {
        return $this->pieces;
    }
    public function dispositionsPieces() {
        return $this->dispositionsPieces;
    }
//    public function set($property, $value = NULL, array $options = []) {
//        $this->$property = new Layer($value);
//    }
        
}
