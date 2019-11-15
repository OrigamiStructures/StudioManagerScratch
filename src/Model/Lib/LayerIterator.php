<?php


namespace App\Model\Lib;

/**
 * LayerIterator
 *
 * @package App\Model\Lib
 */
class LayerIterator extends \AppendIterator
{

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
}
