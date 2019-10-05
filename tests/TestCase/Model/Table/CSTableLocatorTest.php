<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\CSTableLocator;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use App\Model\Table\MenusTable;
use Cake\ORM\Locator\TableLocator;

/**
 * App\Model\Table\CSTableLocator Test Case
 */
class CSTableLocatorTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\CSTableLocator
     */
    public $CSTableLocatorWithConfig;

    /**
     * Test subject
     *
     * @var \App\Model\Table\CSTableLocator
     */
    public $CSTableLocatorNoConfig;

    /**
     * Sample config array
     *
     * @var array
     */
    public $configArray = [
        'ContextUser' => 'context user object',
        'CurrentUser' => 'current user object'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->CSTableLocatorWithConfig = new CSTableLocator($this->configArray);
        $this->CSTableLocatorNoConfig = new CSTableLocator();
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->CSTableLocatorWithConfig);
        unset($this->CSTableLocatorNoConfig);

        parent::tearDown();
    }

    /**
     * Test __construct method
     *
     * @return void
     */
    public function testConstruct()
    {
        $this->assertArraySubset(
            $this->configArray,
            $this->CSTableLocatorWithConfig->getInjections(),
            false,
            'The config values were not stored during construction');

        $this->assertEmpty(
            $this->CSTableLocatorNoConfig->getInjections(),
            'The config has content though no values were provided');
    }

    /**
     * Test parent::__construct method
     *
     * AppTable has some changes to accommodate the override factory.
     * I need to make sure it still works in native mode
     */
    public function testConstructFromParent()
    {
        $TableLocator = new TableLocator();
        $table = $TableLocator->get('Menus');
        $this->assertTrue(get_class($table) === 'App\Model\Table\MenusTable',
            'parent version of get() didn\'t construct a table as expected');
    }

    /**
     * Test get method when locator has stored config values
     *
     * @return void
     */
    public function testGetStoredConfig()
    {
        $table = $this->CSTableLocatorWithConfig->get('Menus');
        $this->assertTrue(get_class($table) === 'App\Model\Table\MenusTable',
            'get() didn\'t construct a table');
        $this->assertTrue($table->contextUser() === 'context user object',
            'property did not populate from stored config value');
        $this->assertTrue($table->currentUser() === 'current user object',
            'property did not populate from stored config value');
    }

    /**
     * Test get method when locator doesn't have stored config values
     *
     * @return void
     */
    public function testGetNoConfig()
    {
        $table = $this->CSTableLocatorNoConfig->get('Menus');
        $this->assertTrue(get_class($table) === 'App\Model\Table\MenusTable',
            'get() didn\'t construct a table');
        $this->assertTrue($table->contextUser() === null,
            'property exists though it was not in config');
        $this->assertTrue($table->currentUser() === null,
            'property exists though it was not in config');
    }

    /**
     * Test get method, override a stored config value
     *
     * @return void
     */
    public function testGetOverrideStoredConfig()
    {
        $table = $this->CSTableLocatorWithConfig->get('Menus', ['ContextUser' => 'override']);
        $this->assertTrue($table->contextUser() === 'override',
            'The override value did not win over the stored value');
        $this->assertTrue($table->currentUser() === 'current user object',
            'config value that was not overridden was changed from its store value anyway');
    }

    /**
     * Test get method, override a stored config value
     *
     * @return void
     */
    public function testGetWithAdditionalConfigValue()
    {
        $table = $this->CSTableLocatorWithConfig->get('Menus', ['additional' => 'additional']);
        $this->assertTrue($table->additional === 'additional',
            'The additional config was not stored or was not accessible');
        $this->assertTrue($table->contextUser() === 'context user object',
            'stored config value was lost when adding a new config value');
    }
    /**
     * Test getInjectionKeys method
     *
     * @return void
     */
    public function testGetInjectionKeys()
    {
        $this->assertArraySubset(
            ['ContextUser', 'CurrentUser'],
            $this->CSTableLocatorWithConfig->getInjectionKeys(),
            false, 'unexpected keys returned');
        $this->assertArraySubset(
            [],
            $this->CSTableLocatorNoConfig->getInjectionKeys(),
            false, 'unexpected keys returned');
    }

    /**
     * Test getInjectionValue method
     *
     * @return void
     */
    public function testGetInjectionValue()
    {
        $this->assertTrue(
            $this->CSTableLocatorWithConfig->getInjectionValue('ContextUser') === 'context user object',
            false, 'unexpected value returned');
        $this->assertTrue(
            $this->CSTableLocatorNoConfig->getInjectionValue('ContextUser') === null,
            false, 'unexpected value returned');
    }

    /**
     * Test setInjection method
     *
     * @return void
     */
    public function testSetInjection()
    {
        $this->CSTableLocatorWithConfig->setInjection('additional', 'new value');
        $this->assertArraySubset(
            [
                'ContextUser' => 'context user object',
                'CurrentUser' => 'current user object',
                'additional' => 'new value'
            ],
            $this->CSTableLocatorWithConfig->getInjections(),
            false, 'store values were unexpected after injection of new value');
    }

    /**
     * Test getInjections method
     *
     * @return void
     */
    public function testGetInjections()
    {
        $this->assertArraySubset(
            [
                'ContextUser' => 'context user object',
                'CurrentUser' => 'current user object'
            ],
            $this->CSTableLocatorWithConfig->getInjections(),
            false, 'unexpected keys returned');
        $this->assertArraySubset(
            [],
            $this->CSTableLocatorNoConfig->getInjections(),
            false, 'unexpected keys returned');
    }

    /**
     * Test setInjections method
     *
     * @return void
     */
    public function testSetInjections()
    {
        $this->CSTableLocatorWithConfig->setInjections(['replacement' => 'replacement']);
        $this->assertArraySubset(
            [
                'replacement' => 'replacement'
            ],
            $this->CSTableLocatorWithConfig->getInjections(),
            false, 'The array was not replaced as requested');
    }
}
