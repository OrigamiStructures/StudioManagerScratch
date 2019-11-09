<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\ORM\TableRegistry;
use Cake\Collection\Collection;
use App\Model\Entity\Piece;
use CakeDC\Users\Exception\BadConfigurationException;

/**
 * PieceAllocation Handles allocation/deletion of pieces during create/refine
 *
 * The rules for piece management are too complex to include in the
 * Artwork Stack Component which manages Artwork CRUD tasks. So they've
 * all been delegated to this class.
 *
 * Allocation of new pieces and deletion of existing pieces are processes that
 * are trigged by change to Editions->quantity. Both piece increase and decrease
 * are performed according to set rules and those rules are defined in this class.
 *
 * Piece assignment and reassignment are user directed processes that
 * move pieces between an Edition layer and its Format layer(s).
 * That process is handled in the PieceAssignmentComponent.
 *
 * @todo Make this handle custom numbering schemes (see notes in Range)
 * @todo Make this handle user-defined finite numbering schemes (see notes in Range)
 *
 * @author dondrake
 */
class PieceAllocationComponent extends Component {

	protected $controller;
	protected $stack;
	protected $artwork;
	protected $Artworks;
	private $edition;
	private $edition_index;
	private $format;
	private $format_index;
	private $multiple_formats;
	private $pieces;

	/**
	 * Build the required component internals
	 *
	 *
	 * @param array $config
	 * @throws Exception Requires and Artwork Entity
	 */
	public function initialize(array $config = array()) {
		if (!isset($config['artwork'])) {
			throw new Exception('An Artwork Entity stack must be provided to the Piece Allocation Component');
		}
		parent::initialize($config);
		$this->controller = $this->_registry->getController();
		$this->artwork = $config['artwork'];
	}

	/**
	 * @todo Flagged as a serious issue:
	 *		 https://github.com/OrigamiStructures/StudioManagerScratch/issues/41
     * @todo addition of variable $create is a hack/apprimation of the old
     *      SystemState/StateMap check. It can't be trusted in production
     *      without verification and testing
	 */
	public function allocate() {
		$index = array_keys($this->artwork->editions)[0];
		$this->edition = $this->artwork->editions[$index];
//		osd($this->artwork->multiple);die;
//		if (isset($this->artwork->multiple)) {
//			$this->multiple_formats = (boolean) $this->artwork->multiple; // an input value from creation forms
//		}
		unset($this->pieces);

		$create = stristr($this->getController()->request->getParam('action'), 'create');
		if ($create && ($this->onePiece() || !$this->multiple_formats)) {
			$this->piecesToFormat();
		}
	}

	/**
	 * Move the single piece onto the last edited format
	 *
	 * THIS SEEMS A BIT QUESTIONABLE. ONE PIECE? FORMAT ASSUMPTION...
	 *
	 * @param Entity $piece
	 * @return Entity
	 */
	protected function piecesToFormat($piece = NULL) {
		$edition = $this->artwork->editions[0];
		$format = $edition->formats[0];
		$format->pieces = $edition->pieces;
		unset($edition->pieces);
	}

	/**
	 * Does the edition have only one piece?
	 *
	 * @return boolean
	 */
	protected function onePiece() {
		return $this->edition->quantity === 1;
	}

	/**
	 * Handle edition size changes for editions that already have pieces
	 *
	 * There is a lot of initialization that has been done. Many properties set
	 *
	 * @param Entity $artwork The post-save entity
	 * @param array $quantity_tuple (int) $original, (int) $refinement, $id
	 */
	public function refine($data = []) {
		extract($data); // $original, $refinement, $id (edition_id)
		$this->edition = $this->artwork->returnEdition($id);
		$change = $refinement - $original; // decrease (-x), increase (+x)

		// Unique and Rights have ONE piece don't have the input for quanitity
		if ($this->edition->type === EDITION_OPEN) {
			$method = 'resizeOpenEdition';
		} else {
			$method = 'resizeLimitedEdition';
		}
		return $this->$method($original, $change); // return array of Pieces to delete
	}

	/**
	 * Change the size of an Open edition
	 *
	 * @param integer $original
	 * @param integer $change
	 */
	protected function resizeOpenEdition($original, $change) {
		// both increase and decrease will need to do queries
		$this->Pieces = TableRegistry::getTableLocator()->get('Pieces');
		$piece = $this->Pieces
				->find('unassigned', ['edition_id' => $this->edition->id])
				->toArray();

		if ($change > 0) {
			$this->increaseOpenEdition($change, $piece);
		} else {

			$editions = TableRegistry::getTableLocator()->get('Editions');
			$original_edition = $editions->get($this->edition->id,
				['contain' => ['Formats']]);

			if (abs($change) > $original_edition->undisposed_piece_count ) {
				$this->edition->errors('quantity', 'The quantity was set '
						. 'lower than the allowed minimum');
				return;
			}
			// return [] deletions required
			return $this->decreaseOpenEdition($change, $original_edition);
		}
	}

