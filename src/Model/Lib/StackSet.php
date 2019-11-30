<?php
namespace App\Model\Lib;

use App\Interfaces\LayerStructureInterface;
use App\Model\Entity\StackEntity;
use App\Interfaces\xxxLayerAccessInterface;
//use App\Model\Traits\LayerAccessTrait;
use App\Model\Lib\StackSetAccessArgs;
use App\Model\Traits\LayerElementAccessTrait;
use Cake\Core\ConventionsTrait;
use Cake\Error\Debugger;
use Cake\Utility\Text;

/**
 * StackSet
 *
 * This is a collector class which holds sets of Entities that extend StackEntity
 *
 * This class provides access to the stored entities and their data
 * to make it easier to pull out stacks, layers, and merged collections of
 * entities from multiple stack.
 *
 * @author dondrake
 */
class StackSet implements LayerStructureInterface, \Countable {

	use LayerElementAccessTrait;
	use ConventionsTrait;

	protected $_data = [];

	protected $_stackName;

    //<editor-fold desc="LayerStructureInterface Realization">
    /**
     * Gather the available data at this level and package the iterator
     *
     * @param $name string
     * @return LayerAccessProcessor
     */
    public function getLayer($name)
    {
        $stacks = $this->getData();
        $Product = new LayerAccessProcessor($name);
        foreach ($stacks as $stack) {
            if (is_a($stack->$name, '\App\Model\Lib\Layer')) {
                $result = $stack->$name;
            } else {
                $result = [];
            }
            $Product->insert($result);
        }
        return $Product;
    }

    /**
     * Get an new LayerAccessArgs instance
     * @return LayerAccessArgs
     */
    public function getArgObj()
    {
        return new LayerAccessArgs();
    }
    //</editor-fold>

    //<editor-fold desc="LayerElementAccessTrait abstract completion">
    public function getData()
    {
        return $this->_data;
    }

    /**
     * Get all the ids accross all the stored StackEntities or the Layer entities
     *
     * This is a collection-level method that matches the StackEntity's and Layer's
     * IDs() methods. These form a pass-through chain.
     *
     * Calling IDs() from this level will insure unique results if
     * Layer IDs are pulled.
     *
     * StackEntity IDs will be from the primary entity propery and will
     * be unique becuase the set structure insures it.
     *
     * @param string $layer
     * @return array
     */
    public function IDs($layer = null) {
        if(is_null($layer)){
            return array_keys($this->getData());
        }
        $ids = $this->getLayer($layer)
            ->toDistinctList('id');

        return $ids;
    }

    //</editor-fold>

    //<editor-fold desc="Public Associated Data Features">
    public function linkedTo($foreign, $foreign_id, $linked = null) {
        $accessProcessor = $this->getLayer($linked);
        $foreign_key = $this->_modelKey($foreign);
        return $accessProcessor
            ->find()
            ->specifyFilter($foreign_key, $foreign_id);
    }

    /**
	 * Return all StackEntities that contain a layer entity with id = $id
     *
     * @todo This method seems confusing. Is it necessary?
	 *
	 * @param string $layer
	 * @param string $id
	 * @return array
	 */
	public function ownerOf($layer, $id) {
		$stacks = [];
		foreach ($this->_data as $stack) {
			if ($stack->exists($layer, $id)) {
				$stacks[] = $stack;
			}
		}
		return $stacks;
	}

    /**
     * Get all StackEntities containing any of the layer elements in the set
     *
     * @param $layer string The layer to search in
     * @param $ids array The ids to search for
     */
    public function stacksContaining($layer, $ids)
    {
        $stacks = [];
        foreach ($this->getData() as $stack) {
            //get the ids of the layer members in this stackentity
            //and intersect with the found set
            $intersection = array_intersect($stack->$layer->IDs(), $ids);
            if (count($intersection) > 0) {
                //if there was some overlap, save this stack for return.
                $stacks[$stack->rootID()] = $stack;
            }
        }
        return $stacks;
    }

    //</editor-fold>

    /**
     * Add another entity to the StackSet
     *
     * @param string $id
     * @param StackEntity $stack
     */
    public function insert($id, $stack) {
        $this->_data[$id] = $stack;
        if (!isset($this->_stackName)) {
            $this->_stackName = $stack->rootLayerName();
        }
    }

    public function __debugInfo()
    {
        return [
            '[_data]' => 'Contains ' . count($this->_data) . ' elements, '
                . Text::toList($this->IDs()),
            '[$stackName]' => $this->_stackName
        ];
    }

}
