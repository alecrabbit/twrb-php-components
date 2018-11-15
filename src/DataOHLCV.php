<?php
/**
 * User: alec
 * Date: 31.10.18
 * Time: 15:20
 */
declare(strict_types=1);

namespace AlecRabbit;

use BCMathExtended\BC;

class DataOHLCV
{
    protected const DEFAULT_PERIOD_MULTIPLIER = 3;
    protected const MAX_PERIOD_MULTIPLIER = 100;
    protected const DEFAULT_SIZE = 500;
    protected const MAX_SIZE = 1440;
    protected const MIN_SIZE = 10;
    protected const RESOLUTIONS = RESOLUTIONS;

    /** @var array */
    protected $current;
    protected $timestamps = [];
    protected $opens = [];
    protected $highs = [];
    protected $lows = [];
    protected $closes = [];
    protected $volumes = [];
    protected $proxies = [];

    /** @var int */
    private $size;
    /** @var int */
    private $coefficient;
    /** @var string */
    private $pair;

    /**
     * DataOHLCV constructor.
     * @param string $pair
     * @param integer $size
     * @param int $coefficient
     */
    public function __construct(string $pair, ?int $size = null, int $coefficient = 1)
    {
        $this->size =
            (int)bounds(
                $size ?? static::DEFAULT_SIZE,
                static::MIN_SIZE,
                static::MAX_SIZE
            );
        $this->pair = $pair;
        $this->coefficient = $coefficient;
    }

    /**
     * @return int
     */
    public function getSize(): int
    {
        return $this->size;
    }

    public function hasPeriods(int $resolution, int $periods, int $multiplier = null): bool
    {
        $multiplier =
            (int)bounds($multiplier ?? self::DEFAULT_PERIOD_MULTIPLIER, 1, self::MAX_PERIOD_MULTIPLIER);

        return
            isset($this->timestamps[$resolution])
            && (\count($this->timestamps[$resolution]) >= ($periods * $multiplier));
    }

    public function addTrade(int $timestamp, string $side, float $price, float $amount): void
    {
        $this->addOHLCV($timestamp, $price, $price, $price, $price, $amount);
    }

    public function addOHLCV(
        int $timestamp,
        float $open,
        float $high,
        float $low,
        float $close,
        float $volume,
        int $resolution = RESOLUTION_01MIN
    ): void {
        $ts = base_timestamp($timestamp, $resolution);
        if (isset($this->current[$resolution])) {
            if ($ts > $this->current[$resolution]['timestamp']) {
                $this->updateLast($resolution);

                $this->setCurrent($resolution, $ts, $open, $high, $low, $close, $volume);
            } elseif ($ts === $this->current[$resolution]['timestamp']) {
                if ($high > $this->current[$resolution]['high']) {
                    $this->current[$resolution]['high'] = $high;
                }
                if ($low < $this->current[$resolution]['low']) {
                    $this->current[$resolution]['low'] = $low;
                }

                $this->current[$resolution]['close'] = $close;
                $this->current[$resolution]['volume'] =
                    (float)BC::add((string)$this->current[$resolution]['volume'], (string)$volume, NORMAL_SCALE);
            } elseif ($ts < $this->current[$resolution]['timestamp']) {
                throw new \RuntimeException(
                    'Incoming data are in unsorted order. Current timestamp is greater then incoming data\'s.' .
                    ' (' . $ts . ' < ' . $this->current[$resolution]['timestamp'] . ')'
                );
            }
        } else {
            $this->setCurrent($resolution, $ts, $open, $high, $low, $close, $volume);
        }

        $this->trim($resolution);
        if ($nextResolution = $this->nextResolution($resolution)) {
            $this->addOHLCV($timestamp, $open, $high, $low, $close, $volume, $nextResolution);
        }
    }

    private function trim(int $resolution): void
    {
        if (isset($this->timestamps[$resolution]) && (\count($this->timestamps[$resolution]) > $this->size)) {
            unset_first($this->timestamps[$resolution]);
            unset_first($this->opens[$resolution]);
            unset_first($this->highs[$resolution]);
            unset_first($this->lows[$resolution]);
            unset_first($this->closes[$resolution]);
            unset_first($this->volumes[$resolution]);
        }
    }

    private function nextResolution($resolution): ?int
    {
        $key = array_search($resolution, static::RESOLUTIONS, true);
        if ($key !== false && array_key_exists(++$key, static::RESOLUTIONS)) {
            return static::RESOLUTIONS[$key];
        }
        return null;
    }

    /**
     * @param int $resolution
     * @return array
     */
    public function getTimestamps(int $resolution): array
    {
        return
            $this->timestamps[$resolution] ?? [];
    }

