<?php
/**
 * User: alec
 * Date: 05.11.18
 * Time: 23:51
 */

namespace AlecRabbit\Money;


use AlecRabbit\Money\Calculator\BcMathCalculator;
use AlecRabbit\Money\Contracts\Calculator;

/**
 * Money Value Object.
 *
 * @author Mathias Verraes
 */
class Money implements \JsonSerializable
{
    use MoneyFactory;

    public const ROUND_HALF_UP = PHP_ROUND_HALF_UP;
    public const ROUND_HALF_DOWN = PHP_ROUND_HALF_DOWN;

    /** @var Calculator */
    private static $calculator;

    /** @var array */
    private static $calculators = [
        BcMathCalculator::class,
    ];

    /** @var string */
    private $amount;

    /** @var Currency */
    private $currency;

    /**
     * @param int|float|string $amount
     * @param Currency $currency
     *
     * @throws \InvalidArgumentException If amount is not integer
     */
    public function __construct($amount, Currency $currency)
    {
        if (null === $amount) {
            $amount = 0;
        }
        if (!\is_numeric($amount)) {
            throw new \InvalidArgumentException('Amount must be int|float|string');
        }
        $this->amount = trim_zeros($amount);
        $this->currency = $currency;
    }

    /**
     * @param Money $first
     * @param Money[] $collection
     *
     * @return Money
     */
    public static function min(Money $first, Money ...$collection): Money
    {
        $min = $first;

        foreach ($collection as $money) {
            if ($money->lessThan($min)) {
                $min = $money;
            }
        }

        return $min;
    }

    /**
     * Checks whether the value represented by this object is less than the other.
     *
     * @param Money $other
     *
     * @return bool
     */
    public function lessThan(Money $other): bool
    {
        return $this->compare($other) === -1;
    }

    /**
     * Returns an integer less than, equal to, or greater than zero
     * if the value of this object is considered to be respectively
     * less than, equal to, or greater than the other.
     *
     * @param Money $other
     *
     * @return int
     */
    public function compare(Money $other): int
    {
        $this->assertSameCurrency($other);

        return $this->getCalculator()->compare($this->amount, $other->amount);
    }

    /**
     * Asserts that a Money has the same currency as this.
     *
     * @param Money $other
     *
     * @throws \InvalidArgumentException If $other has a different currency
     */
    private function assertSameCurrency(Money $other): void
    {
        if (!$this->isSameCurrency($other)) {
            throw new \InvalidArgumentException('Currencies must be identical.');
        }
    }

    /**
     * Checks whether a Money has the same Currency as this.
     *
     * @param Money $other
     *
     * @return bool
     */
    public function isSameCurrency(Money $other): bool
    {
        return $this->currency->equals($other->currency);
    }

    /**
     * @return Calculator
     */
    private function getCalculator(): Calculator
    {
        if (null === self::$calculator) {
            self::$calculator = self::initializeCalculator();
        }

        return self::$calculator;
    }

    /**
     * @return Calculator
     *
     * @throws \RuntimeException If cannot find calculator for money calculations
     */
    private static function initializeCalculator(): Calculator
    {
        foreach (self::$calculators as $calculator) {
            /** @var Calculator $calculator */
            if ($calculator::supported()) {
                return new $calculator();
            }
        }

        throw new \RuntimeException('Cannot find calculator for money calculations.');
    }

    /**
     * @param Money $first
     * @param Money ...$collection
     *
     * @return Money
     */
    public static function max(Money $first, Money ...$collection): Money
    {
        $max = $first;

        foreach ($collection as $money) {
            if ($money->greaterThan($max)) {
                $max = $money;
            }
        }

        return $max;
    }

    /**
     * Checks whether the value represented by this object is greater than the other.
     *
     * @param Money $other
     *
     * @return bool
     */
    public function greaterThan(Money $other): bool
    {
        return $this->compare($other) === 1;
    }

    /**
     * @param Money $first
     * @param Money ...$collection
     *
     * @return Money
     */
    public static function sum(Money $first, Money ...$collection): Money
    {
        return $first->add(...$collection);
    }

