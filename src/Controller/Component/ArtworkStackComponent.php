<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use App\Model\Table\EditionsTable;
use App\Model\Table\FormatsTable;
use App\Model\Table\PiecesTable;
use App\Model\Table\SubscriptionsTable;
use App\Model\Table\SeriesTable;
use Cake\Collection\Collection;
use Cake\ORM\TableRegistry;
use Cake\Cache\Cache;
use App\Model\Lib\Providers;
//use Cake\Controller\Component\PaginatorComponent;

/**
 * ArtworkStackComponent provides a unified interface for the three layers, Artwork, Edition, and Format
 *
 * This class has been gutted. Code deemed 'poor quality' that doesn't contain
 * useful business logic has been dumped. What remains is for reference only.
 *
 * The creation and refinement of Artworks is a process that may effect 1, 2 or 3
 * the layers. So, depending on context, we may be in any of 3 controllers.
 * This component provides the basic services that allow them all to behave
 * in the same way. Queries are always performed from the top of the stack
 * even if we are working on a Format. Saves are always done from the top also.
 * All the views and elements are designed to cascade through all the layers.
 *
 * Refinement of editions that involves quantity changes trigger the application
 * of a special rule set to manage piece records. This task is passed off to
 * a separate component.
 *
 * @todo Exceptions in this or calling code should clear the art stack cache, probably
 *			a special Exception class should be written that takes care of the cache.
 * @todo I'm not removing SystemState because this Component will be trashed. But
 *      I'm leaving it for now for reference. There are probably some methods that
 *      point to forgotten concepts that shouldn't be lost.
 *
 * @author dondrake
 */
class ArtworkStackComponent extends Component {

	public $components = ['Paginator'];

	public $full_containment = [
		'Users', 'Images', /*'Editions.Users',*/ 'Editions' => [
			'Series', 'Pieces' => ['Dispositions'], /*'Formats.Users',*/ 'Formats' => [
				'Images', 'Pieces' => ['Dispositions'], /*'Subscriptions'*/
				]
			]
		];

	/**
	 * Wrap both refinement save and deletions in a single transaction
	 *
	 * Creation is a simple Table->save() but refinement may involve deletion
	 * of piece records. This method provides refinement for all layers of the stack.
	 *
	 * @param Entity $artwork
	 * @param array $deletions
	 * @return boolean
	 */
	public function refinementTransaction($artwork, $deletions) {
		$ArtworkTable = TableRegistry::getTableLocator()->get('Artworks');
		Cache::delete("get_default_artworks[_{$artwork->id}_]", 'artwork');//die;
//		osd($artwork);die;
		$result = $ArtworkTable->getConnection()->transactional(function () use ($ArtworkTable, $artwork, $deletions) {
			$result = $ArtworkTable->save($artwork, ['atomic' => false]);
			if (is_array($deletions)) {
				foreach ($deletions as $piece) {
					$result = $result && $ArtworkTable->Editions->Pieces->delete($piece, ['atomic' => false]);
				}
			}
			return $result;
		});
		return $result;
	}

	/**
	 * Call from anywhere in the ArtworkStack to get the proper result
	 *
	 * @return Entity
	 */

	public function allocatePieces($artwork) {
		$this->PieceAllocation = $this->controller->loadComponent('PieceAllocation', ['artwork' => $artwork]);
		$this->PieceAllocation->allocate();
	}

	/**
	 * Check and handle edition->quantity change during refine() requests
	 *
	 * Both artworks and editions refine() methods may see changes to
	 * the edition size. The is the method that detects if quantity
	 * was edited. All edition types pass through this check. The
	 * handling will be parsed out to specialized code in the
	 * PieceAllocationComponent if there was an edit of this value.
	 *
	 * @param entity $artwork The full artwork stack
	 * @param integer $edition_id ID of the edition that was up for editing
	 */
	public function refinePieces($artwork, $edition_id) {
		$edition = $artwork->returnEdition($edition_id);
		$quantity_tuple = !$edition->dirty('quantity') ?
				FALSE :
				[
					'original' => $edition->getOriginal('quantity'),
					'refinement' => $edition->quantity,
					'id' => $edition->id,
				];
		if ($quantity_tuple) {
			$this->PieceAllocation = $this->controller->loadComponent('PieceAllocation', ['artwork' => $artwork]);
			return $this->PieceAllocation->refine($quantity_tuple); // return [deletions required]
		}
//		osd($quantity_tuple, 'after call');//die;
		return []; // deletions required
	}


	/**
	 * Use URL query arguments to filter the Entity
	 *
	 * 'review' views target specifics memebers of the an Artwork stack. The
	 * URL arguments indicate which Edition and possibly which Format the
	 * artist wants to see. The query gets everything because that is also the
	 * source of data for the menus. This process reduces the Entity stack
	 * so the view will only have the required information.
	 *
	 * @param Entity $artwork
	 * @return Entity
	 */
	protected function filterEntities($artwork) {
		if ($this->SystemState->urlArgIsKnown('edition')) {
			$edition_id = $this->SystemState->queryArg('edition');
			$format_id = $this->SystemState->urlArgIsKnown('format') ? $this->SystemState->queryArg('format') : FALSE;
			$editions = new Collection($artwork->editions);

			$edition_result = $editions->filter(function($edition) use ($edition_id, $format_id) {
				if ($edition->id == $edition_id) {
					if ($format_id) {
						$formats = new Collection($edition->formats);

						$format_result = $formats->filter(function($format) use ($format_id) {
							return $format->id == $format_id;
						});
						$edition->formats = $format_result->toArray();
					}
					return TRUE;
				}
				return FALSE;
			});
			$artwork->editions = $edition_result->toArray();
		}
		return $artwork;
	}

}
