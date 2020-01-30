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
//        debug($this->PreferencesForm->prefsSchema);
//        $this->replaceSchemaWithTestSchema();
    }

    protected function replaceSchemaWithTestSchema()
    {
        $originalColumns = $this->PreferencesForm->schema()->fields();
        foreach ($originalColumns as $originalColumn) {
            $this->PreferencesForm->schema()->removeField($originalColumn);
        }
        $testSchema = [
            'pagination.limit' => [
                'type' => 'integer',
                'default' => (int) 10,
                'length' => null,
                'precision' => null
            ],
            'pagination.sort.people' => [
                'type' => 'string',
                'default' => 'last_name',
                'length' => null,
                'precision' => null
            ],
            'pagination.sort.artwork' => [
                'type' => 'string',
                'default' => 'title',
                'length' => null,
                'precision' => null
            ],
            'id' => [
                'type' => 'string',
                'length' => null,
                'precision' => null,
                'default' => null
            ]
        ];
        $this->PreferencesForm->schema()->addFields($testSchema);
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
//        $expectedDefaults = [
//            'pagination.sort.people' => 'last_name',
//            'pagination.sort.category' => 'last_name',
//            'pagination.sort.organization' => 'last_name'
//        ];
//
//        $expectedPaths =[
//            'pagination.sort.people',
//            'pagination.sort.category',
//            'pagination.sort.organization'
//        ];

        $this->assertTrue(is_array($this->PreferencesForm->getDefaults()),
            'defaults did not construct as expected');
        $this->assertTrue(is_array($this->PreferencesForm->getValidPaths()),
            'paths did not construct as expexted');
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
        $this->PreferencesForm->asContext('AA074ebc-758b-4729-91f3-bcd65e51ace4' , ['pagination.sort.people' => 'first_name']);
        $actual = $this->PreferencesForm->schema()->field('pagination.sort.people');

        $this->assertEquals('first_name', $actual['default'],
            'The schema did not alter to reflect user pref settings');
    }
}
