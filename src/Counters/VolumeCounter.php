<?php
/**
 * User: alec
 * Date: 19.11.18
 * Time: 17:27
 */
declare(strict_types=1);

namespace AlecRabbit\Counters;

use AlecRabbit\Structures\Trade;
use AlecRabbit\Structures\Volume;

class VolumeCounter extends TimedCounter
{
    /** @var array */
    protected $volumesTotal = [];
    /** @var array */
    protected $volumesBuy = [];
    /** @var array */
    protected $volumesSell = [];

    public function addTrade(Trade $trade): void
    {
        $time = $this->getTime($trade->timestamp);
        $this->volumesTotal[$time] = $this->volumesTotal[$time] ?? 0;
        $this->volumesTotal[$time] += $trade->amount;
        if ($trade->side === T_SELL) {
            $this->volumesSell[$time] = $this->volumesSell[$time] ?? 0;
            $this->volumesSell[$time] += $trade->amount;
        } else {
            $this->volumesBuy[$time] = $this->volumesBuy[$time] ?? 0;
            $this->volumesBuy[$time] += $trade->amount;
        }
        $this->trim();
    }

    private function trim(): void
    {
        $time = $this->relativeMode ? $this->lastTimestamp : time();
        $threshold = $time - $this->length;
        if (null !== ($key = array_key_first($this->volumesTotal)) && ($key <= $threshold)) {
            unset($this->volumesTotal[$key]);
        }
        if (null !== ($key = array_key_first($this->volumesSell)) && ($key <= $threshold)) {
            unset($this->volumesSell[$key]);
        }
        if (null !== ($key = array_key_first($this->volumesBuy)) && ($key <= $threshold)) {
            unset($this->volumesBuy[$key]);
        }
    }

    /**
     * @param bool|null $reset
     * @return Volume
     */
    public function getCalculatedVolumes(?bool $reset = null): Volume
    {
        $volume = new Volume();
        if (0 < ($sum = array_sum($this->volumesTotal))) {
            $volume
                ->setTotal($sum)
                ->setSell(array_sum($this->volumesSell))
                ->setBuy(array_sum($this->volumesBuy));
        }
        if ($reset) {
            $this->volumesTotal = [];
            $this->volumesSell = [];
            $this->volumesBuy = [];
        }
        return $volume;
    }

    /** @return array */
    public function getVolumes(): array
    {
        return
            [
                $this->volumesTotal,
                $this->volumesSell,
                $this->volumesBuy,
            ];
    }
}
