<?php
namespace App\View\Helper;

use App\View\Helper\ToolLinkHelper;

/**
 * CakePHP MemeberToolHelper
 * @author dondrake
 */
class MemberToolsHelper extends ToolLinkHelper {
	
	protected $alias = 'MemberTools';
	
	/**
	 * The list of layers that can get targeted tools
	 *
	 * @var array
	 */
	protected $_layers = ['member'];
    
}
