<?php
namespace App\Model\Entity\Traits;

use Cake\Utility\Inflector;
use App\Lib\Range;
use Cake\Collection\Collection;

/**
 * DispositionTrait provides tools to manage partially and fully secified disposition nodes
 *
 * Pieces are one of the target nodes for a disposition.
 * Members and the Disposition itself are the others.
 *
 * Piece nodes may be specified down to the piece or they may only be identified
 * to the Format layer with the final piece selection pending. In either case the
 * query is done from the most specific layer up to the most general. An identity
 * label must be constrcuted and a quick way to tell if the specification is complete
 * must be provided.
 *
 * @author dondrake
 */
trait DispositionTrait {

	public function fullyIdentified() {
		return (get_class($this) === 'App\Model\Entity\Piece');
	}

}
