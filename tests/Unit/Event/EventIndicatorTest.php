<?php
/**
 * User: alec
 * Date: 18.11.18
 * Time: 16:39
 */

namespace Tests\Unit\Event;

use AlecRabbit\Event\EventIndicator;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\PhpUnit\ClockMock;

/**
 * @group time-sensitive
 */
class EventIndicatorTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        ClockMock::register(EventIndicator::class);
        ClockMock::withClockMock(true);
    }

    public static function tearDownAfterClass():void
    {
        ClockMock::withClockMock(false);
    }


    /**
     * @test
     * @throws \ReflectionException
     */
    public function current(): void
    {
        $method = new \ReflectionMethod(EventIndicator::class, 'current');
        $method->setAccessible(true);

        $object = new EventIndicator();

        $this->assertInternalType('integer', $method->invoke($object));
        unset($method, $object);
    }

    /**
     * @test
     * @dataProvider countEventDataProvider
     * @param $event
     */
    public function currentEvent($event): void
    {
        $object = new EventIndicator();
        $this->assertEquals($event, $object->countEvent($event));
    }

    /**
     * @test
     */
    public function isOk(): void
    {
        $object = new EventIndicator();
        $this->assertTrue($object->isOk());
        $this->assertFalse($object->isNotOk());
    }

    /**
     * @test
     */
    public function isOkTwo(): void
    {
//        time();
        $object = new EventIndicator(2);
        $this->assertTrue($object->isOk());
        $this->assertFalse($object->isNotOk());
//        dump(time());
        sleep(3);
//        dump(time());
        $this->assertFalse($object->isOk());
        $this->assertTrue($object->isNotOk());
    }

    public function countEventDataProvider(): array
    {
        return [
            [null],
            [new \stdClass()],
            [1],
            ['string'],
            [0.113],
            [[]],
        ];
    }

}
