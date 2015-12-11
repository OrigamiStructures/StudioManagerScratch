<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use App\Model\Table\EditionsTable;
use App\Model\Table\FormatsTable;
use App\Model\Table\PiecesTable;
use App\Model\Table\SubscriptionsTable;
use App\Model\Table\SeriesTable;
use Cake\ORM\TableRegistry;

/**
 * CakePHP ArtworkStackComponent
 * @author dondrake
 */
class ArtworkStackComponent extends Component {
		
    public function initialize(array $config) 
	{
		$this->controller = $this->_registry->getController();
		$this->Artworks = TableRegistry::get('Artworks');
		$this->Editions = TableRegistry::get('Editions');
		$this->Formats = TableRegistry::get('Formats');
		$this->Pieces = TableRegistry::get('Pieces');
		$this->Series = TableRegistry::get('Series');
		$this->Subscriptions = TableRegistry::get('Subscriptions');
	}
	
	/**
	 * 
	 * @return array
	 */
	public function layerChoices() {
		$artist_id = $this->controller->artistId();
		$artworks = $this->Artworks->find('choiceList', ['artist_id' => $artist_id])->toArray();
		$editions = $this->Editions->find('choiceList', ['artist_id' => $artist_id])->toArray();
		$formats = $this->Formats->find('choiceList', ['artist_id' => $artist_id])->toArray();
		$series = $this->Series->find('choiceList', ['artist_id' => $artist_id])->toArray();
		$subscriptions = $this->Subscriptions->find('choiceList', ['artist_id' => $artist_id])->toArray();
		$this->controller->set(compact('artworks', 'editions', 'formats', 'series', 'subscriptions'));
		return [$artworks, $editions, $formats, $series, $subscriptions];
	}
}