    /**
     * Returns a new Money object that represents
     * the sum of this and an other Money object.
     *
     * @param Money[] $addends
     *
     * @return Money
     */
    public function add(Money ...$addends): Money
    {
        $amount = $this->amount;
        $calculator = $this->getCalculator();

        foreach ($addends as $addend) {
            $this->assertSameCurrency($addend);

            $amount = $calculator->add($amount, $addend->amount);
        }
        return new self($amount, $this->currency);
    }

    /**
     * @param Money $first
     * @param Money ...$collection
     *
     * @return Money
     */
    public static function avg(Money $first, Money ...$collection): Money
    {
        return $first->add(...$collection)->divide(\func_num_args());
    }

    /**
     * Returns a new Money object that represents
     * the divided value by the given factor.
     *
     * @param float|int|string $divisor
     *
     * @return Money
     */
    public function divide($divisor): Money
    {
        $this->assertOperand($divisor);

        if ($this->getCalculator()->compare($divisor, '0') === 0) {
            throw new \InvalidArgumentException('Division by zero');
        }

        $quotient = $this->getCalculator()->divide($this->amount, $divisor);
        return
            $this->newInstance($quotient);
    }

    /**
     * Asserts that the operand is integer or float.
     *
     * @param float|int|string|object $operand
     *
     * @throws \InvalidArgumentException If $operand is neither integer nor float
     */
    private function assertOperand($operand): void
    {
        if (!\is_numeric($operand)) {
            throw new \InvalidArgumentException(sprintf(
                'Operand should be a numeric value, "%s" given.',
                \is_object($operand) ? \get_class($operand) : \gettype($operand)
            ));
        }
    }

    /**
     * Asserts that rounding mode is a valid integer value.
     *
     * @param int $roundingMode
     *
     * @throws \InvalidArgumentException If $roundingMode is not valid
     */
    private function assertRoundingMode($roundingMode): void
    {
        if (!\in_array($roundingMode, [self::ROUND_HALF_DOWN, self::ROUND_HALF_UP,], true)) {
            throw new \InvalidArgumentException(
                'Rounding mode should be Money::ROUND_HALF_DOWN | Money::ROUND_HALF_UP '
            );
        }
    }

//    /**
//     * @param string $amount
//     * @param int $rounding_mode
//     *
//     * @return string
//     */
//    private function round($amount, int $rounding_mode): string
//    {
//        $this->assertRoundingMode($rounding_mode);
//
//        if ($rounding_mode === self::ROUND_HALF_UP) {
//            return $this->getCalculator()->ceil($amount);
//        }
//
//        if ($rounding_mode === self::ROUND_HALF_DOWN) {
//            return $this->getCalculator()->floor($amount);
//        }
//
//        return $this->getCalculator()->round($amount);
//    }

    /**
     * Returns a new Money instance based on the current one using the Currency.
     *
     * @param int|string|float|null $amount
     *
     * @return Money
     *
     * @throws \InvalidArgumentException
     */
    private function newInstance($amount): Money
    {
        return new self($amount, $this->currency);
    }

    /**
     * @param string $calculator
     */
    public static function registerCalculator($calculator): void
    {
        if (is_a($calculator, Calculator::class, true) === false) {
            throw new \InvalidArgumentException('Calculator must implement ' . Calculator::class);
        }

        array_unshift(self::$calculators, $calculator);
    }

    /**
     * Checks whether the value represented by this object equals to the other.
     *
     * @param Money $other
     *
     * @return bool
     */
    public function equals(Money $other): bool
    {
        return $this->isSameCurrency($other) && $this->amount === $other->amount;
    }

    /**
     * @param Money $other
     *
     * @return bool
     */
    public function greaterThanOrEqual(Money $other): bool
    {
        return $this->compare($other) >= 0;
    }

    /**
     * @param Money $other
     *
     * @return bool
     */
    public function lessThanOrEqual(Money $other): bool
    {
        return $this->compare($other) <= 0;
    }

    /**
     * Returns the value represented by this object.
     *
     * @return string
     */
    public function getAmount(): string
    {
        return $this->amount;
    }

