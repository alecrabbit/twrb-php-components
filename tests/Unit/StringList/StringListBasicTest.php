<?php
/**
 * Date: 11.11.18
 * Time: 15:42
 */

namespace Tests\Unit\StringList;

use AlecRabbit\StringList\StringList;
use PHPUnit\Framework\TestCase;

class StringListBasicTest extends TestCase
{
    /** @var StringList */
    private $list;

    public function simpleDataProvider(): array
    {
        return [
//            [$including, $excluding, $hasElement, $expected]
            [['par', 'fix'], ['trend', 'fix'], 'fix', false],
            [['par', 'lot'], ['trend', 'fix'], 'trend', false],
            [['par', 'lot'], ['trend', 'fix'], 'lot', true],
            [['par', 'lot'], ['trend', 'fix'], 'par', true],
            [['par', 'lot'], ['trend', 'fix'], 'tor', false],
            [[], ['trend', 'fix'], 'tor', true],
            [[], ['trend', 'fix'], 'fix', false],
            [[], [], 'fix', true],
            [['par'], [], 'fix', false],
            [['fix'], ['fix'], 'fix', false],
        ];
    }

    /**
     * @test
     * @dataProvider simpleDataProvider
     * @param $including
     * @param $excluding
     * @param $hasElement
     * @param $expected
     */
    public function simple($including, $excluding, $hasElement, $expected): void
    {
        $this->list = new StringList($including, $excluding);
        $this->assertEquals($expected, $this->list->has($hasElement));
    }

    /**
     * @test
     * @dataProvider simpleDataProvider
     * @param $including
     * @param $excluding
     * @param $element
     * @param $expected
     */
    public function usingMethods($including, $excluding, $element, $expected): void
    {
        foreach ($including as $value) {
            $this->list->include($value);
        }
        foreach ($excluding as $value) {
            $this->list->exclude($value);
        }
        $this->assertEquals($expected, $this->list->has($element));
    }

    /**
     * @test
     * @dataProvider simpleDataProviderExcludeFirst
     * @param $including
     * @param $excluding
     * @param $element
     * @param $expected
     */
    public function usingMethodsExcludeFirst($including, $excluding, $element, $expected): void
    {
        foreach ($excluding as $value) {
            $this->list->exclude($value);
        }
        foreach ($including as $value) {
            $this->list->include($value);
        }
        $this->assertEquals($expected, $this->list->has($element));
    }


    public function simpleDataProviderExcludeFirst(): array
    {
        return [
//            [$including, $excluding, $hasElement, $expected]
            [['par', 'fix'], ['trend', 'fix'], 'fix', true],
            [['par', 'trend'], ['trend', 'fix'], 'trend', true],
            [['par', 'lot'], ['trend', 'fix'], 'lot', true],
            [['lot'], ['par', 'fix'], 'par', false],
            [['par', 'lot'], ['trend', 'lot'], 'tor', false],
            [[], ['trend', 'fix'], 'tor', true],
            [[], ['trend', 'fix'], 'fix', false],
            [[], [], 'fix', true],
            [['par'], ['fix'], 'fix', false],
            [['fix'], ['fix'], 'fix', true],
            [['fix'], [], 'fix', true],
        ];
    }

    protected function setUp()
    {
        $this->list = new StringList();
    }

    protected function tearDown()
    {
        unset($this->list);
    }
}

