<?php


namespace App\View\Helper;


use Cake\View\Helper;

class PeopleHelper extends Helper
{
	
	public $helpers = ['Html'];

	protected $manifestSummaryFormat = 
		'<em style="font-weight: normal;">Artist:</em> %s '
        . '<span style="font-weight: normal; font-size: smaller;">'
        . '(<em>Manager:</em> %s has %s)'
        . '</span>';

	/**
     * Give a ManifestStackEntity output a summary line describing it
	 * 
	 * $format recieves $artistName, $managerName, $access
	 * 'access' says 'Full Access' or 'Limited Access'
     *
     * @param ManifestStack $manifest
     * @param string $format Alternate sprintf format
     * @return type
     */
    function manifestSummary($manifest, $format = null) {
		$format = is_null($format) ? $this->manifestSummaryFormat : $format;

        $artistName = $manifest->artistCard()->rootDisplayValue();
        $managerName = $manifest->selfAssigned()
            ? 'self'
            : $manifest->managerCard()->rootDisplayValue();
        $access = $manifest->accessSummary();

        return $this->Html->tag(
            'h3',
            sprintf($format, $artistName, $managerName, $access),
            ['escape' => FALSE]);
    }
}