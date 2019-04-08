<?php
namespace App\Test\TestCase\Lib;

use Cake\TestSuite\TestCase;
use App\Lib\Wildcard;

/**
 * RangeTest
 * 
 * Range operates on user provided data so it better be bullet-proof
 *
 * @author dondrake
 */
class WildcardTest  extends TestCase{
	
    public function setUp() {
        parent::setUp();
    }

    public function tearDown() {
        parent::tearDown();
    }

    /**
     * @dataProvider wildcardProvider
     */
    public function testAdd($expected, $string, $position)
    {
        $this->assertSame($expected, Wildcard::add($string, $position));
    }
    
    public function wildcardProvider() {
        return [
            ['tester', 'tester', 'wrongPosition'],
            ['%tester', 'tester', 'start'],
            ['% tester', ' tester', 'before'],
            ['% tester%', ' tester%', 'before'],
            ['tester-%', 'tester-', 'end'],
            [' tester %', ' tester ', 'after'],
            ['%tester%', 'tester', 'both'],
            ['%tester%', "tester", 'wrap'],
            ['%tester', '%tester', 'start'],
            ['%tester%', '%tester%', 'end'],
            ['%tes%ter%', '%tes%ter%', 'wrap'],
        ];
    }
    
    /**
     * @dataProvider addDelimProvider
     */
    public function testAddDelim($expected, $string, $position, $delimiter) {
        $this->assertEquals($expected, Wildcard::add($string, $position, $delimiter));
    }
    
    public function addDelimProvider() {
        return [
            ['*some test ', 'some test ', 'start', '*'],
            ['/test words/', 'test words', 'wrap', '/'],
            ['& tester', ' tester', 'before', '&&'],
            ['$ tester%', '$ tester%', 'before', '$$'],
        ];
    }
    
    /**
     * @dataProvider beforeProvider
     */
    public function testBefore($expected, $string) {
        $this->assertEquals($expected, Wildcard::before($string));
    }
    
    public function beforeProvider() {
        return [
            ['%tester', 'tester'],
            ['%tes%ter', 'tes%ter'],
            ['% tester', ' tester'],
            ['% tester%', ' tester%'],
            ['%tester', '%tester'],
        ];
    }
    
    /**
     * @dataProvider beforeDelimProvider
     */
    public function testBeforeDelim($expected, $string, $delimiter) {
        $this->assertEquals($expected, Wildcard::before($string, $delimiter));
    }
    
    public function beforeDelimProvider() {
        return [
            ['^', '^', '^^^'],
            ['# some words here', ' some words here', '#']
        ];
    }
    
    /**
     * @dataProvider afterProvider
     */
    public function testAfter($expected, $string) {
        $this->assertEquals($expected, Wildcard::after($string));
    }
    
    public function afterProvider() {
        return [
            ['tester-%', 'tester-', 'end'],
            [' tester %', ' tester ', 'after'],
            ['%tester%', '%tester%', 'end'],
        ];
    }
    
    /**
     * @dataProvider afterDelimProvider
     */
    public function testAfterDelim($expected, $string, $delimiter) {
        $this->assertEquals($expected, Wildcard::after($string, $delimiter));
    }
    
    public function afterDelimProvider() {
        return [
            ['^', '^', '^^^'],
            [' some words here#', ' some words here', '#']
        ];
    }
    
    /**
     * @dataProvider wrapProvider
     */
    public function testWrap($expected, $string) {
        $this->assertSame($expected, Wildcard::wrap($string));
    }
    
    public function wrapProvider() {
        return [
            ['%tester%', 'tester', 'both'],
            ['%tester%', "tester", 'wrap'],
            ['%tester .%', "tester .%", 'wrap'],
            ['%tes%ter%', '%tes%ter%', 'wrap'],
        ];
    }
    
    /**
     * @dataProvider wrapDelimProvider
     */
    public function testWrapeDelim($expected, $string, $delimiter) {
        $this->assertEquals($expected, Wildcard::wrap($string, $delimiter));
    }
    
    public function wrapDelimProvider() {
        return [
            ['^^', '^', '^^^'],
            ['# some words here#', ' some words here', '#']
        ];
    }
    
}
