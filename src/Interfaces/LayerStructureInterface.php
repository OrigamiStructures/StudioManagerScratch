<?php


namespace App\Interfaces;


use App\Model\Lib\LayerProcessor;

interface LayerStructureInterface
{

    /**
     * Gather the available data at this level and package the iterator
     *
     * @param $name string
     * @return LayerProcessor
     */
    public function getLayer($name);

}
