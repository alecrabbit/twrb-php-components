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
            if (null !== $asset = $this->assetOf($newAsset)) {
                $this->assets[(string)$newAsset->getCurrency()] = $asset->add($newAsset);
            } else {
                $this->assets[(string)$newAsset->getCurrency()] = $newAsset;
            }
        }
    }

    private function assetOf(Money $money): ?Money
    {
        return $this->getAsset($money->getCurrency());
    }

    public function getAsset(Currency $currency): ?Money
    {
        return
            $this->assets[(string)$currency] ?? null;
    }

    public function have(Money $money): bool
    {
        if (null !== $asset = $this->assetOf($money)) {
            return
                $asset->greaterThanOrEqual($money);
        }
        return false;
    }

    public function subtract(Money $money): ?Money
    {
        if (null !== $asset = $this->assetOf($money)) {
            return $asset->subtract($money);
        }
        return null;
    }

    public function add(Money $money): Money
    {
        $currency = $money->getCurrency();
        if (null !== $asset = $this->assetOf($money)) {
            $this->assets[(string)$currency] = $asset->add($money);
        } else {
            $this->assets[(string)$currency] = $money;
        }
        return $this->getAsset($currency);
    }

    public function getCurrencies(): iterable
    {
        return
            array_keys($this->assets);
    }

}