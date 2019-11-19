<?php


namespace App\Model\Traits;

/**
 * LayerElementAccessTrait
 *
 * This trait adds data access features to classes that have an ID indexed
 * array available at ::getData().
 *
 * @package App\Model\Traits
 */
trait LayerElementAccessTrait
{

    /**
     * Return the n-th stored element or element(ID)
     *
     * Data is stored in id-indexed arrays, but this method will let you
     * pluck the id's or n-th item out
     *
     * @param int $number Array index 0 through n or Id of element
     * @param boolean $byIndex LAYERACC_INDEX or LAYERACC_ID
     * @return Entity
     */
    public function element($key, $byIndex = LAYERACC_INDEX){
        $data = $this->getData();
        if ($byIndex) {
            $data = array_values($data);
            if (count($data) > $key) {
                $result = $data[$key];
            } else {
                $result = null;
            }
        } else {
            if (in_array($key, $this->IDs())) {
                $result = $data[$key];
            } else {
                $result = null;
            }
        }
        return $result;
    }

    /**
     * Convenience wrapper to return the first element
     *
     * @return Entity
     */
    public function shift() {
        return $this->element(0, LAYERACC_INDEX);
    }


    /**
     * Get the IDs of all the primary entities in the stored stack entities
     *
     * @return array
     */
    public function IDs() {
        return array_keys($this->getData());
    }

}
