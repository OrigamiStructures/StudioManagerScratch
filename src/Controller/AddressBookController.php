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
}