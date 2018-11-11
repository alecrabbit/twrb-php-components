<?php
/**
 * User: alec
 * Date: 11.11.18
 * Time: 15:42
 */

namespace Tests\Unit\StringList;

use AlecRabbit\StringList\StringList;
use PHPUnit\Framework\TestCase;

class StringListBasicTest extends TestCase
{
    private $list;

    public function simpleDataProvider(): array
    {
        return [
//            [$including, $excluding, $hasElement, $expected]
            [['par', 'lot'], ['trend', 'fix'], 'fix', false],
            [['par', 'lot'], ['trend', 'fix'], 'trend', false],
            [['par', 'lot'], ['trend', 'fix'], 'lot', true],
            [['par', 'lot'], ['trend', 'fix'], 'par', true],
            [['par', 'lot'], ['trend', 'fix'], 'tor', false],
            [[], ['trend', 'fix'], 'tor', true],
            [[], ['trend', 'fix'], 'fix', false],
            [[], [], 'fix', true],
            [['par'], [], 'fix', false],
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
        $this->list = new StringList();
        foreach ($including as $value) {
            $this->list->include($value);
        }
        foreach ($excluding as $value) {
            $this->list->exclude($value);
        }
        $this->assertEquals($expected, $this->list->has($element));
    }

    protected function tearDown()
    {
        unset($this->list);
    }
}

