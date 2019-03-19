<?php
namespace App\Test\TestCase\Model\Entity;

use App\Model\Entity\Member;
use Cake\TestSuite\TestCase;
use App\Model\Table\MembersTable;

/**
 * App\Model\Entity\Member Test Case
 */
class MemberTest extends TestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.members',
    ];
    /**
     * Test subject
     *
     * @var \App\Model\Entity\Member
     */
    public $Member;
    public $Members;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->Members = $this->getTableLocator()->get('Members');
        $this->Member = $this->Members->find('all')->toArray();
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Member, $this->Members);

        parent::tearDown();
    }

    /**
     * Test name method
     *
     * @return void
     */
    public function testNamePlainPerson()
    {
        $this->assertTrue($this->Member[0]->name() === 'Don Drake');
    }

    public function testNameFLPerson()
    {
        $this->assertTrue($this->Member[1]->name() === 'Gail Drake');
    }

    public function testNameLFPerson()
    {
        $this->assertTrue($this->Member[0]->name() === 'Drake, Don');
    }

    public function testNameLabeledPerson()
    {
        $this->assertTrue($this->Member[1]->name() === 'Person: Gail Drake');
    }

    public function testNamePlainOrg()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    public function testNameFLOrg()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    public function testNameLFOrg()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    public function testNameLabeledOrg()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    public function testNamePlainGroup()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    public function testNameFLGroup()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    public function testNameLFGroup()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    public function testNameLabeledGroup()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test isCollector method
     *
     * @return void
     */
    public function testIsCollector()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test collector method
     *
     * @return void
     */
    public function testCollector()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test collectedCount method
     *
     * @return void
     */
    public function testCollectedCount()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test isDispositionParticipant method
     *
     * @return void
     */
    public function testIsDispositionParticipant()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test dispositionCount method
     *
     * @return void
     */
    public function testDispositionCount()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test isActive method
     *
     * @return void
     */
    public function testIsActive()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test firstName method
     *
     * @return void
     */
    public function testFirstName()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test lastName method
     *
     * @return void
     */
    public function testLastName()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test type method
     *
     * @return void
     */
    public function testType()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
