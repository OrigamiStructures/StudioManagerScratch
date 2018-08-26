<?php
namespace App\Model\Entity\Traits;

use App\Exception\BadClassConfigurationException;

/**
 * EntityDebugTrait
 * 
 * @todo The use of upStreamLinkedObject may not be the best solution
 * Another idea is to make an abstract method here that each entity must 
 * implement. That method would name the entities that link to this one 
 * and use that list to guide the children in what to watch out for. 
 *
 * @author dondrake
 */
trait EntityDebugTrait {
	
	public $upStreamLinkedObject = [];
	
	public function __construct(array $properties = [], array $options = []) {
		parent::__construct();
		$this->upStreamLinkedObject[] = get_class($this);
	}

	/**
	 * Replace Time objects with a concise, readable alternative
	 * 
	 * When debugging an Entity, the clone gets this for all Time objects
	 * 
	 * @param type $time
	 * @return type
	 */
	public function debugTime($time) {
		return '[Time object] => ' . $time->nice();
	}
	
	/**
	 * Manage modification of this cloned Entity
	 * 
	 * Cloning is triggered when the entity is being debugged (output). 
	 * The cloned entity can (and probably will) still retains references 
	 * to other (original) entities. These will get information about upstream 
	 * entities so, when their __debugInfo() gets called, and they get cloned, 
	 * they can watch for likely recursive references.
	 * 
	 * Also, Time objects will be replaced with a simpler string. 
	 */
	function __clone() {
		foreach ($this as $property => $value) {
			switch (gettype($value)) {
				case 'object':
					if ($property === '_joinData') {
						$this->$property = $value;
					} else {
						$this->$property = $this->_processObject($value);
					}
					break;
				case 'array':
					$this->$property = $this->_scanArray($value);
				default:
					break;
			}
		}
		
	}
	
	/**
	 * Continue the clone process into array properties
	 * 
	 * @param array $source
	 * @return array
	 */
	private function _scanArray($source) {
		foreach ($source as $key => $value) {
			switch (gettype($value)) {
				case 'object':
					if ($key === '_joinData') {
						$source[$key] = $value;
					} else {
						$source[$key] = $this->_processObject($value);
					}
					break;
				case 'array':
					$source[$key] = $this->_scanArray($value);
					break;
				default:
					break;
			}
		}
		return $source;
	}
	
	/**
	 * Deal with objects found in the clone being debugged
	 * 
	 * as of 8/2018 there are just 3 choices, 
	 *	eliminate the object with a 'recursion' message
	 *	change Time objects into simpler time strings
	 *	let the object remain
	 * 
	 * In the last case, if the object is another entity, we register 
	 * this class with it so it can watch for recursions
	 * 
	 * @param object $object
	 * @return mixed
	 */
	private function _processObject($object) {
		if($object instanceof \Cake\ORM\Entity) {
			if (in_array(get_class($object), $this->upStreamLinkedObject)) {
				$object = 'POSSIBLE RECURSION OF ' . get_class($object) . '. ID = ' . 
					(isset($object->id) ? $object->id : 'unknown');
			} else {
				// register this link as a possible recursion candidate 
				// in this discoved Entity. This property builds up and gets 
				// carried down the stack. 
				$object->upStreamLinkedObject[] = get_class($this);
			}
		} elseif ($object instanceof \Cake\I18n\Time) {
			$object = $this->debugTime($object);
		}
		return $object;
	}
	
	/**
	 * Manage debug output to limit run-away recursion 
	 * 
	 * Debugging Entities can be very messy when children contain references to 
	 * their parents. These recursive references can't be modified in the object 
	 * because the data is (presumably) required by the program. So, a clone is 
	 * made of the object. Then the clone is modified and used for the debug output. 
	 * 
	 * @return array
	 * @throws BadClassConfigurationException
	 */
	public function __debugInfo() {
		echo '<p>' . get_class($this) . ' ::</p><p>' . var_dump($this->upStreamLinkedObject) . '</p>';
		$clone = clone $this;
		$properties = $clone->_properties;
		echo '<p>' . get_class($this) . ' ::</p><p>' . var_dump($this->upStreamLinkedObject) . '</p>';
        return $properties + [
            '[new]' => $this->isNew(),
            '[accessible]' => array_filter($this->_accessible),
            '[dirty]' => $this->_dirty,
            '[original]' => $this->_original,
            '[virtual]' => $this->_virtual,
            '[errors]' => $this->_errors,
            '[repository]' => $this->_registryAlias
        ];
	}
}
