<?php


namespace App\Interfaces;


use Cake\ORM\Query;

interface FilteringInterface
{

    public function userFilter(Query $query);

}
