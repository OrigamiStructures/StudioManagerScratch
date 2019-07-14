<?php
namespace App\Test\TestCase\Model\Behavior;

use App\Model\Behavior\IntegerQueryBehavior;
use Cake\TestSuite\TestCase;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\Table;
use Cake\ORM\Query;
use App\Test\TestCase\Model\Behavior\SampleTable;

/**
 * App\Model\Behavior\IntegerQueryBehavior Test Case
 */
class IntegerQueryBehaviorTest extends TestCase
{

	/**
	 * Fixtures
	 *
	 * @var array
	 */
	public $fixtures = [
//        'app.art_stacks',
		'app.pieces'
	];

    /**
     * Test subject
     *
     * @var \App\Model\Behavior\IntegerQueryBehavior
     */
    public $IntegerQuery;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->connection = ConnectionManager::get('test');
        $this->sample = new SampleTable(['connection' => $this->connection]);
        $this->sample->addBehavior('IntegerQuery');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->sample);
        $this->getTableLocator()->clear();

        parent::tearDown();
    }

    /**
     * Test integer method 'between'
     *
     * @return void
     */
    public function testInteger()
    {
        /*
         * between with properly ordered values
         */
        $q = $this->sample->find('number', ['values' => ['between', 3, 4]]);
        $this->assertContains('WHERE number BETWEEN :c0 AND :c1', $q->sql());
        $v = $q->getValueBinder();
        $this->assertEquals(3, $v->bindings()[':c0']['value']);
        $this->assertEquals(4, $v->bindings()[':c1']['value']);
        unset($q, $v);

        /*
         * between with properly ordered values
         */
        $q = $this->sample->find('number', ['values' => ['between', 12, 2]]);
        $this->assertContains('WHERE number BETWEEN :c0 AND :c1', $q->sql());
        $v = $q->getValueBinder();
        $this->assertEquals(2, $v->bindings()[':c0']['value']);
        $this->assertEquals(12, $v->bindings()[':c1']['value']);
        unset($q, $v);

        /*
         * Less than
         */
        $q = $this->sample->find('number', ['values' => ['<', 9]]);
        $this->assertContains('WHERE number < :c0', $q->sql());
        $v = $q->getValueBinder();
        $this->assertEquals(9, $v->bindings()[':c0']['value']);
        unset($q, $v);

        /*
         * Greater than
         */
        $q = $this->sample->find('number', ['values' => ['>', 7]]);
        $this->assertContains('WHERE number > :c0', $q->sql());
        $v = $q->getValueBinder();
        $this->assertEquals(7, $v->bindings()[':c0']['value']);
        unset($q, $v);

        /*
         * Equal to
         */
        $q = $this->sample->find('number', ['values' => ['=', 7]]);
        $this->assertContains('WHERE number = :c0', $q->sql());
        $v = $q->getValueBinder();
        $this->assertEquals(7, $v->bindings()[':c0']['value']);
        unset($q, $v);

        /*
         * Greater than or equal to
         */
        $q = $this->sample->find('number', ['values' => ['>=', 7]]);
        $this->assertContains('WHERE number >= :c0', $q->sql());
        $v = $q->getValueBinder();
        $this->assertEquals(7, $v->bindings()[':c0']['value']);
        unset($q, $v);

        /*
         * Less than or equal to
         */
        $q = $this->sample->find('number', ['values' => ['<=', 5, 3, 7]]);
        $this->assertContains('WHERE number <= :c0', $q->sql());
        $v = $q->getValueBinder();
        $this->assertEquals(5, $v->bindings()[':c0']['value']);
        unset($q, $v);

        /*
         * IN from array
         */
        $q = $this->sample->find('number', ['values' => [5, 3, 7, 9]]);
        $this->assertContains('WHERE number in (:c0,:c1,:c2,:c3)', $q->sql());
        $v = $q->getValueBinder();
        $this->assertEquals(3, $v->bindings()[':c1']['value']);
        $this->assertEquals(9, $v->bindings()[':c3']['value']);
        unset($q, $v);

        /*
         * A based on a single element array
         */
        $q = $this->sample->find('number', ['values' => ['13']]);
        $this->assertContains('WHERE number = :c0', $q->sql());
        $v = $q->getValueBinder();
        $this->assertEquals(13, $v->bindings()[':c0']['value']);
        unset($q, $v);
//
//        /*
//         * find based on a single range string in an array
//         */
        $q = $this->sample->find('number', ['values' => ['1-3, 9, 13-15']]);
        $this->assertContains('WHERE number in (:c0,:c1,:c2,:c3,:c4,:c5,:c6)', $q->sql());
        $v = $q->getValueBinder();
        $this->assertEquals(7, count($v->bindings()));
        $this->assertEquals(14, $v->bindings()[':c5']['value']);
        unset($q, $v);
        
    }
    
    protected function _peek($param) {
        echo '<pre>';
        print_r($param);
        echo '</pre>';
    }
    
}
