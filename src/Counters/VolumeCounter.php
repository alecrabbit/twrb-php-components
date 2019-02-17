<?php
/**
 * Date: 19.11.18
 * Time: 17:27
 */
declare(strict_types=1);

namespace AlecRabbit\Counters;

use function AlecRabbit\array_key_first;
use AlecRabbit\Money\CalculatorFactory as Factory;
use AlecRabbit\Money\Contracts\CalculatorInterface;
use AlecRabbit\Structures\Trade;

class VolumeCounter extends EventsCounter
{
    protected const DOMAINS = [
        STR_TOTAL,
        STR_SELL,
        STR_BUY,
        STR_VP_TOTAL,
        STR_VP_SELL,
        STR_VP_BUY,
        STR_P_SUM_TOTAL,
        STR_P_SUM_SELL,
        STR_P_SUM_BUY,
    ];

    /** @var CalculatorInterface */
    protected $calculator;

    /**
     * @inheritDoc
     */
    public function __construct(?array $periods = null)
    {
        parent::__construct($periods);
        $this->calculator = Factory::getCalculator();
    }

    public function addTrade(Trade $trade): void
    {
        $baseTimes = $this->getBaseTimes($trade->timestamp);
        if ($this->precisionMode) {
            $volumePrice = $this->calculator->multiply((string)$trade->amount, $trade->price);
        } else {
            $volumePrice = $trade->amount * $trade->price;
        }
        foreach ($baseTimes as $period => $timestamp) {
            $this->processPart(STR_TOTAL, $period, $timestamp, $volumePrice, $trade);
            $this->processPart(
                $trade->side === T_SELL ? STR_SELL : STR_BUY,
                $period,
                $timestamp,
                $volumePrice,
                $trade
            );
            $this->trim($period);
        }
    }

    /**
     * @param string $subdomain
     * @param int $period
     * @param int $timestamp
     * @param float|string $volumePrice
     * @param Trade $trade
     */
    protected function processPart(
        string $subdomain,
        int $period,
        int $timestamp,
        $volumePrice,
        Trade $trade
    ): void {
        $this->data[STR_VOLUMES][STR_VP . $subdomain][$period][$timestamp] =
            $this->data[STR_VOLUMES][STR_VP . $subdomain][$period][$timestamp] ?? 0;

        $this->data[STR_VOLUMES][STR_P_SUM . $subdomain][$period][$timestamp] =
            $this->data[STR_VOLUMES][STR_P_SUM . $subdomain][$period][$timestamp] ?? 0;

        $this->data[STR_VOLUMES][$subdomain][$period][$timestamp] =
            $this->data[STR_VOLUMES][$subdomain][$period][$timestamp] ?? 0;

        $this->data[STR_EVENTS][$subdomain][$period][$timestamp] =
            $this->data[STR_EVENTS][$subdomain][$period][$timestamp] ?? 0;

        $this->data[STR_EVENTS][$subdomain][$period][$timestamp]++;

        if ($this->precisionMode) {
            $this->data[STR_VOLUMES][STR_VP . $subdomain][$period][$timestamp] =
                (float)
                $this->calculator->add(
                    $this->data[STR_VOLUMES][STR_VP . $subdomain][$period][$timestamp],
                    $volumePrice
                );
            $this->data[STR_VOLUMES][STR_P_SUM . $subdomain][$period][$timestamp] =
                (float)
                $this->calculator->add(
                    $this->data[STR_VOLUMES][STR_P_SUM . $subdomain][$period][$timestamp],
                    $trade->price
                );
            $this->data[STR_VOLUMES][$subdomain][$period][$timestamp] =
                (float)
                $this->calculator->add(
                    $this->data[STR_VOLUMES][$subdomain][$period][$timestamp],
                    $trade->amount
                );
        } else {
            $this->data[STR_VOLUMES][STR_VP . $subdomain][$period][$timestamp] += $volumePrice;
            $this->data[STR_VOLUMES][STR_P_SUM . $subdomain][$period][$timestamp] += $trade->price;
            $this->data[STR_VOLUMES][$subdomain][$period][$timestamp] += $trade->amount;
        }
    }