    /**
     * Returns the currency of this object.
     *
     * @return Currency
     */
    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    /**
     * Returns a new Money object that represents
     * the multiplied value by the given factor.
     *
     * @param float|int|string $multiplier
     *
     * @return Money
     */
    public function multiply($multiplier): Money
    {
        $this->assertOperand($multiplier);

        $product = $this->getCalculator()->multiply($this->amount, $multiplier);

        return
            $this->newInstance($product);
    }

    /**
     * Returns a new Money object that represents
     * the remainder after dividing the value by
     * the given factor.
     *
     * @param Money $divisor
     *
     * @return Money
     */
    public function mod(Money $divisor): Money
    {
        $this->assertSameCurrency($divisor);

        return new self($this->getCalculator()->mod($this->amount, $divisor->amount), $this->currency);
    }

    /**
     * Allocate the money among N targets.
     *
     * @param int $n
     *
     * @return Money[]
     *
     * @throws \InvalidArgumentException If number of targets is not an integer
     */
    public function allocateTo($n): array
    {
        if (!\is_int($n)) {
            throw new \InvalidArgumentException('Number of targets must be an integer.');
        }
        if ($n <= 0) {
            throw new \InvalidArgumentException('Cannot allocate to none, target must be greater than zero.');
        }

        return $this->allocate(array_fill(0, $n, 1));
    }

    /**
     * Allocate the money according to a list of ratios.
     *
     * @param array $ratios
     *
     * @return iterable
     */
    public function allocate(array $ratios): iterable
    {
        if (\count($ratios) === 0) {
            throw new \InvalidArgumentException('Cannot allocate to none, ratios cannot be an empty array');
        }

        $remainder = $this->amount;
        $results = [];
        $total = array_sum($ratios);

        if ($total <= 0) {
            throw new \InvalidArgumentException('Cannot allocate to none, sum of ratios must be greater than zero');
        }

        foreach ($ratios as $ratio) {
            if ($ratio < 0) {
                throw new \InvalidArgumentException('Cannot allocate to none, ratio must be zero or positive');
            }

            $share = $this->getCalculator()->share($this->amount, $ratio, $total);
            $results[] = $this->newInstance($share);
            $remainder = $this->getCalculator()->subtract($remainder, $share);
        }

        for ($i = 0; $this->getCalculator()->compare($remainder, 0) === 1; ++$i) {
            if (!$ratios[$i]) {
                continue;
            }

            $results[$i]->amount = (string)$this->getCalculator()->add($results[$i]->amount, 1);
            $remainder = $this->getCalculator()->subtract($remainder, 1);
        }

        return $results;
    }

    /**
     * @param Money $money
     *
     * @return string
     */
    public function ratioOf(Money $money): string
    {
        if ($money->isZero()) {
            throw new \InvalidArgumentException('Cannot calculate a ratio of zero.');
        }

        return $this->getCalculator()->divide($this->amount, $money->amount);
    }

    /**
     * Checks if the value represented by this object is zero.
     *
     * @return bool
     */
    public function isZero(): bool
    {
        return $this->getCalculator()->compare($this->amount, 0) === 0;
    }

    /**
     * @return Money
     */
    public function absolute(): Money
    {
        return $this->newInstance($this->getCalculator()->absolute($this->amount));
    }

    /**
     * @return Money
     */
    public function negative(): Money
    {
        return $this->newInstance(0)->subtract($this);
    }

    /**
     * Returns a new Money object that represents
     * the difference of this and an other Money object.
     *
     * @param Money[] $subtrahends
     *
     * @return Money
     */
    public function subtract(Money ...$subtrahends): Money
    {
        $amount = $this->amount;
        $calculator = $this->getCalculator();

        foreach ($subtrahends as $subtrahend) {
            $this->assertSameCurrency($subtrahend);

            $amount = $calculator->subtract($amount, $subtrahend->amount);
        }

        return new self($amount, $this->currency);
    }

    /**
     * Checks if the value represented by this object is positive.
     *
     * @return bool
     */
    public function isPositive(): bool
    {
        return $this->getCalculator()->compare($this->amount, 0) === 1;
    }

    /**
     * Checks if the value represented by this object is negative.
     *
     * @return bool
     */
    public function isNegative(): bool
    {
        return $this->getCalculator()->compare($this->amount, 0) === -1;
    }

    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'amount' => $this->amount,
            'currency' => $this->currency,
        ];
    }
}