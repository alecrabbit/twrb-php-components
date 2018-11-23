<?php
/**
 * User: alec
 * Date: 19.11.18
 * Time: 17:27
 */
declare(strict_types=1);

namespace AlecRabbit\Counters;

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

    /** @var array */
    protected $volumes = [];

    public function addTrade(Trade $trade): void
    {
        $baseTimes = $this->getBaseTimes($trade->timestamp);
        $volumePrice = $trade->amount * $trade->price;
        foreach ($baseTimes as $period => $timestamp) {
//            $this->volumes[STR_VP_TOTAL][$period][$timestamp] =
//                $this->volumes[STR_VP_TOTAL][$period][$timestamp] ?? 0;
//            $this->volumes[STR_VP_TOTAL][$period][$timestamp] += $volumePrice;
//
//            $this->volumes[STR_P_SUM_TOTAL][$period][$timestamp] =
//                $this->volumes[STR_P_SUM_TOTAL][$period][$timestamp] ?? 0;
//            $this->volumes[STR_P_SUM_TOTAL][$period][$timestamp] += $trade->price;
//
//            $this->volumes[STR_TOTAL][$period][$timestamp] = $this->volumes[STR_TOTAL][$period][$timestamp] ?? 0;
//            $this->volumes[STR_TOTAL][$period][$timestamp] += $trade->amount;
//
//            $this->data[STR_TOTAL][$period][$timestamp] = $this->data[STR_TOTAL][$period][$timestamp] ?? 0;
//            $this->data[STR_TOTAL][$period][$timestamp]++;
            $this->processPart(STR_TOTAL, $period, $timestamp, $volumePrice, $trade);

            if ($trade->side === T_SELL) {
//                $this->volumes[STR_VP_SELL][$period][$timestamp] =
//                    $this->volumes[STR_VP_SELL][$period][$timestamp] ?? 0;
//                $this->volumes[STR_VP_SELL][$period][$timestamp] += $volumePrice;
//
//                $this->volumes[STR_P_SUM_SELL][$period][$timestamp] =
//                    $this->volumes[STR_P_SUM_SELL][$period][$timestamp] ?? 0;
//                $this->volumes[STR_P_SUM_SELL][$period][$timestamp] += $trade->price;
//
//                $this->volumes[STR_SELL][$period][$timestamp] = $this->volumes[STR_SELL][$period][$timestamp] ?? 0;
//                $this->volumes[STR_SELL][$period][$timestamp] += $trade->amount;
//                $this->data[STR_SELL][$period][$timestamp] = $this->data[STR_SELL][$period][$timestamp] ?? 0;
//                $this->data[STR_SELL][$period][$timestamp]++;
                $this->processPart(STR_SELL, $period, $timestamp, $volumePrice, $trade);
            } else {
//                $this->volumes[STR_VP_BUY][$period][$timestamp] =
//                    $this->volumes[STR_VP_BUY][$period][$timestamp] ?? 0;
//                $this->volumes[STR_VP_BUY][$period][$timestamp] += $volumePrice;
//
//                $this->volumes[STR_P_SUM_BUY][$period][$timestamp] =
//                    $this->volumes[STR_P_SUM_BUY][$period][$timestamp] ?? 0;
//                $this->volumes[STR_P_SUM_BUY][$period][$timestamp] += $trade->price;
//
//                $this->volumes[STR_BUY][$period][$timestamp] = $this->volumes[STR_BUY][$period][$timestamp] ?? 0;
//                $this->volumes[STR_BUY][$period][$timestamp] += $trade->amount;
//                $this->data[STR_BUY][$period][$timestamp] = $this->data[STR_BUY][$period][$timestamp] ?? 0;
//                $this->data[STR_BUY][$period][$timestamp]++;
                $this->processPart(STR_BUY, $period, $timestamp, $volumePrice, $trade);
            }
            $this->trim($period);
        }
    }

    protected function processPart(
        string $subdomain,
        int $period,
        int $timestamp,
        float $volumePrice,
        Trade $trade
    ): void {
        $this->volumes[STR_VP . $subdomain][$period][$timestamp] =
            $this->volumes[STR_VP . $subdomain][$period][$timestamp] ?? 0;
        $this->volumes[STR_VP . $subdomain][$period][$timestamp] += $volumePrice;

        $this->volumes[STR_P_SUM . $subdomain][$period][$timestamp] =
            $this->volumes[STR_P_SUM . $subdomain][$period][$timestamp] ?? 0;
        $this->volumes[STR_P_SUM . $subdomain][$period][$timestamp] += $trade->price;

        $this->volumes[$subdomain][$period][$timestamp] = $this->volumes[$subdomain][$period][$timestamp] ?? 0;
        $this->volumes[$subdomain][$period][$timestamp] += $trade->amount;

        $this->data[$subdomain][$period][$timestamp] = $this->data[$subdomain][$period][$timestamp] ?? 0;
        $this->data[$subdomain][$period][$timestamp]++;
    }

    protected function trim(int $period): void
    {
        $threshold = $this->getThreshold($period);
        foreach (static::DOMAINS as $domain) {
            if (null !== ($key = array_key_first($this->data[$domain][$period] ?? [])) && ($key <= $threshold)) {
                unset($this->data[$domain][$period][$key]);
            }
            if (null !== ($key = array_key_first($this->volumes[$domain][$period] ?? [])) && ($key <= $threshold)) {
                unset($this->volumes[$domain][$period][$key]);
            }
        }
    }

    /**
     * @param bool|null $reset
     * @return array
     */
    public function getVolumeArray(?bool $reset = null): array
    {
        $volume = [];
        foreach (static::DOMAINS as $domain) {
            foreach ($this->periods as $period => $groupBy) {
                if (0 < ($sum = array_sum($this->volumes[$domain][$period] ?? []))) {
                    $volume[$domain][$period] = $sum;
                }
            }
        }
        $events = $this->getEventsArray();
        foreach ($this->periods as $period => $groupBy) {
            if (isset($volume[STR_VP_TOTAL][$period], $volume[STR_TOTAL][$period])) {
                $volume[STR_VWAP_TOTAL][$period] = $volume[STR_VP_TOTAL][$period] / $volume[STR_TOTAL][$period];
            }
            if (isset($volume[STR_VP_SELL][$period], $volume[STR_SELL][$period])) {
                $volume[STR_VWAP_SELL][$period] = $volume[STR_VP_SELL][$period] / $volume[STR_SELL][$period];
            }
            if (isset($volume[STR_VP_BUY][$period], $volume[STR_BUY][$period])) {
                $volume[STR_VWAP_BUY][$period] = $volume[STR_VP_BUY][$period] / $volume[STR_BUY][$period];
            }
            if (isset($volume[STR_P_SUM_TOTAL][$period], $events[STR_TOTAL][$period])) {
                $volume[STR_AVG_PRICE_TOTAL][$period] =
                    $volume[STR_P_SUM_TOTAL][$period] / $events[STR_TOTAL][$period];
            }
            if (isset($volume[STR_P_SUM_SELL][$period], $events[STR_SELL][$period])) {
                $volume[STR_AVG_PRICE_SELL][$period] =
                    $volume[STR_P_SUM_SELL][$period] / $events[STR_SELL][$period];
            }
            if (isset($volume[STR_P_SUM_BUY][$period], $events[STR_BUY][$period])) {
                $volume[STR_AVG_PRICE_BUY][$period] =
                    $volume[STR_P_SUM_BUY][$period] / $events[STR_BUY][$period];
            }
        }
        if ($reset) {
            $this->reset();
        }
        return $volume;
    }

    /**
     * @inheritDoc
     */
    public function getEventsArray(?bool $reset = null): array
    {
        $events = [];
        foreach (static::DOMAINS as $domain) {
            foreach ($this->periods as $period => $groupBy) {
                if (0 < ($sum = array_sum($this->data[$domain][$period] ?? []))) {
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
     * @inheritDoc
     */
    protected function reset(): void
    {
        $this->data = [];
        $this->volumes = [];
    }

    /** @return array */
    public function getRawData(): array
    {
        return
            [
                STR_VOLUMES => $this->volumes,
                STR_EVENTS => parent::getRawData(),
            ];
    }
}
