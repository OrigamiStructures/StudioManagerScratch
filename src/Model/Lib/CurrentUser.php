<?php
namespace App\Model\Lib;

use App\Exception\BadClassConfigurationException;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;

/**
 * Description of CurrentUser
 *
 * @author dondrake
 */
class CurrentUser {

	/**
	 * User record moved in from the Auth system storage repository
	 *
	 * [
	 *	'id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
	 *	'management_token' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
	 *	'username' => 'don',
	 *	'email' => 'ddrake@dreamingmind.com',
	 *	'first_name' => 'Don',
	 *	'last_name' => 'Drake',
	 *	'activation_date' => object(Cake\I18n\Time) { },
	 *	'tos_date' => object(Cake\I18n\Time) { },
	 *	'active' => true,
	 *	'is_superuser' => false,
	 *	'role' => 'user',
	 *	'created' => object(Cake\I18n\Time) { },
	 *	'modified' => object(Cake\I18n\Time) { },
	 *	'artist_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
	 *	'member_id' => (int) 1
	 * ]
	 * 
	 * @var array
	 */
	protected $data;
	
	/**
	 * Lazy loaded PersonCard for the registered user
	 *
	 * @var PersonCard
	 */
	protected $Person = NULL;
		
	public function __construct($data, $options = []) {
		
		if (is_null($data)) {
			$message = 'The first request for the CurrentUser object '
					. 'must include the data to construct it.';
			throw new BadClassConfigurationException($msg);
		}
		$this->data = $data;
	}

	public function managerId() {
		return $this->data['management_token'];
	}
	
	public function supervisorId() {
		return $this->data['management_token'];
	}
	
	public function userId() {
		return $this->data['id'];
	}

	/**
	 * Get the name from the stored PersonCard
	 * 
	 * This lazy loads the card
	 * 
	 * @return string
	 */
    public function name()
    {
		if (is_null($this->Person)) {
			$PersonCards = TableRegistry::getTableLocator()->get('PersonCards');
			$this->Person = $PersonCards
				->find('stacksFor', ['seed' => 'identity', 'ids' => [$this->memberId()]])
				->element(0,LAYERACC_INDEX);
		}
        return $this->Person->name();
	}

    protected function memberId()
    {
        return $this->data['member_id'];
	}
	
	public function isSuperuser() {
		return $this->data['is_superuser'] === TRUE;
	}
	
	public function isActive() {
		return $this->data['active'] === TRUE;
	}
//	public function __debugInfo() {
//		return ['data' => $this->data];
//	}
	
//	public function setUser($data) {
//		$this->data = $data;
//	}
	
//	public function user() {
//		return $this->data;
//	}
	
}
