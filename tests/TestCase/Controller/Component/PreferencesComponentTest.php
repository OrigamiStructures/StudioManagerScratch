<?php
namespace App\Test\TestCase\Controller\Component;

use App\Controller\Component\PreferencesComponent;
use Cake\Controller\Controller;
use Cake\Controller\ComponentRegistry;
use Cake\Event\Event;
use Cake\Http\ServerRequest;
use Cake\Http\Response;
use Cake\TestSuite\TestCase;

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
        // Setup our component and fake test controller
//        $event = new Event('Controller.startup', $this->controller);
//        $this->PreferencesComponent->startup($event);
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

        $actual = $this->Component->getController()->ViewBuilder()->getHelpers();
        $this->assertEquals(['Preferences'], $actual,
            'The Preferences helper did not get loaded to the view during intialization');
    }

    /**
     * Test setPrefs method
     *
     * @return void
     */
    public function testSetPrefsClean()
    {
        $post = [
            'paginate' => [
                'limit' => '10',
                'sort' => [
                    'people' => 'middle_name'
                ]
            ],
            'id' => 'AA2f9b46-345f-4c6f-9637-060ceacb21b2'
        ];
        $request = new ServerRequest(['post' => $post]);
        $response = new Response();
        $this->controller = $this->getMockBuilder('Cake\Controller\Controller')
            ->setConstructorArgs([$request, $response])
            ->setMethods(null)
            ->getMock();
        $registry = new ComponentRegistry($this->controller);
        $this->Component = new PreferencesComponent($registry);

        $this->Component->setPrefs();

        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test clearPrefs method
     *
     * @return void
     */
    public function testClearPrefs()
    {
        $this->markTestIncomplete('Not implemented yet.');
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
