<?php
namespace App\Model\Lib\EditionStack;

/**
 * Description of EditionStack
 * 
 * @todo Thinking of how to eliminate/refactor EditionStackComponent
 *
 * @author dondrake
 */
class EditionStack {
	
	protected $_edition;

	protected $_formats = [];
	
	protected $_providers = [];
	
	protected $_pieces = [];
	
	private $_owner_names = [];
	
	public function __construct($edition) {
		;
	}
}
