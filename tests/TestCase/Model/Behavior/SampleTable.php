<?php
namespace App\Test\TestCase\Model\Behavior;

use Cake\ORM\Table;
use Cake\ORM\Query;

/**
 * Used for testing counter cache with custom finder
 */
class SampleTable extends Table
{
    public function initialize(array $config) {
        parent::initialize($config);
        $this->table('pieces');
    }
    public function findTitle(Query $query, array $options) {
        return $this->string($query, 'title', $options['values']);
    }
    public function findNumber(Query $query, array $options)
    {
        return $this->integer($query, 'number', $options['values']);
    }
}
