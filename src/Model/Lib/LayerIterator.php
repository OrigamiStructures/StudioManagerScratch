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
class LayerIterator implements LayerAccessInterface, LayerTaskInterface
{

    protected $layerName;
    /**
     * @var LayerAccessArgs
     */
    protected $AccessArgs = null;

    /**
     * All the entities to operate on
     *
     * @var \AppendIterator
     */
    protected $AppendIterator;

    protected $FilterIterator;

    protected $ResultArray;

    public function __construct($layerName)
    {
        $this->AppendIterator = new \AppendIterator();
        $this->layerName = $layerName;
    }

    public function getAppendIterator() {
        return $this->AppendIterator;
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
     * @return LayerIterator
     */
    public function insert($data)
    {
        if (is_array($data)){
            $result = new \ArrayIterator($data);
        } elseif (is_a($data, '\App\Model\Lib\Layer')) {
            $result = new \ArrayIterator($data->load());
        } elseif (is_a($data, '\Iterator'))  {
            $result = $data;
        } else {
            $result = new \ArrayIterator([$data]);
        }
        $this->AppendIterator->append($result);
        return $this;
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
    public function toValueList($valueSource = null)
    {
        // TODO: Implement toValueList($valueSource = null) method.
    }

    /**
     * Get a key => value list
     *
     * @param $keySource string|ValueSource
     * @param $valueSource string|ValueSource
     * @return array
     */
    public function toKeyValueList($keySource = null, $valueSource = null)
    {
        // TODO: Implement toKeyValueList($keySource = null, $valueSource = null) method.
    }

    /**
     * Get a list of distinct values
     *
     * @param $valueSource string|ValueSource
     * @return array
     */
    public function toDistinctList($valueSource = null)
    {
        // TODO: Implement toDistinctList($valueSource = null) method.
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
        $this->AccessArgs = $this->AccessArgs ?? new LayerAccessArgs();
        $this->AccessArgs->setLayer($this->layerName);
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
        $this->AccessArgs->setLayer($this->layerName);

        if($this->AccessArgs->hasFilter()) {
            $this->ResultArray = $this->performFilter();
        }

        if($this->AccessArgs->hasSort()) {
            $this->ResultArray = $this->performSort();
        }

        if($this->AccessArgs->hasPagination()) {
            $this->ResultArray = $this->performPagination();
        }

        if(!isset($this->ResultArray)) {
            $this->ResultArray = $this->AppendIterator;
        }

        return $this->ResultArray;

    }

    /**
     * Unedited code from Layer
     * @return array
     */
    protected function performFilter()
    {
        $argObj = $this->AccessArgs;
        $comparison = $argObj->selectComparison($argObj->valueOf('filterOperator'));

        $set = collection($this->AppendIterator);
        $results = $set->filter(function ($entity, $key) use ($argObj, $comparison) {
            $actual = $argObj->accessNodeObject('filter')->value($entity);
            return $comparison($actual, $argObj->valueOf('filterValue'));
        })->toArray();
        return $results;
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
        unset($this->ResultArray);
    }

    public function clearAccessArgs()
    {
        $this->AccessArgs = null;
    }

    /**
     * Get a copy of the Access instructions (with no included data)
     *
     * @return LayerAccessArgs
     */
    public function copyArgObj()
    {
        $obj = clone $this->AccessArgs ?? new LayerAccessArgs();
        $obj->resetData();
        return $obj;

    }
}
