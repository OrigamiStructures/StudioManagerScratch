<?php


namespace App\View\Helper;


use Cake\View\Helper;

class PeopleHelper extends Helper
{


    /**
     * This should move to a helper
     *
     * @param type $manifest
     * @param type $helper
     * @param type $outputPattern
     * @return type
     */
    function manifestSummary($manifest, $helper, $outputPattern) {

        $artistName = $manifest->artistCard()->rootDisplayValue();
        $managerName = $manifest->selfAssigned()
            ? 'self'
            : $manifest->managerCard()->rootDisplayValue();
        $access = $manifest->accessSummary();

        return $helper->tag(
            'h3',
            sprintf($outputPattern, $artistName, $managerName, $access),
            ['escape' => FALSE]);
    }
}