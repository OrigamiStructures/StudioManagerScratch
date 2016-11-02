<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;
use App\Model\Entity\Traits\ParentEntityTrait;
use Cake\Utility\Text;
use App\Model\Entity\Traits\AssignmentTrait;
use App\Model\Entity\Traits\DispositionTrait;

/**
 * Format Entity.
 *
 * @property int $id
 * @property \Cake\I18n\Time $created
 * @property \Cake\I18n\Time $modified
 * @property int $user_id
 * @property \App\Model\Entity\User $user
 * @property string $title
 * @property string $description
 * @property int $range_flag
 * @property int $range_start
 * @property int $range_end
 * @property int $image_id
 * @property int $edition_id
 * @property \App\Model\Entity\Edition $edition
 * @property int $subscription_id
 * @property \App\Model\Entity\Piece[] $pieces
 */
class Format extends Entity
{
	
	use ParentEntityTrait;
	use AssignmentTrait;
	use DispositionTrait;

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
	protected $_salable;

	/**
	 * Fully detailed descriptive label for the edition
	 * 
	 * @return string
	 */
	public function _getDisplayTitle() {
		$title = empty($this->title) ? 'Format: ' . $this->_gutDescription() : $this->title;
		return $title;
	}
	
	/**
	 * provide a key that relates Pieces back to their Format or Edition
	 * 
	 * @return string
	 */
	public function key() {
		return $this->_key([$this->edition_id, $this->id]);
	}


	/**
	 * Does this format have any pieces assigned to it
	 * 
	 * assigned_piece_count is a CounterCache value
	 * 
	 * @return boolean
	 */
	public function hasAssigned() {
		return $this->_properties['assigned_piece_count'] > 0;
	}
	
	/**
	 * Are there any pieces assigned that have a disposition catategorized as 'collected'
	 * 
	 * collected_piece_count is a CounterCache value
	 * 
	 * @return boolean
	 */
	public function hasCollected() {
		return $this->_properties['collected_piece_count'] > 0;
	}
	
	/**
	 * Are there pieces available for sale as part of this format
	 * 
	 * @param integer $undisposed All undisposed pieces on the edition
	 * @return boolean
	 */
	public function hasSalable($undisposed) {
		return $this->salable_piece_count($undisposed) > 0;
	}
	
	/**
	 * The count of pieces possibly available for sale as this format
	 * 
	 * edition->undispose + format->disposed_but_not_collected
	 * 
	 * The edition's undisposed pieces represent all pieces that aren't locked 
	 * to a format (even if they are assigned to a format) and so, could be 
	 * assigned to this format and sold. The pieces that are disposed in 
	 * this format but that aren't already collected are also salable.
	 * 
	 * @param integer $undisposed All undisposed pieces on the edition
	 * @return integer
	 */
	public function salable_piece_count($undisposed) {
		if (!isset($this->_salable)) {
			$this->_salable = $this->disposed_piece_count - 
					$this->collected_piece_count + 
					$undisposed;
		}
		return $this->_salable;
	}

	/**
	 * Does this format have assigned pieces that are not disposed?
	 * 
	 * fluid_piece_count is a CounterCache value
	 * 
	 * @return boolean
	 */
	public function hasFluid() {
		return $this->fluid_piece_count > 0;
	}
	
	/**
	 * Does this format have disposed pieces?
	 * 
	 * @return boolean
	 */
	public function hasDisposed() {
		return $this->assigned_piece_count - $this->fluid_piece_count;
	}
	
	/**
	 * Count of disposed pieces on this format
	 * 
	 * @return integer
	 */
	public function _getDisposedPieceCount() {
		return $this->_properties['assigned_piece_count'] - $this->_properties['fluid_piece_count'];
	}
	
	/**
	 * Remove the middle from a long description, so it can serve as a title/label
	 * 
	 * @return string
	 */
	protected function _gutDescription() {
//		$display_value = $this->description;
//		if (strlen($this->description) > 33) {
//			$lead = Text::truncate($this->description, 15, ['ellipsis' => ' ... ']);
//			$tail = Text::tail($this->description, 10, ['ellipsis' => '', 'exact' => FALSE]);
//			
//			$display_value = "$lead$tail";
//		}
//		return $display_value;
		return Text::truncate($this->description, 10);
	}
	
	/**
	 * From an inverted artwork stack, identify the tip-of-the-iceberg
	 * 
	 * A normal artwork stack begins with the Artwork and sees all the children. 
	 * An inverted stack, such as that linked to a disposition, starts art the 
	 * child and contains the specific entity chain up to the Artwork. This method 
	 * creates the unique path/name label for this format or adds to the label 
	 * for the child piece
	 * 
	 * @return string
	 */
	public function identityLabel() {
		$label = $this->display_title;
		if (is_object($this->edition)) {
			$label = "{$this->edition->identityLabel()}, $label";
		}
		return $label;
	}
	
	/**
	 * Return url query breadbrumbs to this Format
	 * 
	 * Can be used to construct a url like:
	 * :controller/:action?artwork=xx&edition=yy&format=zz
	 * 
	 * @return array
	 */
	public function identityArguments() {
		$args = ['format' => $this->id];
		if (is_object($this->edition)) {
			$args = $this->edition->identityArguments() + $args;
		}
		return $args;
	}
	
}
