<?php
namespace App\Test\TestCase\Model\Entity;

use App\Model\Entity\PersonCard;
use App\Model\Table\PersonCardsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Entity\PersonCard Test Case
 */
class PersonCardTest extends TestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.members',
        'app.users',
        'app.groups_members',
        'app.contacts',
        'app.addresses',
        'app.manifests',
        'app.images',
        'app.dispositions'
    ];

    /**
     * Test subject
     *
     * @var \App\Model\Entity\PersonCard
     */
    public $DonCard;

    /**
     * Test subject
     *
     * @var \App\Model\Entity\PersonCard
     */
    public $RaeCard;

    /**
     * Test subject
     *
     * @var \App\Model\Entity\PersonCard
     */
    public $GailCard;

    /**
     * Test subject
     *
     * @var PersonCardsTable
     */
    public $PersonCardsTable;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->PersonCardsTable = TableRegistry::getTableLocator()->get('PersonCards');
        $set = $this->PersonCardsTable->find('stacksFor', ['seed' => 'identity', 'ids' => ['1', '9', '2']]);
        $this->DonCard = $set->element('1', LAYERACC_ID);
        $this->RaeCard = $set->element('9', LAYERACC_ID);
        $this->GailCard = $set->element('2', LAYERACC_ID);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->DonCard);
        unset($this->RaeCard);
        unset($this->GailCard);
        unset($this->PersonCardsTable);

        parent::tearDown();
    }

    /**
     * Test hasContacts method
     *
     * @return void
     */
    public function testHasContacts()
    {
        $this->assertTrue($this->DonCard->hasContacts(),
            'A card with contacts reports none');
        $this->assertFalse($this->RaeCard->hasContacts(),
            'A card with no conctacts reports it has some');
    }

    /**
     * Test getContacts method
     *
     * @return void
     */
    public function testGetContacts()
    {
        $this->assertCount(4, $this->DonCard->getContacts()->toArray(),
            'getContacts did not return the expected number of contacts');
        $this->assertCount(0, $this->RaeCard->getContacts()->toArray(),
            'getContacts returned contacts when there were none');
    }

    /**
     * Test hasAddresses method
     *
     * @return void
     */
    public function testHasAddresses()
    {
        $this->assertTrue($this->DonCard->hasAddresses(),
            'A card with addresses reports none');
        $this->assertFalse($this->RaeCard->hasAddresses(),
            'A card with no addresses reports it has some');
    }

    /**
     * Test getAddresses method
     *
     * @return void
     */
    public function testGetAddresses()
    {
        $this->assertCount(2, $this->DonCard->getAddresses()->toArray(),
            'getAddresses did not return the expected number of addresses');
        $this->assertCount(0, $this->RaeCard->getAddresses()->toArray(),
            'getAddresses returned addresses when there were none');
    }

    /**
     * Test isSupervisor method
     *
     * @return void
     */
    public function testIsSupervisor()
    {
        $this->assertTrue($this->DonCard->isSupervisor(),
            'Supervisor status was not detected');
        $this->assertFalse($this->RaeCard->isSupervisor(),
            'Supervisor status was detected though there are no manifests');
        $this->assertFalse($this->GailCard->isSupervisor(),
            'Supervisor status was detected though this artist is not a supervisor');
    }

    /**
     * Test isManager method
     *
     * @return void
     */
    public function testIsManager()
    {
        $this->assertTrue($this->DonCard->isManager(),
            'Manager status was not detected');
        $this->assertFalse($this->RaeCard->isManager(),
            'Manager status was detected though there are no manifests');
        $this->assertFalse($this->GailCard->isManager(),
            'Manager status was detected though this artist is not a manager');
    }

    /**
     * Test isArtist method
     *
     * @return void
     */
    public function testIsArtist()
    {
        $this->assertTrue($this->DonCard->isArtist(),
            'Artist status was not detected');
        $this->assertFalse($this->RaeCard->isArtist(),
            'Artist status was detected though there are no manifests');
    }

}
