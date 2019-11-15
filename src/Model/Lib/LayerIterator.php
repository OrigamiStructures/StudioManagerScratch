<?php


namespace App\Model\Lib;

use App\Interfaces\Layer;
use App\Interfaces\LayerAccessInterface;
use App\Interfaces\LayerStructureInterface;
use App\Interfaces\LayerTaskInterface;

/**
 * LayerIterator
 *
 * @package App\Model\Lib
 */
class LayerIterator extends \AppendIterator implements LayerAccessInterface, LayerTaskInterface
{

    /**
     * @var LayerAccessArgs
     */
    protected $AccessArgs;

    /**
     * All the entities to operate on
     *
     * @var \AppendIterator
     */
    protected $AppendIterator;

    protected $FilterIterator;

    protected $ResultIterator;

    public function __construct()
    {
        $this->AppendIterator = new \AppendIterator();
    }

    /**
     * Make the class tollerant of the input
     *
     * This will take any form of entity data we might have from our system;
     *  - array of entities
     *  - a Layer object
     *  - a bare entity
     *  - an Iterator
     *
     * @todo Maybe we should loop through the produced iterator to verify
     *      it contains entities and that all the entities appended are
     *      of the same type?
     *
     * @todo Code smell? Should this be input tollerant like this?
     *
     * @param mixed $data
     */
    public function insert($data)
    {
        switch ($data) {
            case is_array($data):
                $result = new \ArrayIterator($data);
                break;
            case is_a($data, '\APP\MODEL\LIB\Layer'):
                $result = new \ArrayIterator($data->load());
                break;
            case is_a($data, '\Iterator');
                $result = $data;
                break;
            default:
                $result = new \ArrayIterator([$data]);
        }
        parent::append($result);
    }

    //<editor-fold desc="****************** LAYER ACCESS INTERFACE *******************">
    /**
     * Get the result as an array of entities
     *
     * @return array
     */
    public function toArray()
    {
        // TODO: Implement toArray() method.
    }

    /**
     * Get the result as Layer object
     *
     * @return Layer
     */
    public function toLayer()
    {
        // TODO: Implement toLayer() method.
    }

    /**
     * Get an array of values
     *
     * @param $valueSource string|ValueSource
     * @return array
     */
    public function toValueList($valueSource)
    {
        // TODO: Implement toValueList() method.
    }

    /**
     * Get a key => value list
     *
     * @param $keySource string|ValueSource
     * @param $valueSource string|ValueSource
     * @return array
     */
    public function toKeyValueList($keySource, $valueSource)
    {
        // TODO: Implement toKeyValueList() method.
    }

    /**
     * Get a list of distinct values
     *
     * @param $valueSource string|ValueSource
     * @return array
     */
    public function toDistinctList($valueSource)
    {
        // TODO: Implement toDistinctList() method.
    }

    /**
     * Get the stored registry instance
     *
     * @return ValueSourceRegistry
     */
    public function getValueRegistry()
    {
        // TODO: Implement getValueRegistry() method.
    }
    //</editor-fold>

    /**
     * Initiate a fluent Access definition
     *
     * @return LayerAccessArgs
     * @todo This name has a collision. It will be changed later
     */
    public function NEWfind()
    {
        $this->AccessArgs = new LayerAccessArgs();
        return $this->AccessArgs;
    }

    /**
     * Run the Access process and return an iterator containing the result
     *
     * @TODO storing each process result separately could function as a
     *      cache system. If we needed multiple related results from one
     *      pool, we could make an identity test for each step and only
     *      do them when they change. This would allow a filtered, sorted
     *      set that we could pull sequential pages from with out
     *      refiltering/resorting. For now, this need seems farfetched.
     *
     * @param $argObj LayerAccessArgs
     * @return LayerIterator
     */
    public function perform($argObj)
    {
        $this->setAccessArgs($argObj);

        if($this->AccessArgs->hasFilter()) {
            $this->ResultIterator = $this->performFilter();
        }

        if($this->AccessArgs->hasSort()) {
            $this->ResultIterator = $this->performSort();
        }

        if($this->AccessArgs->hasPagination()) {
            $this->ResultIterator = $this->performPagination();
        }

        if(!isset($this->ResultIterator)) {
            $this->ResultIterator = $this->AppendIterator;
        }

    }

    protected function performFilter()
    {

    }

    protected function performSort()
    {

    }

    protected function performPagination()
    {

    }

    /**
     * Store an the Access process instructions
     *
     * @param $argObj LayerAccessArgs
     * @return bool
     */
    public function setAccessArgs($argObj)
    {
        $this->AccessArgs = $argObj;
        unset($this->ResultIterator);
    }

    /**
     * Get a copy of the Access instructions (with no included data)
     *
     * @return LayerAccessArgs
     */
    public function copyArgObj()
    {
        $obj = clone $this->AccessArgs;
        $obj->resetData();
        return $obj;

    }
}