    /**
     * @param int $period
     */
    protected function trim(int $period): void
    {
        $threshold = $this->getThreshold($period);
        foreach (static::DOMAINS as $domain) {
            if (null !== ($timestamp = array_key_first($this->data[STR_EVENTS][$domain][$period] ?? []))
                && ($timestamp <= $threshold)) {
                unset($this->data[STR_EVENTS][$domain][$period][$timestamp]);
            }
            if (null !== ($timestamp = array_key_first($this->data[STR_VOLUMES][$domain][$period] ?? []))
                && ($timestamp <= $threshold)) {
                unset($this->data[STR_VOLUMES][$domain][$period][$timestamp]);
            }
        }
    }

    /**
     * @param bool|null $reset
     * @return array
     */
    public function getVolumeArray(?bool $reset = null): array
    {
        $volumes = $this->calcVolumes();
        $events = $this->getEventsArray();
        $averages = $this->calcAverages($volumes, $events);
        if ($reset) {
            $this->reset();
        }
        return [
            STR_VOLUMES => $volumes,
            STR_AVERAGES => $averages,
            STR_EVENTS => $events
        ];
    }

    /**
     * @inheritDoc
     */
    public function getEventsArray(?bool $reset = null): array
    {
        $events = [];
        foreach (static::DOMAINS as $domain) {
            foreach ($this->periods as $period => $groupBy) {
                if (0 < ($sum = array_sum($this->data[STR_EVENTS][$domain][$period] ?? []))) {
                    $events[$domain][$period] = $sum;
                }
            }
        }
        if ($reset) {
            $this->reset();
        }
        return $events;
    }

    /**
     * @param array $volumes
     * @param array $events
     * @return array
     */
    private function calcAverages(array $volumes, array $events): array
    {
        $averages = [];
        foreach ($this->periods as $period => $groupBy) {
            if (isset($volumes[STR_VP_TOTAL][$period], $volumes[STR_TOTAL][$period])) {
                $averages[STR_VWAP_TOTAL][$period] = $volumes[STR_VP_TOTAL][$period] / $volumes[STR_TOTAL][$period];
            }
            if (isset($volumes[STR_VP_SELL][$period], $volumes[STR_SELL][$period])) {
                $averages[STR_VWAP_SELL][$period] = $volumes[STR_VP_SELL][$period] / $volumes[STR_SELL][$period];
            }
            if (isset($volumes[STR_VP_BUY][$period], $volumes[STR_BUY][$period])) {
                $averages[STR_VWAP_BUY][$period] = $volumes[STR_VP_BUY][$period] / $volumes[STR_BUY][$period];
            }
            if (isset($volumes[STR_P_SUM_TOTAL][$period], $events[STR_TOTAL][$period])) {
                $averages[STR_AVG_PRICE_TOTAL][$period] =
                    $volumes[STR_P_SUM_TOTAL][$period] / $events[STR_TOTAL][$period];
            }
            if (isset($volumes[STR_P_SUM_SELL][$period], $events[STR_SELL][$period])) {
                $averages[STR_AVG_PRICE_SELL][$period] =
                    $volumes[STR_P_SUM_SELL][$period] / $events[STR_SELL][$period];
            }
            if (isset($volumes[STR_P_SUM_BUY][$period], $events[STR_BUY][$period])) {
                $averages[STR_AVG_PRICE_BUY][$period] =
                    $volumes[STR_P_SUM_BUY][$period] / $events[STR_BUY][$period];
            }
        }
        return $averages;
    }

    /**
     * @return array
     */
    private function calcVolumes(): array
    {
        $volumes = [];
        foreach (static::DOMAINS as $domain) {
            foreach ($this->periods as $period => $groupBy) {
                if (0 < ($sum = array_sum($this->data[STR_VOLUMES][$domain][$period] ?? []))) {
                    $volumes[$domain][$period] = $sum;
                }
            }
        }
        return $volumes;
    }
}
