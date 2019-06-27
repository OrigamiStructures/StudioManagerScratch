<?php


namespace App\Controller;

use Cake\Controller\Component\PaginatorComponent;


class AddressBookController extends AppController
{
    public $paginate = [
        'limit' => 5
    ];

    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Paginator');
    }

    public function index()
    {
        $PersonCards = $this->getTableLocator()->get('PersonCards');
        $ids = $this->paginate(
            $PersonCards->Identities->find('list')
            ->order(['last_name'])
        )
        ->toArray();
        osd($ids);
        $results = $PersonCards->find('stacksFor',  ['seed' => 'identity', 'ids' => $ids]);
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