	/**
	 * Change the size of a limited edition
	 *
	 * @param integer $original
	 * @param signed-integer $change
	 * @return type
	 */
	protected function resizeLimitedEdition($original, $change) {
		if ($change > 0) {
			$this->increaseLimitedEdition($change);
		} else {

			$editions = TableRegistry::getTableLocator()->get('Editions');
			$original_edition = $editions->get($this->edition->id,
				['contain' => ['Formats']]);

			$pieces = TableRegistry::getTableLocator()->get('Pieces');
			$highestNumberDisposed = $pieces->highestNumberDisposed(
				['edition_id' => $this->edition->id]);
			$edition_tail = $original_edition->quantity - $highestNumberDisposed;

			if (abs($change) > $edition_tail ) {
				$this->edition->errors('quantity', 'The quantity was set '
						. 'lower than the allowed minimum');
				return;
			}
			// return [] deletions required
			return $this->decreaseLimitedEdition($change, $pieces, $original_edition);
		}
	}

	/**
	 * Add pieces to an existing Open edition type
	 *
	 * Open editions use the 'quantity' field of Piece entities, so if an
	 * open edition has unassigned pieces, there will be one record for them.
	 * Increasing the edition size requires changing the 'quantity' value
	 * in that record or creating an unassigned piece record with the quantity
	 *
	 * @param integer $change Number of additional unassigned pieces needed
	 * @param entity $piece Entity, the unassigned piece record
	 * @throws BadConfigurationException
	 */
	protected function increaseOpenEdition($change, $piece) {
		// hasUnassigned() can't work because the edition is in flux
		// and values aren't updated. Specifically, edition->quantity which
		// calculates unassigned is now out of phase with pieces (that's why we're here)
		$format_id = FALSE;
		$flat_edition = $this->edition->format_count === 1;

		if ($flat_edition) {
			$format_id = $this->edition->formats[0]->id;
			$piece = $this->Pieces->find('fluid',
				[
					'edition_id' => $this->edition->id,
				])->toArray();
		} else {
			$piece = $this->Pieces->find('unassigned',
				['edition_id' => $this->edition->id])->toArray();
		}

		if (count($piece) === 1) {
			$piece = $piece[0];
			$data = [
				'quantity' => $piece->quantity + $change,
				'format_id' => $flat_edition ? $format_id : NULL,
			];
			$this->edition->pieces = [$this->Pieces->patchEntity($piece, $data)];

		} elseif (empty($piece)) {
			$data = [
				'quantity' => $change,
				'format_id' => $flat_edition ? $format_id : NULL,
			];
			$this->edition->pieces = [new Piece(
				$this->Pieces->spawn(OPEN_PIECES, 1, $data)[0]
			)];

		} else {
			\Cake\Log\Log::emergency('More than one unassigned piece was '
					. 'found on an Open edition', $piece);
			throw new BadConfigurationException(
					"Open Edition types should not have more than one unassigned "
					. "piece record but edition {$this->edition->id} has more.");
		}

		return []; // deletions required
	}

	/**
	 *
	 * @param type $change
	 * @param type $piece
	 */
	protected function decreaseOpenEdition($change, $original_edition) {
		$change = abs($change);
		$pieces = $this->Pieces->find('undisposed',
			[
				'edition_id' => $this->edition->id,
			])->order(['format_id' => 'ASC'])->toArray();
//		osd($piesces);die;

		(new Collection($this->edition->formats))->each(function($format) {
			$format->pieces = NULL;
			$format->dirty('pieces', FALSE);
		}) ;
		$this->edition->pieces = $pieces;

		$index = 0;
		$limit = count($pieces);
		$deletions = [];
		do {
			$piece = $pieces[$index++];
			if ($piece->quantity > $change) {
				$piece->quantity -= $change;
				$change = 0;
			} else { // change >= quantity
				$change -= $piece->quantity;
				$piece->quantity = 0;
				$deletions[] = $piece;
			}

		} while ($change > 0 && $index < $limit);

		if ($change > 0) {
			throw new \Cake\Network\Exception\BadRequestException(
				'There were not enough undisposed Pieces to reduce the Edition the '
					. 'requested amount.');
		}

		return $deletions;
	}

	/**
	 * Increase the Limited Edition by some amount
	 *
	 * If there is only one format, put the new pieces directly on it.
	 * Otherwise put them on the edition.
	 *
	 * @param signed-int $change
	 */
	protected function increaseLimitedEdition($change) {

		$editions = TableRegistry::getTableLocator()->get('Editions');
		$original_edition = $editions->get($this->edition->id, ['contain' => ['Formats']]);
		$flat_edition = $this->edition->format_count === 1;
		$data = [
			'quantity' => 1,
			'edition_id' => $this->edition->id,
//			'format_id' => $flat_edition ? $format_id : NULL,
		];

		$this->Pieces = TableRegistry::getTableLocator()->get('Pieces');
		$new_pieces = $this->Pieces->spawn(NUMBERED_PIECES, $change, $data, $original_edition->quantity);
		$new_pieces = (new Collection($new_pieces))->map(function($piece) {
			return (new Piece($piece));
		});

		if ($flat_edition) {
			$this->edition->formats[0]->pieces = $new_pieces->toArray();
		} else {
			$this->edition->pieces = $new_pieces->toArray();
		}
	}

	/**
	 * Get the pieces that should be removed from the edition
	 *
	 * Numbered editions can only loose their tailing, undisposed pieces
	 * even if those pieces are assigned to a format.
	 *
	 * @param signed-int $change
	 * @param Table $pieces
	 * @param entity $original_edition
	 * @return array
	 */
	protected function decreaseLimitedEdition($change, $pieces, $original_edition) {
		$change = abs($change);
		$deletions = $pieces->find()->where(['edition_id' => $this->edition->id])
				->order(['number' => 'DESC'])
				->limit($change);
		return $deletions->toArray();
	}

}
