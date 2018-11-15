<?php
/**
 * User: alec
 * Date: 09.11.18
 * Time: 16:06
 */

namespace AlecRabbit\Money;


use AlecRabbit\Money\Calculator\BcMathCalculator;
use AlecRabbit\Money\Contracts\CalculatorInterface;

class CalculatorFactory
{
    /** @var array */
    private static $calculators = [
        BcMathCalculator::class,
    ];

    /** @var CalculatorInterface */
    private static $calculator;

    public static function getCalculator(): CalculatorInterface
    {
        if (null !== self::$calculator) {
            return self::$calculator;
        }
        foreach (self::$calculators as $calculator) {
            /** @var CalculatorInterface $calculator */
            if ($calculator::supported()) {
                return
                    self::$calculator = new $calculator();
            }
        }

        throw new \RuntimeException('Cannot find calculator for money calculations.');
    }

    /**
     * @param string $calculator
     * @return int
     */
    public static function registerCalculator($calculator): int
    {
        if (is_a($calculator, CalculatorInterface::class, true) === false) {
            throw new \InvalidArgumentException('Calculator must implement [' . CalculatorInterface::class . '].');
        }
        return
            array_unshift(self::$calculators, $calculator);
    }


}