<?php
namespace App\Model\Table;

use App\Model\Table\ArtworksTable;
use Cake\ORM\TableRegistry;


/**
 * CakePHP ArtStackTable
 * @author dondrake
 */
class ArtStacksTableHold extends ArtworksTable {
    
    protected $_Editions;
    protected $_Formats;
    protected $_Pieces;

    public function initialize(array $config){
        parent::initialize($config);
        $this->_initializeTables();
    }
    
    protected function _initializeTables(){
        $this->setTable('artworks');
        $this->_Editions = TableRegistry::getTableLocator()->get('Editions');
        $this->_Formats = TableRegistry::getTableLocator()->get('Formats');
        $this->_Pieces = TableRegistry::getTableLocator()->get('Pieces');
    }
    
    public function stackMember($alias) {
        if ($alias === 'Artworks') {
            return $this;
        }
        return $this->{"_$alias"};
    }
}
