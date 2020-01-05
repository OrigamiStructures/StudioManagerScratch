<?php
namespace App\Test\TestCase\Controller\Component;

use App\Controller\Component\PreferencesComponent;
use App\Form\PreferencesForm;
use Cake\Controller\Controller;
use Cake\Controller\ComponentRegistry;
use Cake\Event\Event;
use Cake\Form\Schema;
use Cake\Http\ServerRequest;
use Cake\Http\Response;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Cake\Validation\Validator;

/**
 * App\Controller\Component\PreferencesComponent Test Case
 */
class PreferencesComponentTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Controller\Component\PreferencesComponent
     */
    public $Component;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.preferences',
        'app.users',
    ];

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
        unset($this->Component);

        parent::tearDown();
    }

    public function mockPrefsTable()
    {
        $tableMock = $this->getMockForModel('Preferences', ['save']);
        $tableMock->expects($this->once())->method('save')->willReturn(false);
        TableRegistry::getTableLocator()->set('Preferences', $tableMock);
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize()
    {
        $request = new ServerRequest();
        $response = new Response();
        $this->controller = $this->getMockBuilder('Cake\Controller\Controller')
            ->setConstructorArgs([$request, $response])
            ->setMethods(null)
            ->getMock();
        $registry = new ComponentRegistry($this->controller);
        $this->Component = new PreferencesComponent($registry);

        //this swaps in a form with a stable schema for testing
        //defined in this file below
        $this->Component->setFormClass('App\Test\TestCase\Controller\Component\TestPrefForm');

        $actual = $this->Component->getController()->ViewBuilder()->getHelpers();
        $this->assertEquals(['Preferences'], $actual,
            'The Preferences helper did not get loaded to the view during intialization');
    }

    /**
     * Test setPrefs method
     *
     * Checks the basic rule:
     *  change from default to variant adds entry to ->prefs
     *  change from variant to default removes entry from -prefs
     *  either change is reflected in proper ->for(path) return value
     *
     * @return void
     */
    public function testSetPrefsClean()
    {
        $user_id = 'AA074ebc-758b-4729-91f3-bcd65e51ace4';
        $post = [
            'pagination' => [
                'limit' => '15',
                'sort' => [
                    'people' => 'last_name'
                ]
            ],
            'id' => $user_id
        ];

        //<editor-fold desc="setup">
        $request = new ServerRequest(['post' => $post]);
        $response = new Response();
        $this->controller = $this->getMockBuilder('Cake\Controller\Controller')
            ->setConstructorArgs([$request, $response])
            ->setMethods(null)
            ->getMock();
        $registry = new ComponentRegistry($this->controller);
        $this->Component = new PreferencesComponent($registry);

        //this swaps in a form with a stable schema for testing
        //defined in this file below
        $this->Component->setFormClass('App\Test\TestCase\Controller\Component\TestPrefForm');
        //</editor-fold>

        $this->Component->setPrefs();

        $changedPrefs = $this->Component->getUserPrefsEntity($user_id);

        $this->assertEquals(15, $changedPrefs->for('pagination.limit'),
            'A new user variant value was not set to the prefs list');
        $this->assertEquals('last_name', $changedPrefs->for('pagination.sort.people'),
            'User variant did not become default value as requested in post');

        $this->assertEquals(null, $changedPrefs->getVariant('pagination.sort.people'),
            'although the pref is set to default it still appears in list of variants');
    }

    /**
     * Test setPrefs method
     *
     * Insure that posted data that is not listed in the form schema
     * does not make its way into the stored pref-variants
     *
     * @return void
     */
    public function testSetPrefsExtraPostData()
    {
        $user_id = 'AA074ebc-758b-4729-91f3-bcd65e51ace4';
        $post = [
            'pagination' => [
                'limit' => '10',
                'sort' => [
                    'people' => 'last_name'
                ],
                'non_schema' => 'value'
            ],
            'non_schema' => 'value',
            'id' => $user_id
        ];

        //<editor-fold desc="setup">
        $request = new ServerRequest(['post' => $post]);
        $response = new Response();
        $this->controller = $this->getMockBuilder('Cake\Controller\Controller')
            ->setConstructorArgs([$request, $response])
            ->setMethods(null)
            ->getMock();
        $registry = new ComponentRegistry($this->controller);
        $this->Component = new PreferencesComponent($registry);

        //this swaps in a form with a stable schema for testing
        //defined in this file below
        $this->Component->setFormClass('App\Test\TestCase\Controller\Component\TestPrefForm');
        //</editor-fold>

        $this->Component->setPrefs();

        $changedPrefs = $this->Component->getUserPrefsEntity($user_id);

        $this->assertEmpty($changedPrefs->getVariants(),
            'unexpected values are listed in the prefs.');
    }

    /**
     * Test setPrefs method
     *
     * Insure that posted data that is not listed in the form schema
     * does not make its way into the stored pref-variants
     *
     * @return void
     */
    public function testSetPrefsFailedSave()
    {
        $user_id = 'AA074ebc-758b-4729-91f3-bcd65e51ace4';
        $post = [
            'pagination' => [
                'limit' => '10',
                'sort' => [
                    'people' => 'last_name'
                ]
            ],
            'id' => $user_id
        ];

        //<editor-fold desc="setup">
        $request = new ServerRequest(['post' => $post]);
        $response = new Response();
        $this->controller = $this->getMockBuilder('Cake\Controller\Controller')
            ->setConstructorArgs([$request, $response])
            ->setMethods(null)
            ->getMock();
        $registry = new ComponentRegistry($this->controller);
        $this->Component = new PreferencesComponent($registry);

        //this swaps in a form with a stable schema for testing
        //defined in this file below
        $this->Component->setFormClass('App\Test\TestCase\Controller\Component\TestPrefForm');
        //mocks table with a failed save
        $this->mockPrefsTable();
        //</editor-fold>

        $originalVariants = $this->Component->getUserPrefsEntity($user_id)->getVariants();

        $this->Component->setPrefs();

        $changedPrefs = $this->Component->getUserPrefsEntity($user_id);

        $this->assertEquals($originalVariants, $changedPrefs->getVariants(),
            'No prefs change were expected on failed save, but one occured');

    }

    /**
     * Test clearPrefs method
     *
     * @return void
     */
    public function testClearPrefs()
    {
        $user_id = 'AA074ebc-758b-4729-91f3-bcd65e51ace4';

        $request = new ServerRequest();
        $response = new Response();
        $this->controller = $this->getMockBuilder('Cake\Controller\Controller')
            ->setConstructorArgs([$request, $response])
            ->setMethods(null)
            ->getMock();
        $registry = new ComponentRegistry($this->controller);
        $this->Component = new PreferencesComponent($registry);

        //this swaps in a form with a stable schema for testing
        //defined in this file below
        $this->Component->setFormClass('App\Test\TestCase\Controller\Component\TestPrefForm');

        $this->Component->clearPrefs($user_id);

        $changedPrefs = $this->Component->getUserPrefsEntity($user_id);

        $this->assertEmpty($changedPrefs->getVariants(),
            '"clearPrefs()" did not remove the stored user variants in ->prefs');
    }


    /**
     * Test setFormClass method
     *
     * @return void
     */
    public function testSetFormClass()
    {
        $request = new ServerRequest();
        $response = new Response();
        $this->controller = $this->getMockBuilder('Cake\Controller\Controller')
            ->setConstructorArgs([$request, $response])
            ->setMethods(null)
            ->getMock();
        $registry = new ComponentRegistry($this->controller);
        $this->Component = new PreferencesComponent($registry);

        $this->Component->setFormClass('App\Form\PreferencesForm');
        $object = $this->Component->getFormObjet();
        $this->assertInstanceOf('App\Form\PreferencesForm', $object,
        'changing the registred Form class did not change the class of the instantiated object');
        $this->assertNotInstanceOf('App\Form\LocalPreferencesForm', $object,
            'changing the registred Form class did not change the class of the instantiated object');
    }
}

/**
 * Class TestPrefForm
 * @package App\Test\TestCase\Controller\Component
 */
class TestPrefForm extends PreferencesForm
{

    /**
     * @param Schema $schema
     * @return Schema
     */
    protected function _buildSchema(Schema $schema)
    {
        return $schema
            ->addField(
                'pagination.limit', [
                'type' => 'integer',
                'default' => 10
            ])
            ->addField('pagination.sort.people', [
                'type' => 'string',
                'default' => 'last_name'
            ])
            ->addField('pagination.sort.artwork', [
                'type' => 'string',
                'default' => 'title'
            ])
            ->addField('id', [
                'type' => 'string'
            ]);
    }

    /**
     * @param Validator $validator
     * @return Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator->requirePresence('id');
        return $validator;
    }

}

