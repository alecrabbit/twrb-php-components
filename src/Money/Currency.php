<?php
/**
 * User: alec
 * Date: 05.11.18
 * Time: 20:27
 */

namespace AlecRabbit\Money;


class Currency implements \JsonSerializable
{
    /** @var string */
    private $code;

    /**
     * @param string $code
     */
    public function __construct(string $code)
    {
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