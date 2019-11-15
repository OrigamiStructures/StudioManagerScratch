<?php


namespace App\Interfaces;


use App\Model\Lib\ValueSource;
use App\Model\Lib\ValueSourceRegistry;

interface LayerAccessInterface
{

    /**
     * Get the result as an array of entities
     *
     * @return array
     */
    public function toArray();

    /**
     * Get the result as Layer object
     *
     * @return Layer
     */
    public function toLayer();

    /**
     * Get an array of values
     *
     * @param $valueSource string|ValueSource
     * @return array
     */
    public function toValueList($valueSource);

    /**
     * Get a key => value list
     *
     * @param $keySource string|ValueSource
     * @param $valueSource string|ValueSource
     * @return array
     */
    public function toKeyValueList($keySource, $valueSource);

    /**
     * Get a list of distinct values
     *
     * @param $valueSource string|ValueSource
     * @return array
     */
    public function toDistinctList($valueSource);

    /**
     * Get the stored registry instance
     *
     * @return ValueSourceRegistry
     */
    public function getValueRegistry();

}
