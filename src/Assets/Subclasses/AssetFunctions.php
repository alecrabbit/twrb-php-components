<?php
/**
 * Date: 26.11.18
 * Time: 14:35
 */

namespace AlecRabbit\Assets\Subclasses;

use AlecRabbit\Assets\Asset;
use AlecRabbit\Currency\Currency;
use AlecRabbit\Money\Contracts\CalculatorInterface;

trait AssetFunctions
{
    /** @var CalculatorInterface */
    protected $calculator;

    /**
     * @param Asset $first
     * @param Asset ...$collection
     *
     * @return Asset
     */
    public static function min(Asset $first, Asset ...$collection): Asset
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
     * @param Asset $other
     *
     * @return bool
     */
    public function lessThan(Asset $other): bool
    {
        return $this->compare($other) === -1;
    }

    /**
     * @param Asset $first
     * @param Asset ...$collection
     *
     * @return Asset
     */
    public static function max(Asset $first, Asset ...$collection): Asset
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
     * @param Asset $other
     *
     * @return bool
     */
    public function greaterThan(Asset $other): bool
    {
        return $this->compare($other) === 1;
    }

    /**
     * @param Asset $first
     * @param Asset ...$collection
     *
     * @return Asset
     */
    public static function sum(Asset $first, Asset ...$collection): Asset
    {
        return $first->add(...$collection);
    }

    /**
     * Returns a new Money object that represents
     * the sum of this and an other Money object.
     *
     * @param Asset ...$addends
     *
     * @return Asset
     */
    public function add(Asset ...$addends): Asset
    {
        $amount = $this->getAmount();
        $calculator = $this->calculator;

        foreach ($addends as $addend) {
            $this->assertSameCurrency($addend);

            $amount = $calculator->add($amount, $addend->getAmount());
        }
        return new Asset($amount, $this->getCurrency());
    }

    /**
     * @param Asset $first
     * @param Asset ...$collection
     *
     * @return Asset
     */
    public static function avg(Asset $first, Asset ...$collection): Asset
    {
        return $first->add(...$collection)->divide(\func_num_args());
    }

    /**
     * Returns a new Money object that represents
     * the divided value by the given factor.
     *
     * @param float|int|string $divisor
     *
     * @return Asset
     */
    public function divide($divisor): Asset
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
     * @param Asset $other
     *
     * @return bool
     */
    public function equals(Asset $other): bool
    {
        return $this->isSameCurrency($other) && $this->getAmount() === $other->getAmount();
    }

    /**
     * @param Asset $other
     *
     * @return bool
     */
    public function greaterThanOrEqual(Asset $other): bool
    {
        return $this->compare($other) >= 0;
    }

    /**
     * @param Asset $other
     *
     * @return bool
     */
    public function lessThanOrEqual(Asset $other): bool
    {
        return $this->compare($other) <= 0;
    }

    /**
     * Returns a new Money object that represents
     * the multiplied value by the given factor.
     *
     * @param float|int|string $multiplier
     *
     * @return Asset
     */
    public function multiply($multiplier): Asset
    {
        $this->assertOperand($multiplier);

        $result = $this->calculator->multiply($this->getAmount(), $multiplier);

        return
            $this->newInstance($result);
    }

    /**
     * Returns a new Money object that represents
     * the remainder after dividing the value by
     * the given factor.
     *
     * @param Asset $divisor
     *
     * @return Asset
     */
    public function mod(Asset $divisor): Asset
    {
        $this->assertSameCurrency($divisor);

        $mod = $this->calculator->mod($this->getAmount(), $divisor->getAmount());
        return
            $this->newInstance($mod);
    }

    /**
     * @param Asset $money
     *
     * @return string
     */
    public function ratioOf(Asset $money): string
    {
        if ($money->isZero()) {
            throw new \InvalidArgumentException('Cannot calculate a ratio of zero.');
        }

        return $this->calculator->divide($this->getAmount(), $money->getAmount());
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
     * Returns a new Money object that represents
     * the difference of this and an other Money object.
     *
     * @param Asset ...$subtrahends
     *
     * @return Asset
     */
    public function subtract(Asset ...$subtrahends): Asset
    {
        $amount = $this->getAmount();
        $calculator = $this->calculator;

        foreach ($subtrahends as $subtrahend) {
            $this->assertSameCurrency($subtrahend);

            $amount = $calculator->subtract($amount, $subtrahend->getAmount());
        }

        return new Asset($amount, $this->getCurrency());
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
     * @param Asset $other
     *
     * @return int
     */
    abstract public function compare(Asset $other): int;

    /**
     * Checks whether a Money has the same Currency as this.
     *
     * @param Asset $other
     *
     * @return bool
     */
    abstract public function isSameCurrency(Asset $other): bool;

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
     * @param Asset $other
     *
     * @throws \InvalidArgumentException If $other has a different currency
     */
    abstract protected function assertSameCurrency(Asset $other): void;

    /**
     * Asserts that the operand is integer or float.
     *
     * @param float|int|string|object $operand
     *
     * @throws \InvalidArgumentException If $operand is neither integer nor float
     */
    abstract protected function assertOperand($operand): void;

    /**
     * Returns a new Money instance based on the current one using the Currency.
     *
     * @param int|string|float|null $amount
     *
     * @return Asset
     *
     * @throws \InvalidArgumentException
     */
    abstract protected function newInstance($amount): Asset;
}
