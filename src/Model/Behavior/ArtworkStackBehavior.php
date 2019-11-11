<?php

/*
 * Copyright 2015 Origami Structures
 */

namespace App\Model\Behavior;

use Cake\ORM\Behavior;
use Cake\ORM\Entity;
use App\Model\Table\ArtworksTable;
use App\Model\Table\EditionsTable;
use App\Model\Table\FormatsTable;
use App\Model\Table\PiecesTable;
use App\Model\Entity\Artwork;
use App\Model\Entity\Edition;
use App\Model\Entity\Format;
use App\Model\Entity\Piece;
use Cake\Utility\Inflector;
use Cake\Collection\Collection;
use Cake\ORM\TableRegistry;
use Cake\Event\Event;
use ArrayObject;

/**
 * ArtworkStackBehavior
 *
 * This class is not operational. It has been stripped of all old code of
 * questionable quality (mostly stuff that changed array structures).
 *
 * The code preserved stands as an example for future development.
 *
 * @author dondrake
 */
class ArtworkStackBehavior extends Behavior {


	/**
	 * Callable: Logic for creation of pieces for new Editions
	 *
	 * Direct creation or refinment of a Format for an existing Edition
	 * does not require Piece creation. Those calls are bounced. Later
	 * a better, more comprehensive Piece handling plan will be required.
	 *
	 * @param array $edition
	 * @return array
	 */
	public function createPieces($edition) {
		if ($this->_table->SystemState->controller() !== 'formats') {
			$this->Pieces = TableRegistry::getTableLocator()->get('Pieces');

			// THIS COULD MOVE TO PIECES TABLE

			switch ($edition['type']) {
				case EDITION_LIMITED:
				case PORTFOLIO_LIMITED:
				case PUBLICATION_LIMITED:
					$edition['pieces'] = $this->Pieces->spawn(
					    NUMBERED_PIECES, $edition['quantity']
                    );
					break;
				case EDITION_OPEN:
				case PORTFOLIO_OPEN:
				case PUBLICATION_OPEN:
					$edition['pieces'] = $this->Pieces->spawn(
					    OPEN_PIECES, 1, ['quantity' => $edition['quantity']]
                    );
					break;
				case EDITION_UNIQUE:
				case EDITION_RIGHTS:
					$edition['quantity'] = 1;
					$edition['pieces'] = $this->Pieces->spawn(OPEN_PIECES, 1);
					break;
			}
		}
		return $edition;
	}

}
