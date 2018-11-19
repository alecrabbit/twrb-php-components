<?php
/**
 * User: alec
 * Date: 05.11.18
 * Time: 20:27
 */

namespace AlecRabbit\Money;

use AlecRabbit\Money\Contracts\CurrencyInterface;

class Currency implements CurrencyInterface, \JsonSerializable
{
    private const _CODE_LENGTH = 9;

    /** @var string */
    private $code;

    /**
     * @param string $code
     */
    public function __construct(string $code)
    {
        if (\strlen($code) > static::_CODE_LENGTH) {
            throw new \InvalidArgumentException('Currency code must be not longer then ' . static::_CODE_LENGTH);
        }
        $this->code = strtoupper($code);
    }

    /**
     * Checks whether this currency is the same as an other.
     *
     * @param Currency $currency
     *
     * @return bool
     */
    public function equals(Currency $currency): bool
    {
        return $this->code === $currency->code;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getCode();
    }

    /**
     * Returns the currency code.
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize(): string
    {
        return $this->code;
    }
}
