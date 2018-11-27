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
    private $calculator;


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
     * Allocate the money among N targets.
     *
     * @param int $n
     *
     * @param int|null $precision
     * @return Money[]
     *
     */
    public function allocateTo(int $n, ?int $precision = null): array
    {
        if ($n <= 0) {
            throw new \InvalidArgumentException('Number to allocateTo must be greater than zero.');
        }

        return $this->allocate(array_fill(0, $n, 1), $precision);
    }

    /**
     * Allocate the money according to a list of ratios.
     *
     * @param array $ratios
     *
     * @param int|null $precision
     * @return Money[]
     */
    public function allocate(array $ratios, ?int $precision = null): array
    {
        $precision = $precision ?? 2;
        if (0 === $allocations = \count($ratios)) {
            throw new \InvalidArgumentException('Cannot allocate to none, ratios cannot be an empty array.');
        }

        $remainder = $this->getAmount();
        $results = [];
        $total = array_sum($ratios);

        if ($total <= 0) {
            throw new \InvalidArgumentException('Sum of ratios must be greater than zero.');
        }

        foreach ($ratios as $ratio) {
            if ($ratio < 0) {
                throw new \InvalidArgumentException('Ratio must be zero or positive.');
            }

            $share = $this->calculator->share($this->getAmount(), $ratio, $total, $precision);
            $results[] = $this->newInstance($share);
            $remainder = $this->calculator->subtract($remainder, $share);
        }
        switch ($this->calculator->compare($remainder, '0')) {
            case -1:
                for ($i = $allocations - 1; $i >= 0; $i--) {
                    if (!$ratios[$i]) {
                        continue;
                    }
                    $results[$i]->setAmount($this->calculator->add($results[$i]->amount, $remainder));
                    break;
                }
                break;
            case 1:
                for ($i = 0; $i < $allocations; $i++) {
                    if (!$ratios[$i]) {
                        continue;
                    }
                    $results[$i]->setAmount($this->calculator->add($results[$i]->amount, $remainder));
                    break;
                }
                break;
            default:
                break;
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

    abstract public function compare(Money $other): int;

    abstract public function isSameCurrency(Money $other): bool;

    abstract protected function assertSameCurrency(Money $other): void;

    abstract protected function assertOperand($operand): void;
}
