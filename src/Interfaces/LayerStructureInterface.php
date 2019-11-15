<?php


namespace App\Interfaces;


use App\Model\Lib\LayerIterator;

interface LayerStructureInterface
{

    /**
     * Gather the available data at this level and package the iterator
     *
     * @param $name string
     * @return LayerIterator
     */
    public function getLayer($name);

}
