<?php
/**
 * User: alec
 * Date: 09.11.18
 * Time: 16:16
 */

namespace Tests\Unit\Money;


use AlecRabbit\Money\CalculatorFactory;
use AlecRabbit\Money\Contracts\CalculatorInterface;
use PHPUnit\Framework\TestCase;

class CalculatorFactorySecondTest extends TestCase
{
    public static $backupCalculator;
    public static $backupCalculators;

    /** @var \ReflectionProperty */
    public static $calculator;
    /** @var \ReflectionProperty */
    public static $calculators;

    /**
     * @test
     */
    public function throwsAnExceptionWhenCalculatorIsInvalidaddsCalculator(): void
    {
        $this->expectException(\RuntimeException::class);
        CalculatorFactory::getCalculator();
    }

    /**
     * @test
     */
    public function CalculatorIsValid(): void
    {
        static::$calculators->setValue(static::$backupCalculators);
        /** @noinspection UnnecessaryAssertionInspection */
        $this->assertInstanceOf(CalculatorInterface::class, CalculatorFactory::getCalculator());
    }

    /**
     * @throws \ReflectionException
     */
    protected function setUp(): void
    {
        $reflection = new \ReflectionClass(CalculatorFactory::class);

        static::$calculator = $reflection->getProperty('calculator');
        static::$calculator->setAccessible(true);
        static::$backupCalculator = static::$calculator->getValue();
        static::$calculator->setValue(null);

        static::$calculators = $reflection->getProperty('calculators');
        static::$calculators->setAccessible(true);
        static::$backupCalculators = static::$calculators->getValue();
        static::$calculators->setValue([]);
    }

    protected function tearDown()
    {
        static::$calculator->setValue(static::$backupCalculator);
        static::$calculators->setValue(static::$backupCalculators);
    }


}