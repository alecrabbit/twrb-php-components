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
     * @throws \InvalidArgumentException If amount is not numeric
     */
    public function __construct($amount, Currency $currency)
    {
        $this->setAmount($this->assertAmount($amount));
        $this->setCurrency($currency);
        $this->calculator = Factory::getCalculator();
    }

    /**
     * @param null|int|float|string $amount
     * @return string
     */
    private function assertAmount($amount): string
    {
        if (null === $amount) {
            $amount = 0;
        }
        if (!\is_numeric($amount)) {
            throw new \InvalidArgumentException('Amount must be int|float|string');
        }
        return (string)$amount;
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
     * {@inheritdoc}
     */
    public function compare(Money $other): int
    {
        $this->assertSameCurrency($other);

        return $this->calculator->compare($this->amount, $other->getAmount());
    }

    /**
     * {@inheritdoc}
     */
    protected function assertSameCurrency(Money $other): void
    {
        if (!$this->isSameCurrency($other)) {
            throw new \InvalidArgumentException('Currencies must be identical.');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isSameCurrency(Money $other): bool
    {
        return $this->currency->equals($other->currency);
    }

    /**
     * {@inheritdoc}
     */
    public function getAmount(): string
    {
        return $this->amount;
    }

    /**
     * {@inheritdoc}
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
        return
            (new AllocationCalculator($this))->compute($ratios, $precision);
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    protected function newInstance($amount): Money
    {
        return new Money($amount, $this->currency);
    }

    /**
     * @return Money
     */
    public function absolute(): Money
    {
        return
            $this->newInstance($this->calculator->absolute($this->getAmount()));
    }

    /**
     * @return Money
     */
    public function negative(): Money
    {
        return
            $this->newInstance(0)->subtract($this);
    }

}
