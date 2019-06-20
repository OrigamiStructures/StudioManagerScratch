<?php


namespace App\Controller;


class AddressBookController extends AppController
{
    public function index()
    {
        $PersonCards = $this->getTableLocator()->get('PersonCards');
        $ids = $PersonCards->Identities->find('list')
            ->order(['last_name'])
            ->toArray();
        osd($ids);die;
        $results = $PersonCards->find('stacksFor',  ['seed' => 'identity', 'ids' => $ids]);
        $this->set('results', $results);
    }
}