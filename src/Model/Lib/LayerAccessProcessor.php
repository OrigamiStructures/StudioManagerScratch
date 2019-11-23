<?php


namespace App\Model\Lib;

use App\Interfaces\Layer;
use App\Interfaces\LayerAccessInterface;
use App\Interfaces\LayerStructureInterface;
use App\Interfaces\LayerTaskInterface;
use Cake\Collection\Collection;

/**
 * LayerAccessProcessor
 *
 * @package App\Model\Lib
 */
class LayerAccessProcessor implements LayerAccessInterface, LayerTaskInterface
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

    protected $ResultArray;

    public function __construct($layerName)
    {
        $this->AppendIterator = new LayerAppendIterator();
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
     * @param mixed $data
     * @return LayerAccessProcessor
     *@todo Maybe we should loop through the produced iterator to verify
     *      it contains entities and that all the entities appended are
     *      of the same type?
     *
     * @todo Code smell? Should this be input tollerant like this?
     *
     */
    public function insert($data)
    {
        if (is_array($data)){
            $result = new \ArrayIterator($data);
        } elseif (is_a($data, '\App\Model\Lib\Layer')) {
            $result = new \ArrayIterator($data->toArray());
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
        $this->evaluate();
        return iterator_to_array($this->ResultArray);
    }

    public function rawCount()
    {
        return iterator_count($this->AppendIterator);
    }

    public function resultCount()
    {
        return iterator_count($this->ResultArray);
    }

    /**
     * Do final processing in for the various 'toXxxxx' methods
     *
     * The 5 'toXxxxx` methods return ResultArray. If it exists, it can be
     * trusted as current and valid for the existing AccessArgs (if present).
     *
     * This is because the three ways of resetting AccessArgs
     *      - calling $this->find()
     *      - calling $this->perform($argObj)
     *      - calling $this->setAccessArgs($argObj)
     * also unset ResultArray. And aquiring the AccessArgs to modify
     * their settings can only be done through a method that also
     * resets ResultArray.
     * @todo check for new args too
     */
    protected function evaluate()
    {
        $this->AccessArgs = $this->AccessArgs ?? new LayerAccessArgs();
        //This has to check for new Args too
        if(!isset($this->ResultArray)) {
            $this->ResultArray = $this->perform($this->AccessArgs);
        }
    }

    /**
     * Get the result as Layer object
     *
     * @return Layer
     */
    public function toLayer()
    {
        $result = $this->toArray();
        return layer($result);
    }

    /**
     * Get an array of values
     *
     * @param $valueSource string|ValueSource
     * @return array
     */
    public function toValueList($valueSource = null)
    {
        $this->evaluate();

        //this skips out if appenditerator is empty but hasn't been tested
        //and the need for this hasn't been verified
        $resultValueSource = FALSE;
        if (count($this->ResultArray) > 0) {
            $this->AccessArgs->setAccessNodeObject('resultValue', $valueSource);
            $resultValueSource = $this->AccessArgs->accessNodeObject('resultValue');
        }

        if ($resultValueSource) {

            $result = collection($this->ResultArray)
                ->reduce(function ($harvest, $entity) use ($resultValueSource){
                    if (!is_null($resultValueSource->value($entity))) {
                        array_push($harvest, $resultValueSource->value($entity));
                    }
                    return $harvest;
                }, []);
        } else {
            $result = [];
        }
        return $result;
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
        $this->evaluate();

        //this skips out if appenditerator is empty but hasn't been tested
        //and the need for this hasn't been verified
        $resultValueSource = FALSE;
        if (count($this->ResultArray) > 0) {
            $this->AccessArgs->setAccessNodeObject('resultKey', $keySource);
            $resultKeySource = $this->AccessArgs->accessNodeObject('resultKey');
            $this->AccessArgs->setAccessNodeObject('resultValue', $valueSource);
            $resultValueSource = $this->AccessArgs->accessNodeObject('resultValue');
        }

        if ($resultKeySource && $resultValueSource) {
                $result = collection($this->ResultArray)
                    ->reduce(function($harvest, $entity) use ($resultKeySource, $resultValueSource){
                        $harvest[$resultKeySource->value($entity)] = $resultValueSource->value($entity);
                        return $harvest;
                    }, []);
        } else {
            $result = [];
        }
        return $result;
    }

    /**
     * Get a list of distinct values
     *
     * @param $valueSource string|ValueSource
     * @return array
     */
    public function toDistinctList($valueSource = null)
    {
        return array_unique($this->toValueList($valueSource));
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
    public function find()
    {
        $this->AccessArgs = $this->AccessArgs ?? new LayerAccessArgs($this);
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
     * @return LayerAccessProcessor
     */
    public function perform($argObj)
    {
        $this->setArgObj($argObj);
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
        } else {
            $this->ResultArray = new \ArrayIterator($this->ResultArray);
        }

        if (!($this->ResultArray instanceof \Countable)) {
            osd($this->ResultArray);
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
        if (!isset($this->ResultArray)) {
            $this->ResultArray = $this->AppendIterator;
        }
        $column = $this->AccessArgs->getSortColumn('sort');
        $dir = $this->AccessArgs->getSortDirection();
        $type = $this->AccessArgs->getSortType();
        $unsorted = new Collection($this->ResultArray);
        $sorted = $unsorted->sortBy($column, $dir, $type)->toArray();
        //indexes are out of order and could be confusing
        return array_values($sorted);
    }

    protected function performPagination()
    {
        $page = $this->AccessArgs->valueOf('page');
        $limit = $this->AccessArgs->valueOf('limit');
        if (!isset($this->ResultArray)) {
            $this->ResultArray = $this->AppendIterator;
        }
        $unchuncked = new Collection($this->ResultArray);
        $chunked = $unchuncked->chunk($limit)->toArray();
//        osd($chunked);
        if(isset($chunked[$page])) {
            $result = $chunked[$page];
        } else {
            $result = array_pop($chunked);
        }
        return $result;
    }

    /**
     * Store an the Access process instructions
     *
     * @param $argObj LayerAccessArgs
     * @return bool
     */
    public function setArgObj($argObj)
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
    public function cloneArgObj()
    {
        $obj = clone $this->AccessArgs ?? new LayerAccessArgs();
        $obj->resetData();
        return $obj;

    }

    public function __debugInfo()
    {
        $result = [
            '[AppendIterator]' => isset($this->AppendIterator)
                ? 'Contains ' . $this->rawCount() . ' items.'
                : 'not set',
            '[AccessArgs]' => is_null($this->AccessArgs)
                ? 'null'
                : $this->AccessArgs,
            '[layerName]' => $this->layerName,
            '[ResultArray]' => is_null($this->ResultArray)
                ? 'null'
                : 'Contains ' . $this->resultCount() . ' items.'
        ];
        return $result;
    }
}
