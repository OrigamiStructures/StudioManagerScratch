<?php


namespace App\Controller;


class SupervisorsController extends AppController
{
    public function index()
    {
        $manifests = "Manifests";
        $this->set('manifests', $manifests);
    }

}