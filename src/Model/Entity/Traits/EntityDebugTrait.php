<?php
namespace App\Model\Entity\Traits;

use App\Exception\BadClassConfigurationException;

/**
 * EntityDebugTrait
 *
 * @author dondrake
 */
trait EntityDebugTrait {
	
	protected $_upStreamLinkedObject = [];
	
	public function __construct(array $properties = [], array $options = []) {
		parent::__construct();
		$this->_upStreamLinkedObject[] = get_class($this);
	}

	public function simplifyTime() {
//		foreach 
	}
	/**
	 * 
	 * @param type $time
	 * @return type
	 */
	public function debugTime($time) {
		return '[Time object] => ' . $time->nice();
	}
		
	function __clone() {
		
		foreach ($this as $property => $value) {
			switch (gettype($value)) {
				case 'object':
					if ($property === '_joinData') {
						$this->$property = $value;
					} else {
						$this->$property = $this->_suppressRecursion($value);
					}
//					echo "<p>See object $property (cloning)</p>";
//		echo '<p>' . get_class($value) . ' is instance of \Cake\ORM\Entity: ' .
//				($value instanceof \Cake\ORM\Entity ? 'TRUE' : 'FALSE') . '</p>';
					break;
				case 'array':
//					echo "<p>See array $property (cloning)</p>";
					$this->$property = $this->_scanArray($value);
				default:
//					echo "<p>See value for $property (cloning)</p>";
					break;
			}
		}
		
	}
	
	private function _scanArray($source) {
		foreach ($source as $key => $value) {
			switch (gettype($value)) {
				case 'object':
//					echo "<p>See object $key (array scan)</p>";
//		echo '<p>' . get_class($value) . ' is instance of \Cake\ORM\Entity: ' .
//				($value instanceof \Cake\ORM\Entity ? 'TRUE' : 'FALSE') . '</p>';
					if ($key === '_joinData') {
						$source[$key] = $value;
					} else {
						$source[$key] = $this->_suppressRecursion($value);
					}
					
					break;
				case 'array':
//					echo "<p>See array $key (array scan)</p>";
					$source[$key] = $this->_scanArray($value);
					break;
//					echo "<p>See value for $key (array scan)</p>";
				default:
					break;
			}
		}
		return $source;
	}
	
	private function _suppressRecursion($object) {
		
//		echo '<p>' . get_class($object) . ' is instance of \Cake\ORM\Entity: ' .
//				($object instanceof \Cake\ORM\Entity ? 'TRUE' : 'FALSE') . '</p>';
		
		if($object instanceof \Cake\ORM\Entity) {
			if (in_array(get_class($object), $this->_upStreamLinkedObject)) {
				$object = 'POSSIBLE RECURSION OF ' . get_class($object) . '. ID = ' . 
					(isset($object->id) ? $object->id : 'unknown');
			} else {
//				echo '<p> register ' . get_class($this) . ' on ' . get_class($object);
				$object->registerUpStreamLinkedObject(get_class($this));
			}
		} elseif ($object instanceof \Cake\I18n\Time) {
			$object = $this->debugTime($object);
		}
//		$type = typeof($object);
//		echo "<p>Suppressed result is [$type]</p>";
		return $object;
	}
	
	public function registerUpStreamLinkedObject($class) {
		$this->_upStreamLinkedObject[] = $class;
	}
		
	public function __debugInfo() {
		if (!isset($this->_links)) {
			throw new BadClassConfigurationException('_link property required');
		}
		$clone = clone $this;
		$properties = $clone->_properties;
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
