<?php
namespace App\Model\Lib;

use App\Exception\BadClassConfigurationException;
use App\Model\Table\PersonCardsTable;
use Cake\ORM\TableRegistry;
use App\Model\Entity\PersonCard;

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
     * Array of types of 'admin' access
     *
     * Methods that test for a kind of admin access need to insure only
     * valid admin roles are considered for testing. If the value in question
     * is not in this array, just ignore it altogether. (see $this->admin() )
     *
     * @var array
     */
    protected $_admin_roles = [ADMIN_SYSTEM, ADMIN_ARTIST];

	/**
	 * Lazy loaded PersonCard for the registered user
	 *
	 * @var PersonCard
	 */
	protected $Person = NULL;

	public function __construct($data) {

		if (is_null($data)) {
			$message = 'The first request for the CurrentUser object '
					. 'must include the data to construct it.';
			throw new BadClassConfigurationException($message);
		}
		$this->data = $data;
	}

	/**
	 * Registered user's manager token
	 *
	 * @return string
	 */
	public function managerId() {
		return $this->data['management_token'];
	}

	/**
	 * Registered user's supervisor token
	 *
	 * @return string
	 */
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
    public function getName()
    {
		if (is_null($this->Person)) {
			$PersonCards = TableRegistry::getTableLocator()->get('PersonCards');
			$this->Person = $PersonCards
				->find('stacksFor', ['seed' => 'identity', 'ids' => [$this->memberId()]])
				->element(0,LAYERACC_INDEX);
		}
        return $this->Person->name();
	}

	/**
	 * member_id for the registered user
	 *
	 * @return string
	 */
    protected function memberId()
    {
        return $this->data['member_id'];
	}

	/**
	 * Is the registered user a superuser?
	 *
	 * @return boolean
	 */
	public function isSuperuser() {
		return $this->data['is_superuser'] === TRUE;
	}

	/**
	 * Is the logged-in registered user's account active?
	 *
	 * @return string
	 */
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

    public function userRole()
    {
        return $this->data['role'];
    }

    /**
     * Determine the degree (if any) of admin access
     *
     * @param string $type
     * @return boolean
     */
    public function admin($type = NULL) {
        if (is_null($type)) {
            return in_array($this->userRole(), $this->_admin_roles);
        } elseif (in_array($type, $this->_admin_roles)) {
            return strtolower($type) === $this->userRole();
        }
        return false;
        // Very tentative implementation plan:
        //
        // needs to sent TRUE if user is and 'artist' admin, meaning
        // they need to act as an artist other than themselves. And needs
        // to return TRUE for both 'system' and 'artist' for developers
    }

}
