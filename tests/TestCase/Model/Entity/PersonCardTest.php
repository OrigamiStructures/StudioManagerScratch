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

    public $localSupervisor = 'f22f9b46-345f-4c6f-9637-060ceacb21b2';

    public $foreignSupervisor = '708cfc57-1162-4c5b-9092-42c25da131a9';

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

    //<editor-fold desc="isReceivingManager variants">

    /**
     * From the card owner's view, is receiving manager
     */
    public function testIsReceivingManagerWhenSeenLocally()
    {
        $this->assertTrue($this->DonCard->isRecievingManager($this->localSupervisor),
            'A foreign sup delegated to this manager but the manager\'s ' .
            'owner doesn\'t flag the card as a ReceivingManger');
    }

    /**
     * From foreign delegator's view, is receiving manager
     */
    public function testIsReceivingManagerFromForeignView()
    {
        $this->assertTrue($this->DonCard->isRecievingManager($this->foreignSupervisor),
            'This foreign sup delegated to this manager but ' .
            'doesn\'t flag the card as a ReceivingManger');
    }

    /**
     * From owner view and foreign view, card is not a receiving manager
     *
     * @todo 'Permission development may cause "foriegn" version problems.'
     */
    public function testIsReceivingManagerNOT()
    {
        $this->assertFalse($this->GailCard->isRecievingManager($this->localSupervisor),
            'The card is not a recieving manager but the owner supervisor ' .
            'saw it as one');

        $this->markAsRisky('Permission development may cause "foriegn" version problems.');
        $this->assertFalse($this->GailCard->isRecievingManager($this->foreignSupervisor),
            'The card is not a recieving manager but a foreign supervisor ' .
            'saw it as one');
    }
    //</editor-fold>

    //<editor-fold desc="receivedManagent variants">

    /**
     * supervisor detects recieved management on a person they own
     */
    public function testReceivedManagentOnALocalCard()
    {
        $this->assertCount(1, $this->DonCard->receivedManagement($this->localSupervisor),
            'A foreign sup delegated to this manager but the manager\'s ' .
            'owner doesn\'t return that foreign manifest');
    }

    /**
     * foreign supervisor detects received management on a person they delgated to
     */
    public function testReceivedManagentOnAForeignCard()
    {
        $this->assertCount(1, $this->DonCard->receivedManagement($this->foreignSupervisor),
            'This foreign sup delegated to this manager but ' .
            'doesn\'t find the manifest they issued');
    }

    /**
     * Non managers act properly when polled for their received managements
     *
     * @todo foreign sup variant may fail when permissions are finished
     */
    public function testReceivedManagentOnA_NOT_Manager() {
        $result = $this->GailCard->receivedManagement($this->localSupervisor);
        $this->assertTrue(is_array($result) && count($result) == 0,
            'The return on "no received management" should be an empty array');

        $this->assertCount(0, $this->GailCard->receivedManagement($this->localSupervisor),
            'The card is not a recieving manager but the owner supervisor ' .
            'found more than zero manifests');

        $this->markAsRisky('Permission development may cause "foriegn" version problems.');
        $this->assertCount(0, $this->GailCard->receivedManagement($this->foreignSupervisor),
            'The card is not a recieving manager but a foreign supervisor ' .
            'found more than zero manifests');
    }
    //</editor-fold>

    public function testIsManagementDelegate()
    {

    }

    public function testDelegatedManagement()
    {

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
