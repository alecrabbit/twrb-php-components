<?php
/**
 * Date: 11.11.18
 * Time: 19:34
 */

namespace AlecRabbit\Assets;

use AlecRabbit\Currency\Currency;

class Assets
{
    /** @var Asset[] */
    private $assets = [];

    /**
     * Assets constructor.
     * @param Asset ...$assets
     */
    public function __construct(Asset ...$assets)
    {
        foreach ($assets as $newAsset) {
            $this->add($newAsset);
        }
    }

    /**
     * @param Asset $money
     * @return Asset
     */
    public function add(Asset $money): Asset
    {
        return
            ($asset = $this->assetOf($money))->isZero() ?
                $this->setAsset($money) :
                $this->setAsset($asset->add($money));
    }

    /**
     * @param Asset $money
     * @return Asset
     */
    private function assetOf(Asset $money): Asset
    {
        return $this->getAsset($money->getCurrency());
    }

    /**
     * @param \AlecRabbit\Currency\Currency $currency
     * @return Asset
     */
    public function getAsset(Currency $currency): Asset
    {
        return
            $this->assets[(string)$currency] ?? $this->setAsset(new Asset(0, $currency));
    }

    /**
     * @param Asset $money
     * @return Asset
     */
    private function setAsset(Asset $money): Asset
    {
        return
            $this->assets[(string)$money->getCurrency()] = $money;
    }

    /**
     * @param Asset $money
     * @return bool
     */
    public function have(Asset $money): bool
    {
        return
            $money->subtract($this->assetOf($money))->isNotPositive();
    }

    /**
     * @param Asset $money
     * @return Asset
     */
    public function take(Asset $money): Asset
    {
        $asset = $this->subtract($money);
        if ($asset->isNegative()) {
            throw new \RuntimeException('You can\'t take more than there is.');
        }
        return $asset;
    }

    /**
     * @param Asset $money
     * @return Asset
     */
    public function subtract(Asset $money): Asset
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
