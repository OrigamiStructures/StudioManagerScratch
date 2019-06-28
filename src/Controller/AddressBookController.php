<?php
namespace App\Controller;

use Cake\ORM\TableRegistry;

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
        $ids = //$this->paginate(
            $PersonCards->Identities->find('list')
            ->order(['last_name'])
        //)
        ->toArray();
        osd($ids);
//        $results = $this->paginate($PersonCards->find('stacksFor',  ['seed' => 'identity', 'ids' => $ids]));
        $results = $this->paginate($PersonCards, ['seed' => 'identity', 'ids' => $ids]);
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
        $member = $this->Members->get($id, [
            'contain' => ['Images', 'Groups', 'Users', 'Dispositions', 'Locations']
        ]);
        $this->set('member', $member);
        $this->set('_serialize', ['member']);
    }


}