<?php


namespace App\Controller;


class AddressBookController extends AppController
{
    public function index()
    {
        $RolodexCards = $this->getTableLocator()->get('RolodexCards');
        $ids = $RolodexCards->Identities->find('list')->toArray();
        $results = $RolodexCards->find('stacksFor',  ['seed' => 'identity', 'ids' => $ids]);
        $this->set('results', $results);
    }
}