<?php
/**
 * User: alec
 * Date: 11.11.18
 * Time: 19:34
 */

namespace AlecRabbit\Assets;

use AlecRabbit\Money\Currency;
use AlecRabbit\Money\Money;

class Assets
{
    /** @var Money[] */
    private $assets = [];

    /**
     * Assets constructor.
     * @param Money ...$assets
     */
    public function __construct(Money ...$assets)
    {
        foreach ($assets as $newAsset) {
            $this->add($newAsset);
        }
    }

    /**
     * @param Money $money
     * @return Money
     */
    public function add(Money $money): Money
    {
        return
            ($asset = $this->assetOf($money))->isZero() ?
                $this->setAsset($money) :
                $this->setAsset($asset->add($money));
    }

    /**
     * @param Money $money
     * @return Money
     */
    private function assetOf(Money $money): Money
    {
        return $this->getAsset($money->getCurrency());
    }

    /**
     * @param Currency $currency
     * @return Money
     */
    public function getAsset(Currency $currency): Money
    {
        return
            $this->assets[(string)$currency] ?? $this->setAsset(new Money(0, $currency));
    }

    /**
     * @param Money $money
     * @return Money
     */
    private function setAsset(Money $money): Money
    {
        return
            $this->assets[(string)$money->getCurrency()] = $money;
    }

    /**
     * @param Money $money
     * @return bool
     */
    public function have(Money $money): bool
    {
        return
            $money->subtract($this->assetOf($money))->isNotPositive();
    }

    /**
     * @param Money $money
     * @return Money
     */
    public function take(Money $money): Money
    {
        $asset = $this->subtract($money);
        if ($asset->isNegative()) {
            throw new \RuntimeException('You can\'t take more than there is.');
        }
        return $asset;
    }

    /**
     * @param Money $money
     * @return Money
     */
    public function subtract(Money $money): Money
    {
        return
            $this->add($money->negative());
    }

    /**
     * @return iterable
     */
    public function getCurrencies(): iterable
    {
        return
            array_keys($this->assets);
    }
}
