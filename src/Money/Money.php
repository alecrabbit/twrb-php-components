<?php
/**
 * User: alec
 * Date: 05.11.18
 * Time: 23:51
 */

namespace AlecRabbit\Money;

use AlecRabbit\Money\CalculatorFactory as Factory;
use AlecRabbit\Money\Contracts\MoneyInterface;

/**
 * Money Value Object.
 *
 * @author Mathias Verraes
 */
class Money implements MoneyInterface, \JsonSerializable
{
    use MoneyFactory,
        MoneyFunctions;

    /** @var string */
    protected $amount;

    /** @var Currency */
    protected $currency;

    /**
     * @param null|int|float|string $amount
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

        $this->setAmount((string)$amount);
        $this->setCurrency($currency);
    }

    /**
     * @param string $amount
     */
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
    protected function assertSameCurrency(Money $other): void
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
     * Asserts that the operand is integer or float.
     *
     * @param float|int|string|object $operand
     *
     * @throws \InvalidArgumentException If $operand is neither integer nor float
     */
    protected function assertOperand($operand): void
    {
        if (!\is_numeric($operand)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Operand should be a numeric value, "%s" given.',
                    typeOf($operand)
                )
            );
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
        return new Money($amount, $this->currency);
    }
}
