<?php


namespace App\Controller;


class AddressBookController extends AppController
{
    public function index()
    {
        $Members = $this->getTableLocator()->get('Members');
        $results = $Members->find('all')->toArray();
        $this->set('results', $results);
    }
}