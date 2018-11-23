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
     * @param float $volumePrice
     * @param Trade $trade
     */
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

    /**
     * @param int $period
     */
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
        $volumes = [];
        foreach (static::DOMAINS as $domain) {
            foreach ($this->periods as $period => $groupBy) {
                if (0 < ($sum = array_sum($this->volumes[$domain][$period] ?? []))) {
                    $volumes[$domain][$period] = $sum;
                }
            }
        }
        $events = $this->getEventsArray();
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