    /**
     * @param int $resolution
     * @param bool $useCoefficient
     * @return array
     */
    public function getOpens(int $resolution, bool $useCoefficient = false): array
    {
        return
            $this->mulArr(
                $this->opens[$resolution] ?? [],
                $useCoefficient
            );
    }

    private function mulArr(array $values, bool $useCoefficient): array
    {
        if ($useCoefficient && $this->coefficient !== 1) {
            $values = array_map(
                function ($v) {
                    return
                        $v * $this->coefficient;
                },
                $values
            );
        }

        return $values;
    }

    /**
     * @param int $resolution
     * @param bool $useCoefficient
     * @return array
     */
    public function getHighs(int $resolution, bool $useCoefficient = false): array
    {
        return
            $this->mulArr(
                $this->highs[$resolution] ?? [],
                $useCoefficient
            );
    }

    /**
     * @param int $resolution
     * @param bool $useCoefficient
     * @return null|float
     */
    public function getLastHigh(int $resolution, bool $useCoefficient = false): ?float
    {
        return
            $this->lastElement($this->highs[$resolution] ?? [], $useCoefficient);
    }

    /**
     * @param array $element
     * @param bool $useCoefficient
     * @return null|float
     */
    private function lastElement(array $element, bool $useCoefficient = false): ?float
    {
        if (false !== $lastElement = end($element)) {
            return
                $this->mul(
                    $lastElement,
                    $useCoefficient
                );
        }
        return null;
    }

    private function mul(float $value, bool $useCoefficient): float
    {
        if ($useCoefficient && $this->coefficient !== 1) {
            $value *= $this->coefficient;
        }
        return $value;
    }

    /**
     * @param int $resolution
     * @param bool $useCoefficient
     * @return array
     */
    public function getLows(int $resolution, bool $useCoefficient = false): array
    {
        return
            $this->mulArr(
                $this->lows[$resolution] ?? [],
                $useCoefficient
            );
    }

    /**
     * @param int $resolution
     * @param bool $useCoefficient
     * @return null|float
     */
    public function getLastLow(int $resolution, bool $useCoefficient = false): ?float
    {
        return
            $this->lastElement($this->lows[$resolution] ?? [], $useCoefficient);
    }

    /**
     * @param int $resolution
     * @param bool $useCoefficient
     * @return array
     */
    public function getCloses(int $resolution, bool $useCoefficient = false): array
    {
        return
            $this->mulArr(
                $this->closes[$resolution] ?? [],
                $useCoefficient
            );
    }

    /**
     * @param int $resolution
     * @param bool $useCoefficient
     * @return null|float
     */
    public function getLastClose(int $resolution, bool $useCoefficient = false): ?float
    {
        return
            $this->lastElement($this->closes[$resolution] ?? [], $useCoefficient);
    }

    /**
     * @param int $resolution
     * @return array
     */
    public function getVolumes(int $resolution): array
    {
        return
            $this->volumes[$resolution] ?? [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function dump(): void
    {
        if (\defined('APP_DEBUG') && APP_DEBUG) {
            $result = [];
            $pair = $this->getPair();
            foreach (static::RESOLUTIONS as $resolution) {
                $count = \count($this->timestamps[$resolution] ?? []);
                $result[] =
                    sprintf(
                        '%s [%s] %s %s %s %s %s %s %s',
                        $this->current[$resolution]['timestamp'],
                        RESOLUTION_ALIASES[$resolution],
                        $count,
                        $pair,
                        $this->current[$resolution]['opens'],
                        $this->current[$resolution]['high'],
                        $this->current[$resolution]['low'],
                        $this->current[$resolution]['close'],
                        $this->current[$resolution]['volume']
                    );
            }
            dump($result);
        }
    }

    /**
     * @return string
     */
    public function getPair(): string
    {
        return $this->pair;
    }

    private function setCurrent(
        int $resolution,
        int $timestamp,
        float $open,
        float $high,
        float $low,
        float $close,
        float $volume
    ): void {
        $this->current[$resolution]['timestamp'] = $timestamp;
        $this->current[$resolution]['opens'] = $open;
        $this->current[$resolution]['high'] = $high;
        $this->current[$resolution]['low'] = $low;
        $this->current[$resolution]['close'] = $close;
        $this->current[$resolution]['volume'] = $volume;
    }

    private function updateLast(int $resolution): void
    {
        $this->timestamps[$resolution][] = $this->current[$resolution]['timestamp'];
        $this->opens[$resolution][] = $this->current[$resolution]['opens'];
        $this->highs[$resolution][] = $this->current[$resolution]['high'];
        $this->lows[$resolution][] = $this->current[$resolution]['low'];
        $this->closes[$resolution][] = $this->current[$resolution]['close'];
        $this->volumes[$resolution][] = $this->current[$resolution]['volume'];
    }
}
