<?php
namespace App\Test\TestCase\Form;

use App\Form\LocalPreferencesForm;
use Cake\TestSuite\TestCase;

/**
 * App\Form\LocalPreferencesForm Test Case
 *
 * The heaving lifting is in PreferencesForm. LocalPreferencesForm adds the
 * prefs schema and validation. Neither is testable alone without work mocking
 * the schema (which I wasn't prepared to do). So they are tested as one.
 *
 * This two part construction is to prepare this system to work as a PlugIn.
 * Later, if we do that, we'll have to fix the testing.
 * 
 */
class LocalPreferencesFormTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Form\LocalPreferencesForm
     */
    public $PreferencesForm;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.preferences',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->PreferencesForm = new LocalPreferencesForm();

    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->PreferencesForm);

        parent::tearDown();
    }

    /**
     * Test __construct method
     *
     * @return void
     */
    public function testConstruct()
    {
        $expectedDefaults = [
            'paginate.limit' => (int) 10,
            'paginate.sort.people' => 'last_name',
            'paginate.sort.artwork' => 'title'
        ];

        $expectedPaths =[
            'paginate.limit',
            'paginate.sort.people',
            'paginate.sort.artwork'
        ];

        $this->assertEquals($expectedDefaults, $this->PreferencesForm->getDefaults(),
            'defaults did not construct as expected');
        $this->assertEquals($expectedPaths, $this->PreferencesForm->getValidPaths(),
            'paths did not construct as expexted');
    }

    /**
     * Test getUsersPrefsEntity method
     *
     * Tests return of an entity that has been persisted with
     * only valid preference paths referenced.
     *
     * This is an integration test since the form class must modify
     * the entity.
     *
     * @return void
     */
    public function testGetUsersPrefsEntityAllValidPrefs()
    {
        $prefsEntity = $this->PreferencesForm->getUsersPrefsEntity('AA074ebc-758b-4729-91f3-bcd65e51ace4');
        $this->assertInstanceOf('App\Model\Entity\Preference', $prefsEntity,
            'Did not get a Preference entity');
        $this->assertEquals('10', $prefsEntity->for('paginate.limit'),
            'an unexpected value came from the entity when a default was expected');
        $this->assertEquals('first_name', $prefsEntity->for('paginate.sort.people'),
            'an unexpected value came from the entity when a user variant was expected');

        $expected = [
            'paginate' => [
                'sort' => [
                    'people' => 'first_name'
                ]
            ]
        ];
        $this->assertEquals($expected, $prefsEntity->getVariants(),
            'The variant list unexpectedly changed during the "get" process');
    }

    /**
     * This tests the components ability to remove an invalid preferece path
     * from the entity by filtering against the schema
     *
     * This is an integration test
     */
    public function testGetUsersPrefsEntityInvalidPrefs()
    {
        $prefsEntity = $this->PreferencesForm->getUsersPrefsEntity('BB074ebc-758b-4729-91f3-bcd65e51ace4');
        $expected = [
            'paginate' => [
                'sort' => [
                    'people' => 'first_name'
                ]
            ]
        ];
        $this->assertEquals($expected, $prefsEntity->getVariants(),
            'The variant list did not get the invalid path removed');
    }

    /**
     * Test asContext method
     *
     * This integration test verifies that the entity loads and
     * modifies the form object's schema to reflect the user variant values
     *
     * @return void
     */
    public function testAsContext()
    {
        $this->PreferencesForm->asContext('AA074ebc-758b-4729-91f3-bcd65e51ace4');
        $actual = $this->PreferencesForm->schema()->field('paginate.sort.people');

        $this->assertEquals('first_name', $actual['default'],
            'The schema did not alter to reflect user pref settings');
    }
}
