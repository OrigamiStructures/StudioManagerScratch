<?php
namespace App\Test\TestCase\Model\Entity;

use App\Model\Entity\Manifest;
use Cake\Log\Log;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Entity\Manifest Test Case
 */
class ManifestTest extends TestCase
{
    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        parent::tearDown();
    }

    /**
     * Test hasArtist method
     *
     * @return void
     */
    public function testActorValidationValid()
    {
        $manifest = new Manifest(['member_id' => '3']);
        $this->assertTrue($manifest->getMemberId('artist') == 3);
        $this->assertTrue($manifest->getMemberId('ArtIST') == 3);
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function testActorValidatonInvalid()
    {
        $manifest = new Manifest(['member_id' => '3']);
        $manifest->getOwnerId('badActor');
    }

    /**
     * Test getName method
     *
     * @return void
     */
    public function testGetNameNoNames()
    {
        $manifest = new Manifest(['member_id' => '3']);

        $name = $manifest->getName('artist');
        self::assertEquals(null, $name);
    }

    public function testGetName()
    {
        $manifest = new Manifest(['member_id' => '3', 'names' => ['3' => 'The Name']]);

        $name = $manifest->getName('artist');
        self::assertEquals('The Name', $name);
    }
}
