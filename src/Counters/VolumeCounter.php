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
    protected const DOMAINS = [STR_TOTAL, STR_SELL, STR_BUY,];

    /** @var array */
    protected $volumes = [];

    public function addTrade(Trade $trade): void
    {
        $baseTimes = $this->getBaseTimes($trade->timestamp);
        foreach ($baseTimes as $period => $timestamp) {
            $this->volumes[STR_TOTAL][$period][$timestamp] = $this->volumes[STR_TOTAL][$period][$timestamp] ?? 0;
            $this->volumes[STR_TOTAL][$period][$timestamp] += $trade->amount;
            $this->data[STR_TOTAL][$period][$timestamp] = $this->data[STR_TOTAL][$period][$timestamp] ?? 0;
            $this->data[STR_TOTAL][$period][$timestamp]++;

            if ($trade->side === T_SELL) {
                $this->volumes[STR_SELL][$period][$timestamp] = $this->volumes[STR_SELL][$period][$timestamp] ?? 0;
                $this->volumes[STR_SELL][$period][$timestamp] += $trade->amount;
                $this->data[STR_SELL][$period][$timestamp] = $this->data[STR_SELL][$period][$timestamp] ?? 0;
                $this->data[STR_SELL][$period][$timestamp]++;
            } else {
                $this->volumes[STR_BUY][$period][$timestamp] = $this->volumes[STR_BUY][$period][$timestamp] ?? 0;
                $this->volumes[STR_BUY][$period][$timestamp] += $trade->amount;
                $this->data[STR_BUY][$period][$timestamp] = $this->data[STR_BUY][$period][$timestamp] ?? 0;
                $this->data[STR_BUY][$period][$timestamp]++;
            }
            $this->trim($period);
        }
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

//    /**
//     * @param bool|null $reset
//     * @return Volume
//     */
//    public function getVolumeObject(?bool $reset = null): Volume
//    {
//        $volume = new Volume();
//        if (0 < ($sum = array_sum($this->volumes[STR_TOTAL]))) {
//            $volume
//                ->setTotal($sum)
//                ->setSell(array_sum($this->volumes[STR_SELL]))
//                ->setBuy(array_sum($this->volumes[STR_BUY]));
//        }
//        if ($reset) {
//            $this->reset();
//        }
//        return $volume;
//    }

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
        if ($reset) {
            $this->reset();
        }
        return $volume;
    }

    /**
     * @inheritDoc
     */
    protected function reset(): void
    {
        parent::reset();
        $this->volumes = [];
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

    /** @return array */
    public function getRawData(): array
    {
        return
            array_merge($this->volumes, parent::getRawData());
    }


//    protected function addAmount(Trade $amount): void
//    {
//        $this->volumes[$timestamp] = $this->volumesTotal[$timestamp] ?? 0;
//        $this->volumes[$timestamp] += $trade->amount;
//
//    }
}
