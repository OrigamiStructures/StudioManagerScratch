<?php
namespace App\Test\TestCase\Lib;

use App\Lib\RenumberRequest;
use Cake\TestSuite\TestCase;

/**
 * App\Lib\RenumberRequest Test Case
 */
class RenumberRequestTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Lib\RenumberRequest
     */
    public $RenumberRequest;

    /**
     * Test __construct method
     *
     * @return void
     */
    public function testConstruct()
    {
        $req = new RenumberRequest(3,5);
        $this->assertEquals(3, $req->oldNum(),
            'When constructing with two integers, the first one '
            . 'did not become the old number');
        $this->assertEquals(5, $req->newNum(),
            'When constructing with two integers, the second one '
            . 'did not become the new number');
        
        $mess = implode(' ' , $req->message());
        $match = stristr($mess, "Can't change multiple pieces");

        $this->assertFalse(is_string($match),
            'Setting a new valid request produced some error message');
        
        $match = stristr($mess, "Change piece #");

        $this->assertTrue(is_string($match),
            'A new valid request should allow 
                a \'change #x to #y\' message');
        
        $req = new RenumberRequest(3,null);
        $this->assertNull($req->newNum(),
            'When constructing with and integers and a null, the '
            . 'null did not become the new number');
        
        $mess = implode(' ' , $req->message());
        $match = stristr($mess, "but no new number was ");

        $this->assertTrue(is_string($match),
            'Setting request with null new number should produced an error message');
        
        $match = stristr($mess, "Change piece #");

        $this->assertFalse(is_string($match),
            'A new request with a null new number shouldn\'t allow 
                a \'change #x to #y\' message');

    }

    /**
     * Test __get method
     *
     * @return void
     */
//    public function testGet()
//    {
//        $this->markTestIncomplete('Not implemented yet.');
//    }

    /**
     * Test duplicate method
     *
     * @return void
     */
    public function testDuplicate()
    {
        $req = new RenumberRequest(3,5);
        $req->duplicate(4);
        $mess = implode(' ' , $req->message());
        $match = stristr($mess, "Can't change multiple pieces");

        $this->assertTrue(is_string($match),
            'Setting duplicate() to a positive integer >1 did not '
            . 'create the expected error message');
        
        $match = stristr($mess, "Change piece #3 to");

        $this->assertFalse(is_string($match),
            'Setting duplicate() to > 1 shouldn\'t allow 
                a \'change #x to #y\' message');
        
        $req->duplicate(1);
        $mess = implode(' ' , $req->message());
        $match = stristr($mess, "Can't change multiple pieces");

        $this->assertFalse(is_string($match),
            'Setting duplicate() to 1 did still produced some error message');
        
        $match = stristr($mess, "Change piece #3 to");

        $this->assertTrue(is_string($match),
            'Setting duplicate() to 1 should allow 
                a \'change #x to #y\' message');
    }

    /**
     * Test bad_number method
     *
     * @return void
     */
    public function testBadNumber()
    {
        $req = new RenumberRequest(3,null);
        $req->badNumber(TRUE);
        $mess = implode(' ' , $req->message());
        $match = stristr($mess, "but no new number");

        $this->assertTrue(is_string($match),
            'Having no new number and setting bad_number to true '
            . 'did not generate the expected error message');
        
        $match = stristr($mess, "Change piece #3 to");

        $this->assertFalse(is_string($match),
            'Setting bad_number() to a TRUE when there is no new 
                number shouldn\'t allow a \'change #x to #y\' message');

        $req = new RenumberRequest(3,5);
        $req->badNumber(TRUE);
        $mess = implode(' ' , $req->message());
        $match = stristr($mess, "There is no #");

        $this->assertTrue(is_string($match),
            'Having a new number and setting bad_number to true '
            . 'did not generated the expected error message.');
        
        $match = stristr($mess, "Change piece #3 to");

        $this->assertFalse(is_string($match),
            'Setting bad_number() to a TRUE shouldn\'t allow 
                a \'change #x to #y\' message');

        $req->badNumber(FALSE);
        $mess = implode(' ' , $req->message());
        $match = stristr($mess, "There is no #");

        $this->assertFalse(is_string($match),
            'setting bad_number to False did not eliminate the '
            . 'bad number error message');

        $match = stristr($mess, "Change piece #3 to");

        $this->assertTrue(is_string($match),
            'Setting bad_number() to a FALSE should allow 
                a \'change #x to #y\' message');
    }

    /**
     * Test vague_receiver method
     *
     * @return void
     */
    public function testVagueReceiver()
    {
        $req = new RenumberRequest(3,5);
        $req->vagueReceiver(TRUE);
        $mess = implode(' ' , $req->message());
        $match = stristr($mess, "Can't determine which piece ");

        $this->assertTrue(is_string($match),
            'Setting vague_receiver() to a TRUE did not '
            . 'create the expected error message');

        $match = stristr($mess, "Change piece #3 to");

        $this->assertTrue(is_string($match),
            'Setting vague_receiver() to a TRUE should allow 
                a \'change #x to #y\' message');

        $req->vagueReceiver(FALSE);
        $mess = implode(' ' , $req->message());
        $match = stristr($mess, "Can't determine which piece ");

        $this->assertFalse(is_string($match),
            'Setting vague_receiver() to FALSE '
            . 'still produced a \'vauge\' error message');

        $match = stristr($mess, "Change piece #3 to");

        $this->assertTrue(is_string($match),
            'Setting vague_receiver() to a false should allow 
                a \'change #x to #y\' message');
    }

    /**
     * Test vague_provider method
     *
     * @return void
     */
//    public function testVagueProvider()
//    {
//        $this->markTestIncomplete('Not implemented yet.');
//    }

    /**
     * Test message method
     *
     * @return void
     */
    public function testMessage()
    {
        // Going to assume the individual flag test did 
		// an adiquate job of testing the message method
    }

    /**
     * Test implied method
     *
     * @return void
     */
    public function testImplied()
    {
        //Other changes implied the change
        $req = new RenumberRequest(3,5);
        $mess = implode(' ' , $req->message());
        $match = stristr($mess, "Other changes implied the change");
        
        $this->assertFalse(is_string($match),
            'Creating a new request wrongly assumes the request is implied.');

        $match = stristr($mess, "Change piece #3 to");

        $this->assertTrue(is_string($match),
            'Setting implied() to a false should allow 
                a \'change #x to #y\' message');

        $req->implied(TRUE);
        $mess = implode(' ' , $req->message());
        $match = stristr($mess, "Other changes implied the change");
        
        $this->assertTrue(is_string($match),
            'Setting implied() to true does not create the expected message.');

        $match = stristr($mess, "Change piece #3 to");

        $this->assertFalse(is_string($match),
            'Setting implied() to a true shouldn\'t allow 
                a \'change #x to #y\' message');
    }
}
