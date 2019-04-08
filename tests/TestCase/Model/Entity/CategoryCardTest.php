<?php
namespace App\Test\TestCase\Model\Entity;

use App\Model\Entity\CategoryCard;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Entity\CategoryCard Test Case
 */
class CategoryCardTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Entity\CategoryCard
     */
    public $CategoryCard;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->CategoryCard = new CategoryCard();
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->CategoryCard);

        parent::tearDown();
    }

    /**
     * Test isGroup method
     *
     * @return void
     */
    public function testIsGroup()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test hasMembers method
     *
     * @return void
     */
    public function testHasMembers()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test memberEntities method
     *
     * @return void
     */
    public function testMemberEntities()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test memberIDs method
     *
     * @return void
     */
    public function testMemberIDs()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test members method
     *
     * @return void
     */
    public function testMembers()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
