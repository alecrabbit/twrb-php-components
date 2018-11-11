<?php
/**
 * User: alec
 * Date: 05.11.18
 * Time: 23:51
 */

namespace AlecRabbit\Money;


use AlecRabbit\Money\CalculatorFactory as Factory;
use AlecRabbit\Money\Contracts\CalculatorInterface;
use AlecRabbit\Money\Contracts\MoneyInterface;

/**
 * Money Value Object.
 *
 * @author Mathias Verraes
 */
class Money implements MoneyInterface, \JsonSerializable
{
    use MoneyFactory;

    /** @var CalculatorInterface */
    private $calculator;

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
        $this->calculator = Factory::getCalculator();

        $this->setAmount($amount);
        $this->setCurrency($currency);
    }

    private function setAmount(string $amount): void
    {
        $this->amount = trim_zeros($amount);
    }

    /**
     * @param Currency $currency
     */
    private function setCurrency(Currency $currency): void
    {
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

        return $this->calculator->compare($this->amount, $other->amount);
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
        $calculator = $this->calculator;

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

        if ($this->calculator->compare($divisor, '0') === 0) {
            throw new \InvalidArgumentException('Division by zero.');
        }

        $quotient = $this->calculator->divide($this->amount, $divisor);
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

        $product = $this->calculator->multiply($this->amount, $multiplier);

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

        return new self($this->calculator->mod($this->amount, $divisor->amount), $this->currency);
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
     * @return iterable
     */
    public function allocate(array $ratios, ?int $precision = null): iterable
    {
        $precision = $precision ?? 2;
        if (0 === $allocations = \count($ratios)) {
            throw new \InvalidArgumentException('Cannot allocate to none, ratios cannot be an empty array.');
        }

        $remainder = $this->amount;
        $results = [];
        $total = array_sum($ratios);

        if ($total <= 0) {
            throw new \InvalidArgumentException('Sum of ratios must be greater than zero.');
        }

        foreach ($ratios as $ratio) {
            if ($ratio < 0) {
                throw new \InvalidArgumentException('Ratio must be zero or positive.');
            }

            $share = $this->calculator->share($this->amount, $ratio, $total, $precision);
            $results[] = $this->newInstance($share);
            $remainder = $this->calculator->subtract($remainder, $share);
        }
        switch ($this->calculator->compare($remainder, 0)) {
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

        return $this->calculator->divide($this->amount, $money->amount);
    }

    /**
     * Checks if the value represented by this object is zero.
     *
     * @return bool
     */
    public function isZero(): bool
    {
        return $this->calculator->compare($this->amount, 0) === 0;
    }

    /**
     * @return Money
     */
    public function absolute(): Money
    {
        return $this->newInstance($this->calculator->absolute($this->amount));
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
        $calculator = $this->calculator;

        foreach ($subtrahends as $subtrahend) {
            $this->assertSameCurrency($subtrahend);

            $amount = $calculator->subtract($amount, $subtrahend->amount);
        }

        return new self($amount, $this->currency);
    }

    /**
     * Checks if the value represented by this object is negative.
     *
     * @return bool
     */
    public function isNegative(): bool
    {
        return $this->calculator->compare($this->amount, 0) === -1;
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
        return $this->calculator->compare($this->amount, 0) === 1;
    }
}