<?php
/**
 * User: alec
 * Date: 26.11.18
 * Time: 14:35
 */

namespace AlecRabbit\Money;

use AlecRabbit\Money\Contracts\CalculatorInterface;

trait MoneyFunctions
{
    /** @var CalculatorInterface */
    protected $calculator;

    /**
     * @param Money $first
     * @param Money ...$collection
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
     * @param Money ...$addends
     *
     * @return Money
     */
    public function add(Money ...$addends): Money
    {
        $amount = $this->getAmount();
        $calculator = $this->calculator;

        foreach ($addends as $addend) {
            $this->assertSameCurrency($addend);

            $amount = $calculator->add($amount, $addend->getAmount());
        }
        return new Money($amount, $this->getCurrency());
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

        if ($this->calculator->compare((string)$divisor, '0') === 0) {
            throw new \InvalidArgumentException('Division by zero.');
        }

        $quotient = $this->calculator->divide($this->getAmount(), $divisor);
        return
            $this->newInstance($quotient);
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
        return $this->isSameCurrency($other) && $this->getAmount() === $other->amount;
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

        $product = $this->calculator->multiply($this->getAmount(), $multiplier);

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

        return new Money($this->calculator->mod($this->getAmount(), $divisor->amount), $this->getCurrency());
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

        return $this->calculator->divide($this->getAmount(), $money->amount);
    }

    /**
     * Checks if the value represented by this object is zero.
     *
     * @return bool
     */
    public function isZero(): bool
    {
        return $this->calculator->compare($this->getAmount(), '0') === 0;
    }

    /**
     * @return Money
     */
    public function absolute(): Money
    {
        return $this->newInstance($this->calculator->absolute($this->getAmount()));
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
     * @param Money ...$subtrahends
     *
     * @return Money
     */
    public function subtract(Money ...$subtrahends): Money
    {
        $amount = $this->getAmount();
        $calculator = $this->calculator;

        foreach ($subtrahends as $subtrahend) {
            $this->assertSameCurrency($subtrahend);

            $amount = $calculator->subtract($amount, $subtrahend->amount);
        }

        return new Money($amount, $this->getCurrency());
    }

    /**
     * Checks if the value represented by this object is not negative.
     *
     * @return bool
     */
    public function isNotNegative(): bool
    {
        return
            !$this->isNegative();
    }

    /**
     * Checks if the value represented by this object is negative.
     *
     * @return bool
     */
    public function isNegative(): bool
    {
        return $this->calculator->compare($this->getAmount(), '0') === -1;
    }

    /**
     * Checks if the value represented by this object is not zero.
     *
     * @return bool
     */
    public function isNotZero(): bool
    {
        return
            !$this->isZero();
    }

    /**
     * Checks if the value represented by this object is not positive.
     *
     * @return bool
     */
    public function isNotPositive(): bool
    {
        return
            !$this->isPositive();
    }

    /**
     * Checks if the value represented by this object is positive.
     *
     * @return bool
     */
    public function isPositive(): bool
    {
        return $this->calculator->compare($this->getAmount(), '0') === 1;
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
    abstract public function compare(Money $other): int;

    /**
     * Checks whether a Money has the same Currency as this.
     *
     * @param Money $other
     *
     * @return bool
     */
    abstract public function isSameCurrency(Money $other): bool;

    /**
     * Returns the value represented by this object.
     *
     * @return string
     */
    abstract public function getAmount(): string;

    /**
     * Returns the currency of this object.
     *
     * @return Currency
     */
    abstract public function getCurrency(): Currency;

    /**
     * Asserts that a Money has the same currency as this.
     *
     * @param Money $other
     *
     * @throws \InvalidArgumentException If $other has a different currency
     */
    abstract protected function assertSameCurrency(Money $other): void;

    /**
     * Asserts that the operand is integer or float.
     *
     * @param float|int|string|object $operand
     *
     * @throws \InvalidArgumentException If $operand is neither integer nor float
     */
    abstract protected function assertOperand($operand): void;
}
