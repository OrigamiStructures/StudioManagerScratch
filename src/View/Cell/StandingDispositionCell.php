<?php
namespace App\View\Cell;

use Cake\View\Cell;
use Cake\Network\Request;
use Cake\Network\Response;
use Cake\Event\EventManager;
use App\Lib\SystemState;
use Cake\Cache\Cache;

/**
 * StandingDisposition cell
 */
class StandingDispositionCell extends Cell
{

    /**
     * List of valid options that can be passed into this
     * cell's constructor.
     *
     * @var array
     */
    protected $_validCellOptions = [];

	public $SystemState;

	public $standing_disposition;

	public function __construct(Request $request = null,
			Response $response = null,
			EventManager $eventManager = null, array $cellOptions = array()) {
		parent::__construct($request, $response, $eventManager, $cellOptions);
		$this->SystemState = $SystemState = $cellOptions['SystemState'];
		$this->standing_disposition = $standing_disposition = $this->SystemState->standing_disposition;
		$this->set(compact('standing_disposition', 'SystemState'));
	}

    /**
     * Default display method.
     *
     * @return void
     */
    public function display()
    {
		if (is_null($this->request->getSession()->read('Auth.User')) && is_null($this->SystemState->artistId())) {
			// if there's no one logged in or no artist id targeted
			// we wouldn't show a standing disposition even if one existed
			// otherwise it was already set in construction if it existed.
			$this->set('standing_disposition', FALSE);
		} else {
			$this->set('standing_disposition', $this->SystemState->standing_disposition);
		}
    }

}
