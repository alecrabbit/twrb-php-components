<?php
/**
 * User: alec
 * Date: 09.11.18
 * Time: 16:16
 */

namespace Tests\Unit\Money;


use AlecRabbit\Money\Calculator\BcMathCalculator;
use AlecRabbit\Money\CalculatorFactory;
use PHPUnit\Framework\TestCase;

class CalculatorFactoryTest extends TestCase
{
    /**
     * @test
     */
    public function throwsAnExceptionWhenCalculatorIsInvalid(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        CalculatorFactory::registerCalculator('InvalidCalculator');
    }

    /**
     * @test
     */
    public function addsCalculator(): void
    {
        $this->assertEquals(2, CalculatorFactory::registerCalculator(BcMathCalculator::class));
    }
}