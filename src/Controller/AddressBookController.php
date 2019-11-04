<?php
namespace App\Controller;

use Cake\ORM\TableRegistry;
use App\Model\Table\MembersTable;

/**
 * Class AddressBookController
 * @package App\Controller\
 * @property MembersTable $Members
 */
class AddressBookController extends AppController
{
    public $paginate = [
        'limit' => 5,
		'sort' => 'last_name',
    ];

    public function initialize()
    {
        parent::initialize();
    }

    public function index()
    {
        $PersonCards = TableRegistry::getTableLocator()->get('PersonCards');
        $ids = $PersonCards->Identities->find('list')->order(['last_name'])->toArray();

		$stackCall = function($paginator) use ($PersonCards, $ids) {
			return $PersonCards->find(
					'stacksFor', 
					['seed' => 'identity', 'ids' => $ids, 'paginator' => $paginator]
				);
		};
		
        $results = $this->paginate($stackCall);
        $this->set('results', $results);
    }

    /**
     * View method
     *
     * @param string|null $id Member id.
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function view($id = null)     {
        try {
            $member = $this->Members->get($id, [
                'contain' => ['Images', 'Groups', 'Users', 'Dispositions', 'Locations']
            ]);
        } catch (\Exception $e) {

        }
        $this->set('member', $member);
        $this->set('_serialize', ['member']);
    }


}