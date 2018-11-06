<?php
namespace App\Test\TestCase\Lib;

use Cake\TestSuite\TestCase;
use App\Lib\Range;

/**
 * RangeTest
 * 
 * Range operates on user provided data so it better be bullet-proof
 *
 * @author dondrake
 */
class RangeTest  extends TestCase{
	
    public function setUp() {
        parent::setUp();
    }

    public function tearDown() {
        parent::tearDown();
    }

    /**
     * @dataProvider arrayToProvider
     */
    public function testArrayToString($source, $path, $string) {
        $this->assertSame($string, Range::arrayToString($source, $path));
    }
    
    public function arrayToProvider() {
        return [
            [[1], '', '1'],
            [[1,2], '', '1-2'],
            [[2,1], '', '1-2'],
            [[6,2,1], '', '1-2, 6'],
            [[1,2,6,9,10], '', '1-2, 6, 9-10'],
            
            // throws duplicate value in output string
//            [[[2,3],[5,6],[5,7]], '{n}.{n}', '2-3, 5, 5-7'], //FIX THIS ONE
            
            [[['id'=>3], ['id'=>5]], '{n}.id', '3, 5'],
        ];
    }

    /**
     * @dataProvider stringToProvider
     */
    public function testStringToArray($string, $array) {
        $this->assertTrue(array_values(
            Range::stringToArray($string)) === $array);
        $this->assertTrue(array_values(
            Range::stringToArray($string, 'array')) === $array);
        $this->assertTrue(array_values(
            Range::stringToArray($string, 'values')) === $array);
        $this->assertTrue(array_keys(
            Range::stringToArray($string, 'assoc')) === $array);
        $this->assertTrue(array_keys(
            Range::stringToArray($string, 'keys')) === $array);
    }
    
    public function stringToProvider() {
        return [
            ['1', [1]],
            ['1-3', [1,2,3]],
            ['1, 4-5', [1,4,5]],
            ['5-4, 1', [1,4,5]],
            ['3, 1-3',[1,2,3]],
            ['3, 1-3,99',[1,2,3,99]],
        ];
    }

    /**
     * @dataProvider validationProvider
     */
    public function testPatternValidation($expected, $string) {
        $this->assertSame($expected, Range::patternValidation($string));
    }
    
    public function validationProvider() {
        return [
            [TRUE, '1-3, 5'],       // normal range and value
            [TRUE, '99'],           // simple value
            [TRUE, '30, 1-7, 22'],  // mixed order values and ranges
            [TRUE, '7-4'],          // backwards range
            [TRUE, '32, 2, 44, 25'],// mixed order values
            [FALSE,'31-33-36'],     // double range
            [FALSE, ',,3, 4,, 6-8'],// missig values
            [FALSE, 'garbage value'],// garbage value
            [FALSE, '1-3, trash'],  // partial garbage
            
        ];
    }
}
