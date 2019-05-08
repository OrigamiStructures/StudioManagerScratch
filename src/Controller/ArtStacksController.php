<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Cache\Cache;
use App\Model\Lib\Layer;
use App\Model\Table\ArtworksTable as ArtStacks;

/**
 * ArtStacks Controller
 *
 * @property \App\Model\Table\ArtStacksTable $ArtStacks
 *
 * @method \App\Model\Entity\ArtStack[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ArtStacksController extends AppController
{
    
    public function initialize() {
        parent::initialize();
        $this->ArtStacks = $this->getTableLocator()->get('ArtStacks');
    }
    
    public function testMe() {
        $editions = $this->ArtStacks->layer('Editions')->find('all')->toArray();
        $artworks = $this->ArtStacks->layer('Artworks')->find('all')->toArray();
        $pieceSet = $this->ArtStacks->layer('Pieces')->find()->where(['edition_id' => 35]) ->toArray();
        
        $layer = new Layer($pieceSet);
        osd($layer->trait_distinct($piecesSet, 'edition_id'));
    }

}
