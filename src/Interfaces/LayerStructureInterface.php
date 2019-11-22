<?php


namespace App\Interfaces;


use App\Model\Lib\LayerAccessArgs;
use App\Model\Lib\LayerAccessProcessor;

interface LayerStructureInterface
{

    /**
     * Gather the available data at this level and package the iterator
     *
     * @param $name string
     * @return LayerAccessProcessor
     */
    public function getLayer($name);

    /**
     * Get an new LayerAccessArgs instance
     * @return LayerAccessArgs
     */
    public function getArgObj();

}
