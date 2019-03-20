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
        $this->assertTrue($this->Member[1]->name(FIRST_LAST) === 'Gail Drake');
    }

    public function testNameLFPerson()
    {
        $this->assertTrue($this->Member[0]->name(LAST_FIRST) === 'Drake, Don');
    }

    public function testNameLabeledPerson()
    {
        $this->assertTrue($this->Member[1]->name(LABELED) === 'Person: Gail Drake');
    }

    public function testNamePlainOrg()
    {
        $this->assertTrue($this->Member[4]->name() === 'Alice Goask');
    }

    public function testNameFLOrg()
    {
        $this->assertTrue($this->Member[3]->name(FIRST_LAST) === 'Wonderland Group');
    }

    public function testNameLFOrg()
    {
        $this->assertTrue($this->Member[4]->name(LAST_FIRST) === 'Alice Goask');
    }

    public function testNameLabeledOrg()
    {
        $this->assertTrue($this->Member[3]->name(LABELED) === 'Institution: Wonderland Group');
    }

    public function testNamePlainGroup()
    {
        $this->assertTrue($this->Member[2]->name() === 'Drake Family');
    }

    public function testNameFLGroup()
    {
        $this->assertTrue($this->Member[6]->name(FIRST_LAST) === 'Collectors');
    }

    public function testNameLFGroup()
    {
        $this->assertTrue($this->Member[2]->name(LAST_FIRST) === 'Drake Family');
    }

    public function testNameLabeledGroup()
    {
        $this->assertTrue($this->Member[6]->name(LABELED) === 'Group: Collectors');
    }

    /**
     * Test isCollector with 1, 0, null
     *
     * @return void
     */
    public function testIsCollector()
    {
        $this->assertTrue($this->Member[0]->isCollector(),
            'Collector not detected on collector = 1');
        $this->assertFalse($this->Member[2]->isCollector(),
            'Collector wrongly detected on collector = null');
        $this->assertFalse($this->Member[3]->isCollector(),
            'Collector wrongly detected on collector = 0');
        $this->assertTrue($this->Member[8]->isCollector(),
            'Collector wrongly detected on collector = 2');
    }

    /**
     * Test collector method
     *
     * @return void
     */
    public function testCollector()
    {
        $this->assertEquals(1, $this->Member[0]->collector(),
            'collector did not return expected 1 value');
        $this->assertEquals(null, $this->Member[2]->collector(),
            'collector did not return expected null value');
        $this->assertEquals(2, $this->Member[8]->collector(),
            'collector did not return expected false value');
    }

    /**
     * Test collectedCount method
     *
     * @return void
     */
    public function testCollectedCount()
    {
        $this->assertEquals(1, $this->Member[0]->collectedCount(),
            'collector did not return expected 1 value');
        $this->assertEquals(0, $this->Member[2]->collectedCount(),
            'collector did not return expected null value');
        $this->assertEquals(2, $this->Member[8]->collectedCount(),
            'collector did not return expected false value');
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
