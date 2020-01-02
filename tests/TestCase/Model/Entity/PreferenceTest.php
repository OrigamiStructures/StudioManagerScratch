<?php
namespace App\Test\TestCase\Model\Entity;

use App\Model\Entity\Preference;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Entity\Preference Test Case
 */
class PreferenceTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Entity\Preference
     */
    public $Preference;
    public $prefs;
    public $defaults;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->Preference = new Preference();
        $this->prefs = [
            'pagination' => [
                'sort' => [
                    'direction' => 'DESC'
                ]
            ]
        ];
        $this->defaults = [
            'pagination.sort.direction' => 'ASC',
            'pagination.limit' => 10
        ];

    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Preference, $this->prefs, $this->defaults);

        parent::tearDown();
    }

    /**
     * Test for method
     *
     * @return void
     */
    public function testFor()
    {
        $this->Preference->setVariants($this->prefs);
        $this->Preference->setDefaults($this->defaults);

        $this->assertEquals('DESC', $this->Preference->for('pagination.sort.direction'),
            'for() did not return the expected user variant value');
        $this->assertEquals(10, $this->Preference->for('pagination.limit'),
            'for() did not return the expected default value in place of missing user value');
    }

    /**
     * @expectedException \App\Exception\BadClassConfigurationException
     */
    public function testForWithUnsetDefaults()
    {
        $this->Preference->setVariants($this->prefs);
        $this->Preference->for('pagination.sort.direction');
    }

    /**
     * Test getVariants method
     *
     * @return void
     */
    public function testGetVariants()
    {
        $this->Preference->setVariants($this->prefs);
        $this->assertEquals($this->prefs, $this->Preference->getVariants(),
            'The full set of user variants was ot returned');
    }

    /**
     * Test setVariant method
     *
     * @return void
     */
    public function testSetVariant()
    {
        $this->Preference->setVariants($this->prefs);
        $this->Preference->setDefaults($this->defaults);

        $this->Preference->setVariant('pagination.sort', 'NEW_VAL');
        $this->assertEquals('NEW_VAL', $this->Preference->for('pagination.sort'),
            'The new value was not set to the path location as requested');
    }

    /**
     * Test getVariant method
     *
     * @return void
     */
    public function testGetVariant()
    {
        $this->Preference->setVariants($this->prefs);
        $this->Preference->setDefaults($this->defaults);

        $this->assertEquals('DESC', $this->Preference->getVariant('pagination.sort.direction'),
            'getVariant(path) did not return the expected user variant value');
        $this->assertEquals(null, $this->Preference->getVariant('pagination.limit'),
            'getVariant() did not return the expected null in place of missing user value');
    }
}
