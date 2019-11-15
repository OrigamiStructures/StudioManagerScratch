<?php
namespace App\Interfaces;

use App\Model\Lib\LayerAccessArgs;
use App\Model\Lib\LayerIterator;

interface LayerTaskInterface
{

    /**
     * Initiate a fluent Access definition
     *
     * @todo This name has a collision. It will be changed later
     * @return LayerAccessArgs
     */
    public function NEWfind();

    /**
     * Run the Access process and return an iterator containing the result
     *
     * @param $argObj LayerAccessArgs
     * @return LayerIterator
     */
    public function perform($argObj);

    /**
     * Store an the Access process instructions
     *
     * @param $argObj LayerAccessArgs
     * @return bool
     */
    public function setAccessArgs($argObj);

    /**
     * Get a copy of the Access instructions (with no included data)
     *
     * @return LayerAccessArgs
     */
    public function copyArgObj();
}
