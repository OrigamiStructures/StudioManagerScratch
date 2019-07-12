<?php //
namespace App\Test\TestCase\Model\Behavior;

use App\Model\Behavior\StringQueryBehavior;
use Cake\TestSuite\TestCase;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\Table;
use Cake\ORM\Query;
use App\Test\TestCase\Model\Behavior\SampleTable;

/**
 * App\Model\Behavior\StringQueryBehavior Test Case
 */
class StringQueryBehaviorTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Behavior\StringQueryBehavior
     */
    public $StringQueryBehavior;

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
        $this->sample->addBehavior('StringQuery');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->sample);
        parent::tearDown();
    }

    /**
     * Test string method
     *
     * @return void
     */
    public function testString()
    {
        /*
         * straight string search
         */
        $q = $this->sample->find('title', ['values' => 'between']);
        $this->assertContains('WHERE title = :c0', $q->sql());
        $v = $q->getValueBinder();
        $this->assertEquals('between', $v->bindings()[':c0']['value']);
        unset($q, $v);

        /*
         * string passed in an array search
         */
        $q = $this->sample->find('title', ['values' => ['between']]);
        $this->assertContains('WHERE title = :c0', $q->sql());
        $v = $q->getValueBinder();
        $this->assertEquals('between', $v->bindings()[':c0']['value']);
        unset($q, $v);

        /*
         * embedded wildcard
         */
        $q = $this->sample->find('title', ['values' => 'between % things']);
        $this->assertContains('WHERE title = :c0', $q->sql());
        $v = $q->getValueBinder();
        $this->assertEquals('between % things', $v->bindings()[':c0']['value']);
        unset($q, $v);

        /*
         * leading wildcard (in an array)
         */
        $q = $this->sample->find('title', ['values' => ['%between']]);
        $this->assertContains('WHERE title like :c0', $q->sql());
        $v = $q->getValueBinder();
        $this->assertEquals('%between', $v->bindings()[':c0']['value']);
        unset($q, $v);

        /*
         * trailing wildcard
         */
        $q = $this->sample->find('title', ['values' => ['between%']]);
        $this->assertContains('WHERE title like :c0', $q->sql());
        $v = $q->getValueBinder();
        $this->assertEquals('between%', $v->bindings()[':c0']['value']);
        unset($q, $v);

        /*
         * leading and trailing wildcard
         */
        $q = $this->sample->find('title', ['values' => ['% between %']]);
        $this->assertContains('WHERE title like :c0', $q->sql());
        $v = $q->getValueBinder();
        $this->assertEquals('% between %', $v->bindings()[':c0']['value']);
        unset($q, $v);

    }
    
}
